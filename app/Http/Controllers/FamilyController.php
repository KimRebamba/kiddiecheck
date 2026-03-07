<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Student;
use App\Models\Test;
use App\Models\AssessmentPeriod;
use App\Models\PeriodSummaryScore;
use App\Services\EccdScoring;

class FamilyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ──────────────────────────────────────────────
    //  HELPERS
    // ──────────────────────────────────────────────

    private function getScaleVersionId()
    {
        return DB::table('scale_versions')
            ->where('name', 'ECCD 2004')
            ->value('scale_version_id');
    }

    private function getDomains($scaleVersionId)
    {
        return DB::table('domains as d')
            ->join('questions as q', 'q.domain_id', 'd.domain_id')
            ->where('q.scale_version_id', $scaleVersionId)
            ->select('d.domain_id', 'd.name as domain_name')
            ->distinct()
            ->orderBy('d.domain_id')
            ->get();
    }

    private function getDomainQuestions($domainId, $scaleVersionId)
    {
        return DB::table('questions')
            ->where('domain_id', $domainId)
            ->where('scale_version_id', $scaleVersionId)
            ->orderBy('order')
            ->get();
    }

    private function getAuthFamily()
    {
        $user   = Auth::user();
        $family = DB::table('families')->where('user_id', $user->user_id)->first();

        if (!$family) abort(404, 'Family profile not found');

        return [$user, $family];
    }

    private function calculateAge($birthDate)
    {
        $age = Carbon::parse($birthDate)->diff(Carbon::now());

        if ($age->y > 0) {
            $str = $age->y . ' year' . ($age->y > 1 ? 's' : '');
            if ($age->m > 0) $str .= ' and ' . $age->m . ' month' . ($age->m > 1 ? 's' : '');
            return $str;
        }

        return $age->m . ' month' . ($age->m > 1 ? 's' : '');
    }

    private function getInterpretation($score)
    {
        return match(true) {
            $score >= 130 => 'Very Superior',
            $score >= 120 => 'Superior',
            $score >= 110 => 'High Average',
            $score >= 90  => 'Average',
            $score >= 80  => 'Low Average',
            $score >= 70  => 'Borderline',
            default       => 'Extremely Low',
        };
    }

    private function calculatePercentage($score)
    {
        $percentage = (($score - 40) / (160 - 40)) * 100;
        return round(max(0, min(100, $percentage)));
    }

    private function verifyTestOwnership($testId)
    {
        $test = DB::table('tests as t')
            ->join('students as s', 's.student_id', 't.student_id')
            ->where('t.test_id', $testId)
            ->where('t.examiner_id', Auth::id())
            ->select('t.*', 's.first_name', 's.last_name')
            ->first();

        if (!$test) abort(403, 'Unauthorized');

        return $test;
    }

    private function updateTestStatus($testId, $status)
    {
        DB::table('tests')
            ->where('test_id', $testId)
            ->where('examiner_id', Auth::id())
            ->update(['status' => $status, 'updated_at' => now()]);
    }

    private function prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId)
    {
        $prevIndex  = $questionIndex - 1;
        $prevDomain = $domainNumber;

        if ($prevIndex < 1) {
            $prevDomain = $domainNumber - 1;
            if ($prevDomain >= 1) {
                $prevIndex = DB::table('questions')
                    ->where('domain_id', $domains[$prevDomain - 1]->domain_id)
                    ->where('scale_version_id', $scaleVersionId)
                    ->count();
            } else {
                return [null, null];
            }
        }

        return [$prevDomain, $prevIndex];
    }

    private function nextNav($domainNumber, $questionIndex, $totalInDomain, $totalDomains)
    {
        $nextIndex  = $questionIndex + 1;
        $nextDomain = $domainNumber;

        if ($nextIndex > $totalInDomain) {
            $nextDomain = $domainNumber + 1;
            $nextIndex  = 1;
            if ($nextDomain > $totalDomains) {
                return [null, null];
            }
        }

        return [$nextDomain, $nextIndex];
    }

    // ──────────────────────────────────────────────
    //  DASHBOARD
    // ──────────────────────────────────────────────
    // Replace the index() method in FamilyController with this version:

    // Replace the index() method in FamilyController with this version:

    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'family') {
            abort(403, 'Unauthorized access');
        }

        $family = DB::table('families as f')
            ->where('f.user_id', $user->user_id)
            ->first();

        if (!$family) {
            abort(404, 'Family profile not found');
        }

        $students = DB::table('students as s')
            ->where('s.family_id', $family->user_id)
            ->orderBy('s.date_of_birth', 'desc')
            ->get();

        $scaleVersionId = $this->getScaleVersionId();
        $totalQuestions  = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        $children   = [];
        $studentIds = [];

        foreach ($students as $s) {
            $studentIds[] = $s->student_id;

            // Look for an active (current) assessment period for this child
            $activePeriod = DB::table('assessment_periods')
                ->where('student_id', $s->student_id)
                ->where('status', '!=', 'completed')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            $answered        = 0;
            $periodCompleted = false;
            $needsAction     = false;

            if ($activePeriod) {
                // Only consider this family's test in the current period
                $familyTest = DB::table('tests')
                    ->where('student_id', $s->student_id)
                    ->where('examiner_id', $user->user_id)
                    ->where('period_id', $activePeriod->period_id)
                    ->whereIn('status', ['in_progress', 'completed', 'finalized'])
                    ->orderBy('test_date', 'desc')
                    ->first();

                if ($familyTest) {
                    $answered = DB::table('test_responses')
                        ->where('test_id', $familyTest->test_id)
                        ->count();

                    $periodCompleted = in_array($familyTest->status, ['completed', 'finalized']);

                    if ($familyTest->status === 'in_progress' && $answered < $totalQuestions) {
                        $needsAction = true;
                    }
                } else {
                    // Assessment window is active but the family has not started the test yet
                    $needsAction = true;
                }
            }

            $children[] = [
                'student_id'         => $s->student_id,
                'name'               => $s->first_name . ' ' . $s->last_name,
                'first_name'         => $s->first_name,
                'age'                => $this->calculateAge($s->date_of_birth),
                'profile_image'      => $s->feature_path,
                'total_tests'        => $totalQuestions,
                'completed'          => $answered,
                'active_period'     => $activePeriod,
                'period_completed'  => $periodCompleted,
                'needs_action'       => $needsAction,
            ];
        }

        // Upcoming/active assessment windows for these children
        $assessments = collect();
        $testsByPeriod = [];

        if (!empty($studentIds)) {
            $assessments = DB::table('assessment_periods as ap')
                ->join('students as s', 's.student_id', '=', 'ap.student_id')
                ->whereIn('ap.student_id', $studentIds)
                ->where('ap.end_date', '>=', now())
                ->orderBy('ap.start_date')
                ->limit(5)
                ->select('ap.period_id', 'ap.student_id', 'ap.start_date', 'ap.end_date', 'ap.status',
                         's.first_name', 's.last_name')
                ->get();

            $periodIds = $assessments->pluck('period_id')->all();

            if (!empty($periodIds)) {
                $familyTests = DB::table('tests')
                    ->whereIn('period_id', $periodIds)
                    ->where('examiner_id', $user->user_id)
                    ->whereIn('status', ['in_progress', 'completed', 'finalized'])
                    ->orderBy('test_date', 'desc')
                    ->get(['test_id', 'period_id', 'student_id', 'status', 'test_date']);

                foreach ($familyTests as $t) {
                    // First (latest) per period wins
                    if (!isset($testsByPeriod[$t->period_id])) {
                        $testsByPeriod[$t->period_id] = $t;
                    }
                }

                foreach ($assessments as $ap) {
                    $ap->family_test = $testsByPeriod[$ap->period_id] ?? null;
                }

                // For the family dashboard, hide assessment periods that this
                // family has already finished (completed/finalized). This keeps
                // the "Upcoming Assessments" card from listing periods that are
                // fully done from the family's point of view.
                $assessments = $assessments->filter(function ($ap) {
                    $familyTest = $ap->family_test ?? null;
                    return !($familyTest && in_array($familyTest->status, ['completed', 'finalized']));
                })->values();
            }
        }

        // Latest family-side results (multiple tests per child, newest first)
        $rawResults = collect();
        if (!empty($studentIds)) {
            $rawResults = DB::table('tests as t')
                ->join('test_standard_scores as ss', 'ss.test_id', '=', 't.test_id')
                ->join('students as s', 's.student_id', '=', 't.student_id')
                ->whereIn('t.student_id', $studentIds)
                ->where('t.examiner_id', $user->user_id)
                ->whereIn('t.status', ['completed', 'finalized'])
                ->orderBy('t.test_date', 'desc')
                ->limit(20)
                ->select('t.test_id', 't.student_id', 't.test_date', 'ss.standard_score', 'ss.interpretation',
                         's.first_name', 's.last_name', 's.feature_path')
                ->get();
        }

        $latestResults = [];
        foreach ($rawResults as $r) {
            $latestResults[] = [
                'test_id'        => $r->test_id,
                'child_name'     => $r->first_name . ' ' . $r->last_name,
                'score'          => $r->standard_score,
                'interpretation' => $r->interpretation,
                'date'           => $r->test_date,
                'profile_image'  => $r->feature_path,
            ];
        }

        // Period-level monitoring data per child (only periods where a family
        // score exists, to stay aligned with admin "missing family eval" logic).
        $monitoring = [];
        if (!empty($studentIds)) {
            $periodSummaries = PeriodSummaryScore::query()
                ->join('assessment_periods as ap', 'ap.period_id', '=', 'period_summary_scores.period_id')
                ->join('students as s', 's.student_id', '=', 'ap.student_id')
                ->whereIn('ap.student_id', $studentIds)
                ->whereIn('ap.status', ['completed'])
                ->whereNotNull('period_summary_scores.family_standard_score')
                ->orderBy('ap.student_id')
                ->orderBy('ap.start_date')
                ->get([
                    'ap.student_id',
                    'ap.description',
                    'ap.start_date',
                    'ap.end_date',
                    'period_summary_scores.final_standard_score',
                    'period_summary_scores.final_interpretation',
                ]);

            foreach ($periodSummaries->groupBy('student_id') as $studentId => $rows) {
                $monitoring[$studentId] = $rows->map(function ($row) {
                    return [
                        'label'          => $row->description,
                        'start_date'     => $row->start_date,
                        'end_date'       => $row->end_date,
                        'score'          => $row->final_standard_score,
                        'interpretation' => $row->final_interpretation,
                    ];
                })->values()->all();
            }
        }

        return view('family.index', [
            'family_name'    => $family->family_name ?? 'Family',
            'children'       => $children,
            'assessments'    => $assessments,
            'latest_results' => $latestResults,
            'monitoring'     => $monitoring,
        ]);
    }

    // ──────────────────────────────────────────────
    //  PROFILE IMAGE
    // ──────────────────────────────────────────────

    public function updateProfileImage(Request $request, $studentId)
    {
        [$user, $family] = $this->getAuthFamily();

        $student = DB::table('students')
            ->where('student_id', $studentId)
            ->where('family_id', $family->user_id)
            ->first();

        if (!$student) abort(404);

        $request->validate([
            'selected_avatar' => 'nullable|string',
            'profile_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $newImage = $student->feature_path;

        if ($request->filled('selected_avatar')) {
            $newImage = $request->selected_avatar;
        }

        if ($request->hasFile('profile_image')) {
            if ($student->feature_path && str_starts_with($student->feature_path, 'profiles/')) {
                Storage::disk('public')->delete($student->feature_path);
            }
            $newImage = $request->file('profile_image')->store('profiles', 'public');
        }

        DB::table('students')
            ->where('student_id', $studentId)
            ->update(['feature_path' => $newImage, 'updated_at' => now()]);

        return redirect()->route('family.index')
            ->with('success', 'Profile image updated successfully!');
    }

    public function showStudentProfile($studentId)
    {
        [$user, $family] = $this->getAuthFamily();

        $student = Student::with(['section', 'family'])
            ->where('student_id', $studentId)
            ->where('family_id', $family->user_id)
            ->first();

        if (!$student) abort(404);

        return view('family.student-profile', compact('student'));
    }

    public function familyProfile()
    {
        [$user, $family] = $this->getAuthFamily();
        return view('family.family-profile', compact('family'));
    }

    // ──────────────────────────────────────────────
    //  AJAX ENDPOINTS
    // ──────────────────────────────────────────────

    public function getChildDetails($studentId)
    {
        [$user, $family] = $this->getAuthFamily();

        $student = DB::table('students')
            ->where('student_id', $studentId)
            ->where('family_id', $family->user_id)
            ->select('student_id', 'first_name', 'last_name', 'date_of_birth')
            ->first();

        if (!$student) abort(404);

        $tests = DB::table('tests as t')
            ->leftJoin('test_standard_scores as ss', 'ss.test_id', 't.test_id')
            ->where('t.student_id', $studentId)
            ->where('t.examiner_id', $user->user_id)
            ->whereIn('t.status', ['completed', 'finalized'])
            ->orderBy('t.test_date', 'desc')
            ->select('t.test_id', 't.test_date', 't.status', 't.notes', 'ss.standard_score', 'ss.interpretation')
            ->get();

        return response()->json([
            'student' => $student,
            'tests'   => $tests,
        ]);
    }

    public function getUpcomingAssessments()
    {
        [$user, $family] = $this->getAuthFamily();

        $studentIds = DB::table('students')
            ->where('family_id', $family->user_id)
            ->pluck('student_id');

        $assessments = DB::table('assessment_periods as ap')
            ->join('students as s', 's.student_id', 'ap.student_id')
            ->whereIn('ap.student_id', $studentIds)
            ->where('ap.end_date', '>=', now())
            ->orderBy('ap.start_date')
            ->select('ap.period_id', 'ap.student_id', 'ap.start_date', 'ap.end_date', 'ap.status',
                     's.first_name', 's.last_name')
            ->get();

        // Attach this family's test per period and hide periods where their
        // test is already completed/finalized, to stay consistent with the
        // main dashboard's Upcoming Assessments card.
        $periodIds    = $assessments->pluck('period_id')->all();
        $testsByPeriod = [];

        if (!empty($periodIds)) {
            $familyTests = DB::table('tests')
                ->whereIn('period_id', $periodIds)
                ->where('examiner_id', $user->user_id)
                ->whereIn('status', ['in_progress', 'completed', 'finalized'])
                ->orderBy('test_date', 'desc')
                ->get(['test_id', 'period_id', 'student_id', 'status', 'test_date']);

            foreach ($familyTests as $t) {
                if (!isset($testsByPeriod[$t->period_id])) {
                    $testsByPeriod[$t->period_id] = $t;
                }
            }

            foreach ($assessments as $ap) {
                $ap->family_test = $testsByPeriod[$ap->period_id] ?? null;
            }

            $assessments = $assessments->filter(function ($ap) {
                $familyTest = $ap->family_test ?? null;
                return !($familyTest && in_array($familyTest->status, ['completed', 'finalized']));
            })->values();
        }

        return response()->json($assessments);
    }

    public function resultsIndex()
    {
    [$user, $family] = $this->getAuthFamily();
    return view('family.results-history', compact('family'));
    }

    // ──────────────────────────────────────────────
    //  TEST FLOW
    // ──────────────────────────────────────────────
    public function testsIndex()
    {
        return view('family.tests');
    }

    public function showStartTest($studentId)
    {
        $user = Auth::user();

        $student = DB::table('students')
            ->where('student_id', $studentId)
            ->where('family_id', $user->user_id)
            ->first();

        if (!$student) {
            return redirect()->route('family.index')
                ->with('error', 'Student not found.');
        }

        $period = DB::table('assessment_periods')
            ->where('student_id', $studentId)
            ->where('status', '!=', 'completed')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$period) {
            return redirect()->route('family.index')
                ->with('error', 'No active assessment period found.');
        }

        $scaleVersionId = $this->getScaleVersionId();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        // Look for this family's test in the current period
        $existingTest = DB::table('tests')
            ->where('student_id', $studentId)
            ->where('examiner_id', $user->user_id)
            ->where('period_id', $period->period_id)
            ->whereIn('status', ['in_progress', 'completed', 'finalized'])
            ->orderBy('test_date', 'desc')
            ->first();

        if ($existingTest && in_array($existingTest->status, ['completed', 'finalized'], true)) {
            // Family test for this period is already done – send them to the result
            return redirect()->route('family.tests.result', $existingTest->test_id);
        }

        $answeredCount = 0;

        if ($existingTest && $existingTest->status === 'in_progress') {
            $answeredCount = DB::table('test_responses')
                ->where('test_id', $existingTest->test_id)
                ->count();

            // If all questions are already answered but status is still in_progress,
            // align it with the completed state and treat it as done.
            if ($answeredCount >= $totalQuestions) {
                DB::table('tests')
                    ->where('test_id', $existingTest->test_id)
                    ->update(['status' => 'completed', 'updated_at' => now()]);

                return redirect()->route('family.tests.result', $existingTest->test_id);
            }
        }

        return view('family.start-test', compact(
            'student', 'period', 'existingTest', 'answeredCount', 'totalQuestions'
        ));
    }

   public function showQuestion($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);

        if (in_array($test->status, ['finalized', 'canceled', 'terminated'])) {
            return redirect()->route('family.index')
                ->with('error', 'This test is ' . $test->status . '.');
        }

        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);

        if ($domainNumber < 1 || $domainNumber > count($domains)) abort(404);

        $currentDomain = $domains[$domainNumber - 1];
        $questions     = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);

        if ($questionIndex < 1 || $questionIndex > count($questions)) abort(404);

        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses')
            ->where('test_id', $testId)
            ->where('question_id', $question->question_id)
            ->value('response');

        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        $questionText = $question->display_text ?? $question->text;

                // Redirect: follow instructions game
        if ($currentDomain->domain_name === 'Receptive Language' && (int)$questionIndex === 3) {
            return redirect()->route('family.tests.follow.instructions.game', [
                'test'   => $testId,
                'domain' => $domainNumber,
                'index'  => $questionIndex,
            ]);
        }

                // Redirect: point to objects game
        if ($currentDomain->domain_name === 'Receptive Language' && (int)$questionIndex === 5) {
            return redirect()->route('family.tests.point.objects.game', [
                'test'   => $testId,
                'domain' => $domainNumber,
                'index'  => $questionIndex,
            ]);
        }

        // Redirect: matching objects game
        if (str_contains(strtolower($questionText), 'when looking at pictures, can your child name what they see')) {
            return redirect()->route('family.tests.name.game', [
                'test'   => $testId,
                'domain' => $domainNumber,
                'index'  => $questionIndex,
            ]);
        }

        // Redirect: color matching game
        if (str_contains(strtolower($questionText), 'match objects of the same color')) {
            return redirect()->route('family.tests.color.game', [
                'test'   => $testId,
                'domain' => $domainNumber,
                'index'  => $questionIndex,
            ]);
        }

        // Redirect: picture matching game
        if (str_contains(strtolower($questionText), 'match two pictures that are the same')) {
            return redirect()->route('family.tests.picture.game', [
                'test'   => $testId,
                'domain' => $domainNumber,
                'index'  => $questionIndex,
            ]);
        }

        if (str_contains(strtolower($questionText), 'sort or put things together based on their shape')) {
        return redirect()->route('family.tests.shape.game', [
        'test'   => $testId,
        'domain' => $domainNumber,
        'index'  => $questionIndex,
        ]);
        }

        if (str_contains(strtolower($questionText), 'sort objects using both color and size')) {
        return redirect()->route('family.tests.size.color.game', [
        'test'   => $testId,
        'domain' => $domainNumber,
        'index'  => $questionIndex,
            ]);
        }

        if (str_contains(strtolower($questionText), 'line up objects from smallest to biggest')) {
        return redirect()->route('family.tests.size.order.game', [
        'test'   => $testId,
        'domain' => $domainNumber,
        'index'  => $questionIndex,
            ]);
        }

                // Redirect: matches objects game (Cognitive Q7)
        if ($currentDomain->domain_name === 'Cognitive' && (int)$questionIndex === 7) {
            return redirect()->route('family.tests.game', [
                'test'   => $testId,
                'domain' => $domainNumber,
                'index'  => $questionIndex,
            ]);
        }

                // Redirect: puzzle game
        if ($currentDomain->domain_name === 'Cognitive' && (int)$questionIndex === 12) {
            return redirect()->route('family.tests.puzzle.game', [
                'test'   => $testId,
                'domain' => $domainNumber,
                'index'  => $questionIndex,
            ]);
        }

        if ($currentDomain->domain_name === 'Cognitive' && (int)$questionIndex === 15) {
            return redirect()->route('family.tests.color.name.game', [
                'test'   => $testId,
                'domain' => $domainNumber,
                'index'  => $questionIndex,
            ]);
        }

        if ($currentDomain->domain_name === 'Cognitive' && (int)$questionIndex === 16) {
            return redirect()->route('family.tests.animal.veggie.game', [
                'test'   => $testId,
                'domain' => $domainNumber,
                'index'  => $questionIndex,
            ]);
        }

                // Redirect: what's wrong game
        if ($currentDomain->domain_name === 'Cognitive' && (int)$questionIndex === 20) {
            return redirect()->route('family.tests.whats.wrong.game', [
                'test'   => $testId,
                'domain' => $domainNumber,
                'index'  => $questionIndex,
            ]);
        }

                // Redirect: uppercase/lowercase letter match game
        if ($currentDomain->domain_name === 'Cognitive' && (int)$questionIndex === 21) {
            return redirect()->route('family.tests.letter.match.game', [
                'test'   => $testId,
                'domain' => $domainNumber,
                'index'  => $questionIndex,
            ]);
        }

        if ($currentDomain->domain_name === 'Social-Emotional' && (int)$questionIndex === 11) {
            return redirect()->route('family.tests.feelings.game', [
                'test'   => $testId,
                'domain' => $domainNumber,
                'index'  => $questionIndex,
            ]);
        }

        if (str_contains(strtolower($questionText), 'name objects in pictures')) {
        return redirect()->route('family.tests.name.game', [
        'test'   => $testId,
        'domain' => $domainNumber,
        'index'  => $questionIndex,
            ]);
        }

        $totalDomainQuestions = count($questions);

        return view('family.question', compact(
        'test', 'testId', 'currentDomain', 'question', 'questionText',
        'domainNumber', 'questionIndex', 'existingResponse',
        'totalAnswered', 'totalQuestions', 'totalDomainQuestions',
        'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex', 'domains'
    ));
    }

    public function startTest($studentId)
    {
        $user = Auth::user();

        if ($user->role !== 'family') {
            abort(403, 'Unauthorized access');
        }

        // Verify student belongs to this family
        $student = DB::table('students')
            ->where('student_id', $studentId)
            ->where('family_id', $user->user_id)
            ->first();

        if (!$student) {
            return redirect()->route('family.index')
                ->with('error', 'Student not found or does not belong to your family.');
        }

        // Find an active assessment period for this student
        $period = DB::table('assessment_periods')
            ->where('student_id', $studentId)
            ->where('status', '!=', 'completed')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$period) {
            return redirect()->route('family.index')
                ->with('error', 'No active assessment period found for this student.');
        }

        // Check if there's already a family test for this period
        $existingTest = DB::table('tests')
            ->where('student_id', $studentId)
            ->where('examiner_id', $user->user_id)
            ->where('period_id', $period->period_id)
            ->whereIn('status', ['in_progress', 'completed', 'finalized'])
            ->orderBy('test_date', 'desc')
            ->first();

        if ($existingTest) {
            if ($existingTest->status === 'in_progress') {
                return redirect()->route('family.tests.question', [
                    'test'   => $existingTest->test_id,
                    'domain' => 1,
                    'index'  => 1,
                ]);
            }

            // Already completed/finalized – go to result instead of creating a new test
            return redirect()->route('family.tests.result', $existingTest->test_id);
        }

        // Create a new test for this family and period
        $testId = DB::table('tests')->insertGetId([
            'period_id'    => $period->period_id,
            'student_id'   => $studentId,
            'examiner_id'  => $user->user_id,
            'test_date'    => now(),
            'notes'        => null,
            'status'       => 'in_progress',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Redirect to first domain, first question
        return redirect()->route('family.tests.question', [
            'test'   => $testId,
            'domain' => 1,
            'index'  => 1,
        ]);
    }

    public function showGame($testId, $domainNumber, $questionIndex)
    {
    $test           = $this->verifyTestOwnership($testId);
    $scaleVersionId = $this->getScaleVersionId();
    $domains        = $this->getDomains($scaleVersionId);
    $currentDomain  = $domains[$domainNumber - 1];
    $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
    $question       = $questions[$questionIndex - 1];

    $existingResponse = DB::table('test_responses as tr')
        ->where('tr.test_id', $testId)
        ->where('tr.question_id', $question->question_id)
        ->value('tr.response');

    $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
    $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

    [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
    [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

    return view('family.matching-game', compact(
        'test', 'testId', 'currentDomain', 'question',
        'domainNumber', 'questionIndex', 'existingResponse',
        'totalAnswered', 'totalQuestions',
        'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
    ));
}

    public function showFollowInstructionsGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses')
            ->where('test_id', $testId)
            ->where('question_id', $question->question_id)
            ->value('response');

        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('family.RL-3-One-step', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }
    public function showPointObjectsGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses')
            ->where('test_id', $testId)
            ->where('question_id', $question->question_id)
            ->value('response');

        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('family.RL-5-Points-Objects', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showColorGame($testId, $domainNumber, $questionIndex)
    {
    $test           = $this->verifyTestOwnership($testId);
    $scaleVersionId = $this->getScaleVersionId();
    $domains        = $this->getDomains($scaleVersionId);
    $currentDomain  = $domains[$domainNumber - 1];
    $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
    $question       = $questions[$questionIndex - 1];

    $existingResponse = DB::table('test_responses as tr')
        ->where('tr.test_id', $testId)
        ->where('tr.question_id', $question->question_id)
        ->value('tr.response');

    $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
    $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

    [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
    [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

    return view('family.color-matching-game', compact(
        'test', 'testId', 'currentDomain', 'question',
        'domainNumber', 'questionIndex', 'existingResponse',
        'totalAnswered', 'totalQuestions',
        'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
    ));
    }

        public function showAnimalVeggieGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses')
            ->where('test_id', $testId)
            ->where('question_id', $question->question_id)
            ->value('response');

        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('family.C-16-Name-Animal-Vegetable', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

        public function showWhatsWrongGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses')
            ->where('test_id', $testId)
            ->where('question_id', $question->question_id)
            ->value('response');

        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('family.C-20-Whats-wrong-pic', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

        public function showLetterMatchGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses')
            ->where('test_id', $testId)
            ->where('question_id', $question->question_id)
            ->value('response');

        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('family.C-21-UpperLower-Letters', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

        public function showPuzzleGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses')
            ->where('test_id', $testId)
            ->where('question_id', $question->question_id)
            ->value('response');

        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('family.C-12-Puzzle', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showPictureGame($testId, $domainNumber, $questionIndex)
    {
    $test           = $this->verifyTestOwnership($testId);
    $scaleVersionId = $this->getScaleVersionId();
    $domains        = $this->getDomains($scaleVersionId);
    $currentDomain  = $domains[$domainNumber - 1];
    $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
    $question       = $questions[$questionIndex - 1];

    $existingResponse = DB::table('test_responses')
        ->where('test_id', $testId)
        ->where('question_id', $question->question_id)
        ->value('response');

    $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
    $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

    [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
    [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

    return view('family.picture-matching-game', compact(
        'test', 'testId', 'currentDomain', 'question',
        'domainNumber', 'questionIndex', 'existingResponse',
        'totalAnswered', 'totalQuestions',
        'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
    ));
    }

    public function showShapeGame($testId, $domainNumber, $questionIndex)
    {
    $test           = $this->verifyTestOwnership($testId);
    $scaleVersionId = $this->getScaleVersionId();
    $domains        = $this->getDomains($scaleVersionId);
    $currentDomain  = $domains[$domainNumber - 1];
    $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
    $question       = $questions[$questionIndex - 1];

    $existingResponse = DB::table('test_responses')
        ->where('test_id', $testId)
        ->where('question_id', $question->question_id)
        ->value('response');

    $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
    $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

    [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
    [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

    return view('family.shape-sorting-game', compact(
        'test', 'testId', 'currentDomain', 'question',
        'domainNumber', 'questionIndex', 'existingResponse',
        'totalAnswered', 'totalQuestions',
        'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
    ));
    }

    public function showSizeColorGame($testId, $domainNumber, $questionIndex)
    {
    $test           = $this->verifyTestOwnership($testId);
    $scaleVersionId = $this->getScaleVersionId();
    $domains        = $this->getDomains($scaleVersionId);
    $currentDomain  = $domains[$domainNumber - 1];
    $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
    $question       = $questions[$questionIndex - 1];

    $existingResponse = DB::table('test_responses')
        ->where('test_id', $testId)
        ->where('question_id', $question->question_id)
        ->value('response');

    $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
    $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

    [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
    [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

    return view('family.size-color-sorting-game', compact(
        'test', 'testId', 'currentDomain', 'question',
        'domainNumber', 'questionIndex', 'existingResponse',
        'totalAnswered', 'totalQuestions',
        'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
    ));
    }

    public function showSizeOrderGame($testId, $domainNumber, $questionIndex)
    {
    $test           = $this->verifyTestOwnership($testId);
    $scaleVersionId = $this->getScaleVersionId();
    $domains        = $this->getDomains($scaleVersionId);
    $currentDomain  = $domains[$domainNumber - 1];
    $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
    $question       = $questions[$questionIndex - 1];

    $existingResponse = DB::table('test_responses')
        ->where('test_id', $testId)
        ->where('question_id', $question->question_id)
        ->value('response');

    $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
    $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

    [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
    [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

    return view('family.size-ordering-game', compact(
        'test', 'testId', 'currentDomain', 'question',
        'domainNumber', 'questionIndex', 'existingResponse',
        'totalAnswered', 'totalQuestions',
        'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
    ));
    }

    public function showNameGame($testId, $domainNumber, $questionIndex)
    {
    $test           = $this->verifyTestOwnership($testId);
    $scaleVersionId = $this->getScaleVersionId();
    $domains        = $this->getDomains($scaleVersionId);
    $currentDomain  = $domains[$domainNumber - 1];
    $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
    $question       = $questions[$questionIndex - 1];

    $existingResponse = DB::table('test_responses')
        ->where('test_id', $testId)
        ->where('question_id', $question->question_id)
        ->value('response');

    $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
    $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

    [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
    [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

    return view('family.EL-8-Name-Objects', compact(
        'test', 'testId', 'currentDomain', 'question',
        'domainNumber', 'questionIndex', 'existingResponse',
        'totalAnswered', 'totalQuestions',
        'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
    ));
    }

    public function showColorNameGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses')
            ->where('test_id', $testId)
            ->where('question_id', $question->question_id)
            ->value('response');

        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('family.C-15-Name-that-color', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

        public function showFeelingsGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses')
            ->where('test_id', $testId)
            ->where('question_id', $question->question_id)
            ->value('response');

        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('family.SE011-Feeling-inOthers', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function submitQuestion(Request $request, $testId, $domainNumber, $questionIndex)
    {
        $request->validate(['response' => 'required|in:yes,no']);

        $test = $this->verifyTestOwnership($testId);

        if (in_array($test->status, ['completed', 'finalized', 'canceled', 'terminated'], true)) {
            return redirect()->route('family.index')->with('error', 'This test cannot be modified.');
        }

        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];

        $questionIds = DB::table('questions')
            ->where('domain_id', $currentDomain->domain_id)
            ->where('scale_version_id', $scaleVersionId)
            ->orderBy('order')
            ->pluck('question_id')
            ->toArray();

        $questionId = $questionIds[$questionIndex - 1];

        $exists = DB::table('test_responses')
            ->where('test_id', $testId)
            ->where('question_id', $questionId)
            ->exists();

        if ($exists) {
            DB::table('test_responses')
                ->where('test_id', $testId)
                ->where('question_id', $questionId)
                ->update(['response' => $request->response]);
        } else {
            DB::table('test_responses')->insert([
                'test_id'     => $testId,
                'question_id' => $questionId,
                'response'    => $request->response,
                'is_assumed'  => false,
            ]);
        }

        DB::table('tests')
            ->where('test_id', $testId)
            ->where('examiner_id', Auth::id())
            ->update(['status' => 'in_progress', 'updated_at' => now()]);

        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questionIds), count($domains));

        if (is_null($nextDomain)) {
            return redirect()->route('family.tests.result', $testId);
        }

        return redirect()->route('family.tests.question', [
            'test'   => $testId,
            'domain' => $nextDomain,
            'index'  => $nextIndex,
        ]);
    }

    public function result($testId)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();

        $allQuestions = DB::table('questions as q')
            ->join('domains as d', 'd.domain_id', 'q.domain_id')
            ->where('q.scale_version_id', $scaleVersionId)
            ->orderBy('d.domain_id')
            ->orderBy('q.order')
            ->select('q.question_id', 'd.domain_id', 'd.name as domain_name')
            ->get();

        $totalQuestions = count($allQuestions);

        $answeredIds = DB::table('test_responses')
            ->where('test_id', $testId)
            ->pluck('question_id')
            ->toArray();

        $totalAnswered = count($answeredIds);
        $domainStats   = [];
        $domainNumber  = 1;

        foreach ($allQuestions->groupBy('domain_id') as $domainId => $questions) {
            $domainQIds     = $questions->pluck('question_id')->toArray();
            $domainAnswered = count(array_intersect($domainQIds, $answeredIds));

            $yesCount = DB::table('test_responses')
                ->where('test_id', $testId)
                ->whereIn('question_id', $domainQIds)
                ->where('response', 'yes')
                ->count();

            $firstUnansweredIndex = 1;
            foreach ($questions as $idx => $q) {
                if (!in_array($q->question_id, $answeredIds)) {
                    $firstUnansweredIndex = $idx + 1;
                    break;
                }
            }

            $domainStats[] = [
                'domain_number'          => $domainNumber++,
                'domain_name'            => $questions->first()->domain_name,
                'total'                  => count($domainQIds),
                'answered'               => $domainAnswered,
                'yes_count'              => $yesCount,
                'is_complete'            => $domainAnswered === count($domainQIds),
                'first_unanswered_index' => $firstUnansweredIndex,
            ];
        }

        $allAnswered = $totalAnswered === $totalQuestions;

        return view('family.result', compact(
            'test', 'testId', 'domainStats', 'totalQuestions', 'totalAnswered', 'allAnswered'
        ));
    }

    public function finalize($testId)
    {
        $test = $this->verifyTestOwnership($testId);

        // Do not allow re-finalizing or changing non-active tests.
        if (in_array($test->status, ['completed', 'finalized', 'canceled', 'terminated'], true)) {
            return redirect()->route('family.tests.result', $testId)
                ->with('info', 'This test has already been submitted.');
        }

        $scaleVersionId = $this->getScaleVersionId();

        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();

        if ($totalAnswered < $totalQuestions) {
            $remaining = $totalQuestions - $totalAnswered;
            return redirect()->route('family.tests.result', $testId)
                ->with('error', $remaining . ' question(s) remaining. Please complete all before submitting.');
        }

        DB::table('tests')
            ->where('test_id', $testId)
            ->where('examiner_id', Auth::id())
            ->update(['status' => 'completed', 'updated_at' => now()]);

        // Compute scores and update period summary so admin/family see consistent data.
        $freshTest = Test::with('assessmentPeriod', 'student')->find($testId);
        if ($freshTest) {
            app(EccdScoring::class)->scoreTestAndRecompute($freshTest);
        }

        return redirect()->route('family.index')
            ->with('success', 'Test completed successfully!');
    }

    // ──────────────────────────────────────────────
    //  PLACEHOLDER ROUTES
    // ──────────────────────────────────────────────

    public function child($studentId)
    {
        return redirect()->route('family.index');
    }

    public function markIncomplete($testId)
    {
        $test = $this->verifyTestOwnership($testId);

        // Only allow moving back to in_progress from in_progress or completed,
        // mirroring the teacher-side rules and keeping admin views consistent.
        if (!in_array($test->status, ['in_progress', 'completed'], true)) {
            return redirect()->route('family.tests.result', $testId)
                ->with('error', 'This test cannot be marked incomplete.');
        }

        $this->updateTestStatus($testId, 'in_progress');

        return redirect()->route('family.tests.result', $testId)
            ->with('info', 'Test marked as incomplete. You can continue answering later.');
    }

    public function pause($testId)
    {
        // Keep status as-is, just update timestamp so activity is reflected.
        DB::table('tests')
            ->where('test_id', $testId)
            ->where('examiner_id', Auth::id())
            ->update(['updated_at' => now()]);

        return redirect()->route('family.index')
            ->with('success', 'Test paused. You can resume it later.');
    }

    public function cancel($testId)
    {
        $this->updateTestStatus($testId, 'canceled');
        return redirect()->route('family.index')->with('success', 'Test canceled.');
    }

    public function terminate($testId)
    {
        $this->updateTestStatus($testId, 'terminated');
        return redirect()->route('family.index')->with('success', 'Test terminated.');
    }

        public function help()
    {
        return view('family.help');
    }
}