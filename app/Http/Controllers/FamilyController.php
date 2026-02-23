<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'family') abort(403, 'Unauthorized access');

        [$user, $family] = $this->getAuthFamily();

        $avatars  = ['bunny', 'fox', 'frog', 'mouse', 'panda', 'tiger'];

        $students = DB::table('students')
            ->where('family_id', $family->user_id)
            ->orderBy('date_of_birth', 'desc')
            ->get();

        $studentIds = $students->pluck('student_id');

        // Upcoming assessments
        $upcomingAssessments = DB::table('assessment_periods as ap')
            ->join('students as s', 's.student_id', 'ap.student_id')
            ->whereIn('ap.student_id', $studentIds)
            ->where('ap.start_date', '>=', Carbon::now()->subDays(30))
            ->orderBy('ap.start_date')
            ->limit(10)
            ->select('ap.*', 's.first_name', 's.last_name')
            ->get()
            ->map(function ($p) {
                $now   = Carbon::now();
                $start = Carbon::parse($p->start_date);
                $end   = Carbon::parse($p->end_date);

                $p->display_status = match(true) {
                    $p->status === 'completed' => 'completed',
                    $now->greaterThan($end)    => 'overdue',
                    $now->between($start, $end)=> 'in_progress',
                    default                    => 'scheduled',
                };

                return $p;
            });

        // Latest test results per child
        $latestResults = DB::table('tests as t')
            ->join('test_standard_scores as ss', 'ss.test_id', 't.test_id')
            ->join('students as s', 's.student_id', 't.student_id')
            ->whereIn('t.student_id', $studentIds)
            ->whereIn('t.status', ['completed', 'finalized'])
            ->orderBy('t.test_date', 'desc')
            ->select('t.student_id', 't.test_date', 'ss.standard_score', 'ss.interpretation',
                     's.first_name', 's.last_name', 's.profile_image')
            ->get()
            ->unique('student_id'); // one result per child

        return view('family.index', [
            'family_name'          => $family->family_name ?? 'Family',
            'students'             => $students,
            'upcoming_assessments' => $upcomingAssessments,
            'latest_results'       => $latestResults,
            'avatars'              => $avatars,
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

        $newImage = $student->profile_image;

        if ($request->filled('selected_avatar')) {
            $newImage = $request->selected_avatar;
        }

        if ($request->hasFile('profile_image')) {
            if ($student->profile_image && str_starts_with($student->profile_image, 'profiles/')) {
                Storage::disk('public')->delete($student->profile_image);
            }
            $newImage = $request->file('profile_image')->store('profiles', 'public');
        }

        DB::table('students')
            ->where('student_id', $studentId)
            ->update(['profile_image' => $newImage, 'updated_at' => now()]);

        return redirect()->route('family.dashboard')
            ->with('success', 'Profile image updated successfully!');
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
            ->where('ap.start_date', '>=', now())
            ->orderBy('ap.start_date')
            ->select('ap.*', 's.first_name', 's.last_name')
            ->get();

        return response()->json($assessments);
    }

    // ──────────────────────────────────────────────
    //  TEST FLOW
    // ──────────────────────────────────────────────

    public function startTest($studentId)
    {
        $user = Auth::user();

        $student = DB::table('students')
            ->where('student_id', $studentId)
            ->where('family_id', $user->user_id)
            ->first();

        if (!$student) {
            return redirect()->route('family.index')
                ->with('error', 'Student not found or does not belong to your family.');
        }

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
            'domain' => 1,
            'index'  => 1,
        ]);
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

        return view('family.question', compact(
            'test', 'testId', 'currentDomain', 'question', 'questionText',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex', 'domains'
        ));
    }

    public function submitQuestion(Request $request, $testId, $domainNumber, $questionIndex)
    {
        $request->validate(['response' => 'required|in:yes,no']);

        $test = $this->verifyTestOwnership($testId);

        if (in_array($test->status, ['finalized', 'canceled', 'terminated'])) {
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
            ->update(['status' => 'completed', 'updated_at' => now()]);

        return redirect()->route('family.index')
            ->with('success', 'Test completed successfully!');
    }

    // ──────────────────────────────────────────────
    //  PLACEHOLDER ROUTES
    // ──────────────────────────────────────────────

    public function child($studentId)       { return redirect()->route('family.index'); }
    public function markIncomplete($testId) { return redirect()->route('family.tests.result', $testId); }
    public function pause($testId)          { return redirect()->route('family.tests.result', $testId); }

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
}