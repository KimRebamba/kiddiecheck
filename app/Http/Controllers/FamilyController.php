<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
    //  DOMAIN-PER-MONTH MAPPING
    //  Month 1 → Domain 1, Month 2 → Domain 2, etc.
    //  Assessment period start_date determines Month 1.
    // ──────────────────────────────────────────────

    /**
     * Return the domain number (1–6) that is allowed this calendar month
     * relative to the period's start_date.
     * Returns null if the period hasn't started or has exceeded 6 months.
     */
    private function getAllowedDomainForPeriod($period): ?int
    {
        $start      = Carbon::parse($period->start_date)->startOfMonth();
        $now        = Carbon::now()->startOfMonth();
        $monthIndex = $start->diffInMonths($now); // 0-based

        // Only months 0–5 are valid (6 domains)
        if ($monthIndex < 0 || $monthIndex > 5) {
            return null;
        }

        return $monthIndex + 1; // Domain number 1–6
    }

    /**
     * For a given test, return which domains have already been completed
     * (all questions answered) keyed by domain_number => true/false.
     */
    private function getCompletedDomains($testId, $domains, $scaleVersionId): array
    {
        $completed = [];
        foreach ($domains as $i => $domain) {
            $domainNumber = $i + 1;
            $total = DB::table('questions')
                ->where('domain_id', $domain->domain_id)
                ->where('scale_version_id', $scaleVersionId)
                ->count();
            $answered = DB::table('test_responses as tr')
                ->join('questions as q', 'q.question_id', 'tr.question_id')
                ->where('tr.test_id', $testId)
                ->where('q.domain_id', $domain->domain_id)
                ->count();
            $completed[$domainNumber] = ($answered >= $total && $total > 0);
        }
        return $completed;
    }

    /**
     * For a domain in a test, get count of answered and total questions.
     */
    private function getDomainProgress($testId, $domainId, $scaleVersionId): array
    {
        $total = DB::table('questions')
            ->where('domain_id', $domainId)
            ->where('scale_version_id', $scaleVersionId)
            ->count();
        $answered = DB::table('test_responses as tr')
            ->join('questions as q', 'q.question_id', 'tr.question_id')
            ->where('tr.test_id', $testId)
            ->where('q.domain_id', $domainId)
            ->count();
        return ['total' => $total, 'answered' => $answered];
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

            $activePeriod = DB::table('assessment_periods')
                ->where('student_id', $s->student_id)
                ->where('status', '!=', 'completed')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            $answered        = 0;
            $periodCompleted = false;
            $needsAction     = false;
            $allowedDomain   = null;

            if ($activePeriod) {
                $allowedDomain = $this->getAllowedDomainForPeriod($activePeriod);

                $familyTest = DB::table('tests')
                    ->where('student_id', $s->student_id)
                    ->where('examiner_id', $user->user_id)
                    ->where('period_id', $activePeriod->period_id)
                    ->whereIn('status', ['in_progress', 'completed', 'finalized'])
                    ->orderBy('test_date', 'desc')
                    ->first();

                if ($familyTest) {
                    $answered        = DB::table('test_responses')->where('test_id', $familyTest->test_id)->count();
                    $periodCompleted = in_array($familyTest->status, ['completed', 'finalized']);

                    if ($familyTest->status === 'in_progress' && $allowedDomain) {
                        $needsAction = true;
                    }
                } else {
                    $needsAction = $allowedDomain !== null;
                }
            }

            $children[] = [
                'student_id'        => $s->student_id,
                'name'              => $s->first_name . ' ' . $s->last_name,
                'first_name'        => $s->first_name,
                'age'               => $this->calculateAge($s->date_of_birth),
                'profile_image'     => $s->feature_path,
                'total_tests'       => $totalQuestions,
                'completed'         => $answered,
                'active_period'     => $activePeriod,
                'period_completed'  => $periodCompleted,
                'needs_action'      => $needsAction,
                'allowed_domain'    => $allowedDomain,
            ];
        }

        $assessments   = collect();
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
                    if (!isset($testsByPeriod[$t->period_id])) {
                        $testsByPeriod[$t->period_id] = $t;
                    }
                }

                foreach ($assessments as $ap) {
                    $ap->family_test     = $testsByPeriod[$ap->period_id] ?? null;
                    $ap->allowed_domain  = $this->getAllowedDomainForPeriod($ap);
                }

                $assessments = $assessments->filter(function ($ap) {
                    $familyTest = $ap->family_test ?? null;
                    return !($familyTest && in_array($familyTest->status, ['completed', 'finalized']));
                })->values();
            }
        }

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
                    'ap.student_id', 'ap.description', 'ap.start_date', 'ap.end_date',
                    'period_summary_scores.final_standard_score', 'period_summary_scores.final_interpretation',
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

        $notifications = [];

        return view('family.index', [
            'family_name'    => $family->family_name ?? 'Family',
            'children'       => $children,
            'assessments'    => $assessments,
            'latest_results' => $latestResults,
            'monitoring'     => $monitoring,
            'notifications'  => $notifications,
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

        return response()->json(['student' => $student, 'tests' => $tests]);
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
                $ap->family_test    = $testsByPeriod[$ap->period_id] ?? null;
                $ap->allowed_domain = $this->getAllowedDomainForPeriod($ap);
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
        [$user, $family] = $this->getAuthFamily();

        $students       = DB::table('students')->where('family_id', $family->user_id)->get();
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);

        $childrenTests = [];

        foreach ($students as $s) {
            $activePeriod = DB::table('assessment_periods')
                ->where('student_id', $s->student_id)
                ->where('status', '!=', 'completed')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            $allowedDomain    = $activePeriod ? $this->getAllowedDomainForPeriod($activePeriod) : null;
            $currentDomain    = $allowedDomain ? ($domains[$allowedDomain - 1] ?? null) : null;
            $monthNumber      = $allowedDomain;

            $existingTest = null;
            $domainProgress = ['total' => 0, 'answered' => 0];

            if ($activePeriod) {
                $existingTest = DB::table('tests')
                    ->where('student_id', $s->student_id)
                    ->where('examiner_id', $user->user_id)
                    ->where('period_id', $activePeriod->period_id)
                    ->whereIn('status', ['in_progress', 'completed', 'finalized'])
                    ->orderBy('test_date', 'desc')
                    ->first();

                if ($existingTest && $currentDomain) {
                    $domainProgress = $this->getDomainProgress(
                        $existingTest->test_id,
                        $currentDomain->domain_id,
                        $scaleVersionId
                    );
                }
            }

            // Build month timeline (6 months, show status for each)
            $monthTimeline = [];
            for ($m = 1; $m <= 6; $m++) {
                $domain = $domains[$m - 1] ?? null;
                $status = 'locked'; // default

                if ($m < $monthNumber) {
                    $status = 'completed'; // past months — assumed done
                } elseif ($m === $monthNumber) {
                    $status = 'active';
                }
                // future months stay 'locked'

                $monthTimeline[] = [
                    'month'       => $m,
                    'domain'      => $domain,
                    'status'      => $status,
                ];
            }

            $childrenTests[] = [
                'student'        => $s,
                'active_period'  => $activePeriod,
                'allowed_domain' => $allowedDomain,
                'current_domain' => $currentDomain,
                'month_number'   => $monthNumber,
                'existing_test'  => $existingTest,
                'domain_progress'=> $domainProgress,
                'month_timeline' => $monthTimeline,
            ];
        }

        return view('family.tests', compact('childrenTests', 'domains'));
    }

    public function showStartTest($studentId)
    {
        $user = Auth::user();

        $student = DB::table('students')
            ->where('student_id', $studentId)
            ->where('family_id', $user->user_id)
            ->first();

        if (!$student) {
            return redirect()->route('family.index')->with('error', 'Student not found.');
        }

        $period = DB::table('assessment_periods')
            ->where('student_id', $studentId)
            ->where('status', '!=', 'completed')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$period) {
            return redirect()->route('family.index')->with('error', 'No active assessment period found.');
        }

        $allowedDomain  = $this->getAllowedDomainForPeriod($period);
        $scaleVersionId = $this->getScaleVersionId();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        $existingTest = DB::table('tests')
            ->where('student_id', $studentId)
            ->where('examiner_id', $user->user_id)
            ->where('period_id', $period->period_id)
            ->whereIn('status', ['in_progress', 'completed', 'finalized'])
            ->orderBy('test_date', 'desc')
            ->first();

        if ($existingTest && in_array($existingTest->status, ['completed', 'finalized'], true)) {
            return redirect()->route('family.tests.result', $existingTest->test_id);
        }

        $answeredCount = 0;
        if ($existingTest && $existingTest->status === 'in_progress') {
            $answeredCount = DB::table('test_responses')
                ->where('test_id', $existingTest->test_id)
                ->count();
        }

        return view('family.start-test', compact(
            'student', 'period', 'existingTest', 'answeredCount', 'totalQuestions', 'allowedDomain'
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

        // ── Domain-per-month enforcement ──────────────────────────────────────
        // Get the period linked to this test so we know the allowed domain.
        $period = DB::table('assessment_periods')
            ->where('period_id', $test->period_id)
            ->first();

        $allowedDomain = $period ? $this->getAllowedDomainForPeriod($period) : null;

        // If the user tries to access a domain other than the allowed one, block.
        if ($allowedDomain !== null && $domainNumber !== $allowedDomain) {
            return redirect()->route('family.tests.question', [
                'test'   => $testId,
                'domain' => $allowedDomain,
                'index'  => 1,
            ])->with('error', "This month's domain is Domain {$allowedDomain}. You cannot access other domains yet.");
        }
        // ─────────────────────────────────────────────────────────────────────

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

        // Domain-scoped progress (only count this domain's questions)
        $domainProgress = $this->getDomainProgress($testId, $currentDomain->domain_id, $scaleVersionId);

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        // Prev/next are only within the same domain — no cross-domain navigation
        if ($prevDomain !== null && $prevDomain !== $domainNumber) {
            $prevDomain = null;
            $prevIndex  = null;
        }
        if ($nextDomain !== null && $nextDomain !== $domainNumber) {
            $nextDomain = null;
            $nextIndex  = null;
        }

        $questionText = $question->display_text ?? $question->text;

        // ── Game redirects (unchanged) ────────────────────────────────────────
        if ($currentDomain->domain_name === 'Receptive Language' && (int)$questionIndex === 3) {
            return redirect()->route('family.tests.follow.instructions.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        if ($currentDomain->domain_name === 'Receptive Language' && (int)$questionIndex === 5) {
            return redirect()->route('family.tests.point.objects.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        if (str_contains(strtolower($questionText), 'when looking at pictures, can your child name what they see')) {
            return redirect()->route('family.tests.name.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        if (str_contains(strtolower($questionText), 'match objects of the same color')) {
            return redirect()->route('family.tests.color.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        if (str_contains(strtolower($questionText), 'match two pictures that are the same')) {
            return redirect()->route('family.tests.picture.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        if (str_contains(strtolower($questionText), 'sort or put things together based on their shape')) {
            return redirect()->route('family.tests.shape.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        if (str_contains(strtolower($questionText), 'sort objects using both color and size')) {
            return redirect()->route('family.tests.size.color.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        if (str_contains(strtolower($questionText), 'line up objects from smallest to biggest')) {
            return redirect()->route('family.tests.size.order.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        if ($currentDomain->domain_name === 'Cognitive' && (int)$questionIndex === 7) {
            return redirect()->route('family.tests.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        if ($currentDomain->domain_name === 'Cognitive' && (int)$questionIndex === 12) {
            return redirect()->route('family.tests.puzzle.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        if ($currentDomain->domain_name === 'Cognitive' && (int)$questionIndex === 15) {
            return redirect()->route('family.tests.color.name.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        if ($currentDomain->domain_name === 'Cognitive' && (int)$questionIndex === 16) {
            return redirect()->route('family.tests.animal.veggie.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        if ($currentDomain->domain_name === 'Cognitive' && (int)$questionIndex === 20) {
            return redirect()->route('family.tests.whats.wrong.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        if ($currentDomain->domain_name === 'Cognitive' && (int)$questionIndex === 21) {
            return redirect()->route('family.tests.letter.match.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        if ($currentDomain->domain_name === 'Social-Emotional' && (int)$questionIndex === 11) {
            return redirect()->route('family.tests.feelings.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        if (str_contains(strtolower($questionText), 'name objects in pictures')) {
            return redirect()->route('family.tests.name.game', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]);
        }
        // ─────────────────────────────────────────────────────────────────────

        $totalDomainQuestions = count($questions);

        return view('family.question', compact(
            'test', 'testId', 'currentDomain', 'question', 'questionText',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions', 'totalDomainQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex', 'domains',
            'allowedDomain', 'domainProgress', 'period'
        ));
    }

    public function startTest($studentId)
    {
        $user = Auth::user();

        if ($user->role !== 'family') abort(403, 'Unauthorized access');

        $student = DB::table('students')
            ->where('student_id', $studentId)
            ->where('family_id', $user->user_id)
            ->first();

        if (!$student) {
            return redirect()->route('family.index')->with('error', 'Student not found or does not belong to your family.');
        }

        $period = DB::table('assessment_periods')
            ->where('student_id', $studentId)
            ->where('status', '!=', 'completed')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$period) {
            return redirect()->route('family.index')->with('error', 'No active assessment period found for this student.');
        }

        $allowedDomain = $this->getAllowedDomainForPeriod($period);

        if (!$allowedDomain) {
            return redirect()->route('family.index')->with('error', 'No domain is available this month.');
        }

        $existingTest = DB::table('tests')
            ->where('student_id', $studentId)
            ->where('examiner_id', $user->user_id)
            ->where('period_id', $period->period_id)
            ->whereIn('status', ['in_progress', 'completed', 'finalized'])
            ->orderBy('test_date', 'desc')
            ->first();

        if ($existingTest) {
            if ($existingTest->status === 'in_progress') {
                // Resume at first unanswered question in the allowed domain
                $scaleVersionId = $this->getScaleVersionId();
                $domains        = $this->getDomains($scaleVersionId);
                $currentDomain  = $domains[$allowedDomain - 1];

                $answeredIds = DB::table('test_responses')
                    ->join('questions as q', 'q.question_id', 'test_responses.question_id')
                    ->where('test_responses.test_id', $existingTest->test_id)
                    ->where('q.domain_id', $currentDomain->domain_id)
                    ->pluck('test_responses.question_id')
                    ->toArray();

                $domainQuestions = DB::table('questions')
                    ->where('domain_id', $currentDomain->domain_id)
                    ->where('scale_version_id', $scaleVersionId)
                    ->orderBy('order')
                    ->pluck('question_id');

                $resumeIndex = 1;
                foreach ($domainQuestions as $qi => $questionId) {
                    if (!in_array($questionId, $answeredIds)) {
                        $resumeIndex = $qi + 1;
                        break;
                    }
                }

                return redirect()->route('family.tests.question', [
                    'test'   => $existingTest->test_id,
                    'domain' => $allowedDomain,
                    'index'  => $resumeIndex,
                ]);
            }

            return redirect()->route('family.tests.result', $existingTest->test_id);
        }

        // Create a new test
        $testId = DB::table('tests')->insertGetId([
            'period_id'   => $period->period_id,
            'student_id'  => $studentId,
            'examiner_id' => $user->user_id,
            'test_date'   => now(),
            'notes'       => null,
            'status'      => 'in_progress',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->route('family.tests.question', [
            'test'   => $testId,
            'domain' => $allowedDomain,
            'index'  => 1,
        ]);
    }

    public function submitQuestion(Request $request, $testId, $domainNumber, $questionIndex)
    {
        $request->validate(['response' => 'required|in:yes,no']);

        $test = $this->verifyTestOwnership($testId);

        if (in_array($test->status, ['completed', 'finalized', 'canceled', 'terminated'], true)) {
            return redirect()->route('family.index')->with('error', 'This test cannot be modified.');
        }

        // Enforce domain-per-month: only allow submitting to the allowed domain
        $period        = DB::table('assessment_periods')->where('period_id', $test->period_id)->first();
        $allowedDomain = $period ? $this->getAllowedDomainForPeriod($period) : null;

        if ($allowedDomain !== null && $domainNumber !== $allowedDomain) {
            return redirect()->route('family.tests.question', [
                'test'   => $testId,
                'domain' => $allowedDomain,
                'index'  => 1,
            ])->with('error', 'You can only answer questions for this month\'s domain.');
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

        // Navigate only within this domain
        $nextIndex  = $questionIndex + 1;
        $nextDomain = $domainNumber;

        if ($nextIndex > count($questionIds)) {
            // Domain finished — go to result/review page
            return redirect()->route('family.tests.result', $testId)
                ->with('success', "Domain {$domainNumber} complete! Come back next month for the next domain.");
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

        // Get the allowed domain for this period
        $period        = DB::table('assessment_periods')->where('period_id', $test->period_id)->first();
        $allowedDomain = $period ? $this->getAllowedDomainForPeriod($period) : null;

        $domainStats  = [];
        $domainNumber = 1;

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

            // Determine if this domain is the current month's domain
            $isCurrentMonthDomain = ($domainNumber === $allowedDomain);
            $isLocked             = ($allowedDomain !== null && $domainNumber > $allowedDomain);
            $isPast               = ($allowedDomain !== null && $domainNumber < $allowedDomain);

            $domainStats[] = [
                'domain_number'          => $domainNumber++,
                'domain_name'            => $questions->first()->domain_name,
                'total'                  => count($domainQIds),
                'answered'               => $domainAnswered,
                'yes_count'              => $yesCount,
                'is_complete'            => $domainAnswered === count($domainQIds),
                'first_unanswered_index' => $firstUnansweredIndex,
                'is_current_month'       => $isCurrentMonthDomain,
                'is_locked'              => $isLocked,
                'is_past'                => $isPast,
            ];
        }

        // For the current month's domain: check if ALL questions are answered
        $currentDomainStats = collect($domainStats)->firstWhere('domain_number', $allowedDomain);
        $allAnswered        = $currentDomainStats
            ? $currentDomainStats['is_complete']
            : false;

        return view('family.result', compact(
            'test', 'testId', 'domainStats', 'totalQuestions', 'totalAnswered',
            'allAnswered', 'allowedDomain', 'period'
        ));
    }

    public function finalize($testId)
    {
        $test = $this->verifyTestOwnership($testId);

        if (in_array($test->status, ['completed', 'finalized', 'canceled', 'terminated'], true)) {
            return redirect()->route('family.tests.result', $testId)
                ->with('info', 'This test has already been submitted.');
        }

        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);

        // Get allowed domain for this period
        $period        = DB::table('assessment_periods')->where('period_id', $test->period_id)->first();
        $allowedDomain = $period ? $this->getAllowedDomainForPeriod($period) : null;

        if (!$allowedDomain) {
            return redirect()->route('family.tests.result', $testId)
                ->with('error', 'No domain is assigned for this month.');
        }

        // Only validate that the current month's domain is complete
        $currentDomain  = $domains[$allowedDomain - 1];
        $domainProgress = $this->getDomainProgress($testId, $currentDomain->domain_id, $scaleVersionId);

        if ($domainProgress['answered'] === 0) {
            return redirect()->route('family.tests.result', $testId)
                ->with('error', 'No questions have been answered for this month\'s domain.');
        }

        if ($domainProgress['answered'] < $domainProgress['total']) {
            $remaining = $domainProgress['total'] - $domainProgress['answered'];
            return redirect()->route('family.tests.result', $testId)
                ->with('error', "{$remaining} question(s) remaining in this month's domain. Please answer all before submitting.");
        }

        // All questions in this month's domain answered — mark domain as done.
        // We keep test status as in_progress so next month's domain can be added.
        // Only mark completed when all 6 domains are done.
        $allDomainsComplete = true;
        foreach ($domains as $i => $domain) {
            $progress = $this->getDomainProgress($testId, $domain->domain_id, $scaleVersionId);
            if ($progress['answered'] < $progress['total']) {
                $allDomainsComplete = false;
                break;
            }
        }

        $newStatus = $allDomainsComplete ? 'completed' : 'in_progress';

        DB::table('tests')
            ->where('test_id', $testId)
            ->where('examiner_id', Auth::id())
            ->update(['status' => $newStatus, 'updated_at' => now()]);

        Log::info('Domain submitted', [
            'test_id'        => $testId,
            'allowed_domain' => $allowedDomain,
            'all_complete'   => $allDomainsComplete,
        ]);

        if ($allDomainsComplete) {
            $freshTest = Test::with('assessmentPeriod', 'student')->find($testId);
            if ($freshTest) {
                app(EccdScoring::class)->scoreTestAndRecompute($freshTest);
            }

            return redirect()->route('family.index')
                ->with('success', 'All 6 domains complete! Your test has been submitted.');
        }

        return redirect()->route('family.index')
            ->with('success', "Month {$allowedDomain} domain complete! Come back next month to continue.");
    }

    // ──────────────────────────────────────────────
    //  GAME METHODS (unchanged)
    // ──────────────────────────────────────────────

    public function showGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];
        $existingResponse = DB::table('test_responses')->where('test_id', $testId)->where('question_id', $question->question_id)->value('response');
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        [$prevDomain, $prevIndex] = [null, null]; // no cross-domain nav in games
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));
        if ($nextDomain !== $domainNumber) { $nextDomain = null; $nextIndex = null; }
        return view('family.matching-game', compact('test', 'testId', 'currentDomain', 'question', 'domainNumber', 'questionIndex', 'existingResponse', 'totalAnswered', 'totalQuestions', 'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'));
    }

    public function showFollowInstructionsGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];
        $existingResponse = DB::table('test_responses')->where('test_id', $testId)->where('question_id', $question->question_id)->value('response');
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        [$prevDomain, $prevIndex] = [null, null];
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));
        if ($nextDomain !== $domainNumber) { $nextDomain = null; $nextIndex = null; }
        return view('family.RL-3-One-step', compact('test', 'testId', 'currentDomain', 'question', 'domainNumber', 'questionIndex', 'existingResponse', 'totalAnswered', 'totalQuestions', 'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'));
    }

    public function showPointObjectsGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];
        $existingResponse = DB::table('test_responses')->where('test_id', $testId)->where('question_id', $question->question_id)->value('response');
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        [$prevDomain, $prevIndex] = [null, null];
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));
        if ($nextDomain !== $domainNumber) { $nextDomain = null; $nextIndex = null; }
        return view('family.RL-5-Points-Objects', compact('test', 'testId', 'currentDomain', 'question', 'domainNumber', 'questionIndex', 'existingResponse', 'totalAnswered', 'totalQuestions', 'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'));
    }

    public function showColorGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];
        $existingResponse = DB::table('test_responses')->where('test_id', $testId)->where('question_id', $question->question_id)->value('response');
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        [$prevDomain, $prevIndex] = [null, null];
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));
        if ($nextDomain !== $domainNumber) { $nextDomain = null; $nextIndex = null; }
        return view('family.color-matching-game', compact('test', 'testId', 'currentDomain', 'question', 'domainNumber', 'questionIndex', 'existingResponse', 'totalAnswered', 'totalQuestions', 'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'));
    }

    public function showPictureGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];
        $existingResponse = DB::table('test_responses')->where('test_id', $testId)->where('question_id', $question->question_id)->value('response');
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        [$prevDomain, $prevIndex] = [null, null];
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));
        if ($nextDomain !== $domainNumber) { $nextDomain = null; $nextIndex = null; }
        return view('family.picture-matching-game', compact('test', 'testId', 'currentDomain', 'question', 'domainNumber', 'questionIndex', 'existingResponse', 'totalAnswered', 'totalQuestions', 'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'));
    }

    public function showShapeGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];
        $existingResponse = DB::table('test_responses')->where('test_id', $testId)->where('question_id', $question->question_id)->value('response');
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        [$prevDomain, $prevIndex] = [null, null];
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));
        if ($nextDomain !== $domainNumber) { $nextDomain = null; $nextIndex = null; }
        return view('family.shape-sorting-game', compact('test', 'testId', 'currentDomain', 'question', 'domainNumber', 'questionIndex', 'existingResponse', 'totalAnswered', 'totalQuestions', 'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'));
    }

    public function showSizeColorGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];
        $existingResponse = DB::table('test_responses')->where('test_id', $testId)->where('question_id', $question->question_id)->value('response');
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        [$prevDomain, $prevIndex] = [null, null];
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));
        if ($nextDomain !== $domainNumber) { $nextDomain = null; $nextIndex = null; }
        return view('family.size-color-sorting-game', compact('test', 'testId', 'currentDomain', 'question', 'domainNumber', 'questionIndex', 'existingResponse', 'totalAnswered', 'totalQuestions', 'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'));
    }

    public function showSizeOrderGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];
        $existingResponse = DB::table('test_responses')->where('test_id', $testId)->where('question_id', $question->question_id)->value('response');
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        [$prevDomain, $prevIndex] = [null, null];
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));
        if ($nextDomain !== $domainNumber) { $nextDomain = null; $nextIndex = null; }
        return view('family.size-ordering-game', compact('test', 'testId', 'currentDomain', 'question', 'domainNumber', 'questionIndex', 'existingResponse', 'totalAnswered', 'totalQuestions', 'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'));
    }

    public function showNameGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];
        $existingResponse = DB::table('test_responses')->where('test_id', $testId)->where('question_id', $question->question_id)->value('response');
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        [$prevDomain, $prevIndex] = [null, null];
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));
        if ($nextDomain !== $domainNumber) { $nextDomain = null; $nextIndex = null; }
        return view('family.EL-8-Name-Objects', compact('test', 'testId', 'currentDomain', 'question', 'domainNumber', 'questionIndex', 'existingResponse', 'totalAnswered', 'totalQuestions', 'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'));
    }

    public function showColorNameGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];
        $existingResponse = DB::table('test_responses')->where('test_id', $testId)->where('question_id', $question->question_id)->value('response');
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        [$prevDomain, $prevIndex] = [null, null];
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));
        if ($nextDomain !== $domainNumber) { $nextDomain = null; $nextIndex = null; }
        return view('family.C-15-Name-that-color', compact('test', 'testId', 'currentDomain', 'question', 'domainNumber', 'questionIndex', 'existingResponse', 'totalAnswered', 'totalQuestions', 'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'));
    }

    public function showAnimalVeggieGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];
        $existingResponse = DB::table('test_responses')->where('test_id', $testId)->where('question_id', $question->question_id)->value('response');
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        [$prevDomain, $prevIndex] = [null, null];
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));
        if ($nextDomain !== $domainNumber) { $nextDomain = null; $nextIndex = null; }
        return view('family.C-16-Name-Animal-Vegetable', compact('test', 'testId', 'currentDomain', 'question', 'domainNumber', 'questionIndex', 'existingResponse', 'totalAnswered', 'totalQuestions', 'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'));
    }

    public function showWhatsWrongGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];
        $existingResponse = DB::table('test_responses')->where('test_id', $testId)->where('question_id', $question->question_id)->value('response');
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        [$prevDomain, $prevIndex] = [null, null];
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));
        if ($nextDomain !== $domainNumber) { $nextDomain = null; $nextIndex = null; }
        return view('family.C-20-Whats-wrong-pic', compact('test', 'testId', 'currentDomain', 'question', 'domainNumber', 'questionIndex', 'existingResponse', 'totalAnswered', 'totalQuestions', 'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'));
    }

    public function showLetterMatchGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];
        $existingResponse = DB::table('test_responses')->where('test_id', $testId)->where('question_id', $question->question_id)->value('response');
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        [$prevDomain, $prevIndex] = [null, null];
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));
        if ($nextDomain !== $domainNumber) { $nextDomain = null; $nextIndex = null; }
        return view('family.C-21-UpperLower-Letters', compact('test', 'testId', 'currentDomain', 'question', 'domainNumber', 'questionIndex', 'existingResponse', 'totalAnswered', 'totalQuestions', 'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'));
    }

    public function showPuzzleGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];
        $existingResponse = DB::table('test_responses')->where('test_id', $testId)->where('question_id', $question->question_id)->value('response');
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        [$prevDomain, $prevIndex] = [null, null];
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));
        if ($nextDomain !== $domainNumber) { $nextDomain = null; $nextIndex = null; }
        return view('family.C-12-Puzzle', compact('test', 'testId', 'currentDomain', 'question', 'domainNumber', 'questionIndex', 'existingResponse', 'totalAnswered', 'totalQuestions', 'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'));
    }

    public function showFeelingsGame($testId, $domainNumber, $questionIndex)
    {
        $test           = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains        = $this->getDomains($scaleVersionId);
        $currentDomain  = $domains[$domainNumber - 1];
        $questions      = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question       = $questions[$questionIndex - 1];
        $existingResponse = DB::table('test_responses')->where('test_id', $testId)->where('question_id', $question->question_id)->value('response');
        $totalAnswered  = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        [$prevDomain, $prevIndex] = [null, null];
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));
        if ($nextDomain !== $domainNumber) { $nextDomain = null; $nextIndex = null; }
        return view('family.SE011-Feeling-inOthers', compact('test', 'testId', 'currentDomain', 'question', 'domainNumber', 'questionIndex', 'existingResponse', 'totalAnswered', 'totalQuestions', 'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'));
    }

    // ──────────────────────────────────────────────
    //  MISC
    // ──────────────────────────────────────────────

    public function child($studentId)
    {
        return redirect()->route('family.index');
    }

    public function markIncomplete($testId)
    {
        $test = $this->verifyTestOwnership($testId);

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
        DB::table('tests')
            ->where('test_id', $testId)
            ->where('examiner_id', Auth::id())
            ->update(['updated_at' => now()]);

        return redirect()->route('family.index')->with('success', 'Test paused. You can resume it later.');
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