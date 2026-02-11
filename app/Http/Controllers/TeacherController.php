<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Test;
use App\Models\Domain;
use App\Models\Question;
use App\Models\DomainScore;
use App\Services\EccdScoring;

class TeacherController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) { return redirect()->route('login'); }
        if ($user->role !== 'teacher') { return redirect()->route('index'); }

        // Ensure teacher record exists with PK = user id
        $teacher = Teacher::firstOrCreate(['id' => $user->id], ['hire_date' => null, 'status' => 'active']);
        $teacher->load('students.tests');
        $students = $teacher->students;

        $status = [];
        $longitudinals = [];
        foreach ($students as $s) {
            // Determine active in-progress teacher test (for this teacher only)
            $inProgress = $s->tests()->whereHas('observer', function($q) use ($teacher){ $q->where('role','teacher')->where('id', $teacher->id); })
                ->where('status','in_progress')->orderBy('test_date','desc')->first();
            // Determine last completed teacher test for eligibility calculation (for this teacher only)
            $latestCompleted = $s->tests()->whereHas('observer', function($q) use ($teacher){ $q->where('role','teacher')->where('id', $teacher->id); })
                ->finalized()->orderBy('test_date','desc')->first();
            $monthsSince = $latestCompleted ? \Illuminate\Support\Carbon::parse($latestCompleted->test_date)->diffInMonths(now()) : null;
            $eligible = ($inProgress === null) && ($monthsSince === null || $monthsSince >= 6);
            $status[$s->id] = [
                'latest_teacher' => $latestCompleted ?? $inProgress,
                'latest_teacher_completed' => $latestCompleted,
                'in_progress' => $inProgress,
                'eligible' => $eligible,
            ];
            // Longitudinal summary for dashboard (completed periods only)
            $longitudinals[$s->id] = \App\Services\AssessmentLongitudinal::summarize($s)['longitudinal'] ?? null;
        }

        return view('teacher.dashboard', compact('teacher','students','status','longitudinals'));
    }

    public function student($studentId)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'teacher') { return redirect()->route('index'); }
        $teacher = Teacher::firstOrCreate(['id' => $user->id], ['hire_date' => null, 'status' => 'active']);
        $student = Student::with(['family','section','teachers.user','tests.scores.domain','tests.responses','assessmentPeriods'])
            ->whereHas('teachers', function($q) use ($teacher){ $q->where('teachers.id', $teacher->id); })
            ->findOrFail($studentId);

        $domains = Domain::orderBy('id')->get();
        $tests = $student->tests()->with(['scores','observer'])->orderBy('test_date','desc')->get();
        // Separate teacher vs family tests
        $teacherTests = $tests->filter(fn($t) => $t->observer?->role === 'teacher');
        $familyTests = $tests->filter(fn($t) => $t->observer?->role === 'family');

        // Compute averages for 6/12/18 months (teacher primary)
        $avg = function($collection, $months, $domainId){
            $cutoff = now()->subMonths($months);
            $sel = $collection->filter(fn($t) => \Illuminate\Support\Carbon::parse($t->test_date)->gte($cutoff));
            return round($sel->map(function($t) use ($domainId){
                    $v = optional($t->scores->firstWhere('domain_id',$domainId))->scaled_score;
                    if ($v === null) return null;
                    $max = (int) config('eccd.scaled_score_max', 19);
                    return $v > $max ? \App\Services\EccdScoring::percentageToScaled((float)$v) : (float)$v;
                })
                ->filter()->avg() ?? 0, 2);
        };

        return view('teacher.student', compact('student','domains','tests','teacherTests','familyTests','avg'));
    }

    public function startTest($studentId)
    {
        $now = now();
        $user = Auth::user();
        if (!$user || $user->role !== 'teacher') { return redirect()->route('index'); }
        $teacher = Teacher::firstOrCreate(['id' => $user->id], ['hire_date' => null, 'status' => 'active']);
                // Ensure teacher is actively assigned via explicit pivot column filter (avoid alias issues)
                $student = Student::whereHas('teachers', function($q) use ($teacher){
                        $q->where('teachers.id', $teacher->id)
                            ->where('teacher_student.status', 'active');
                })->with('assessmentPeriods')->findOrFail($studentId);

        // Prevent concurrent in-progress tests for THIS teacher on the same student
        $hasInProgress = $student->tests()->where('status', 'in_progress')
            ->whereHas('observer', function($q) use ($teacher){ $q->where('role','teacher')->where('id',$teacher->id); })
            ->exists();
        if ($hasInProgress) {
            session()->flash('error', 'You already have an in-progress test for this student.');
            return redirect()->route('teacher.student', $student->id);
        }

        // Enforce one test per assessment period per teacher (non-cancelled/terminated)
        $period = $student->assessmentPeriods->first(function($p) use ($now){ return $now->between($p->starts_at, $p->ends_at); });
        if ($period) {
            $hasThisPeriod = $student->tests()->where('assessment_period_id', $period->id)
                ->whereHas('observer', function($q) use ($teacher){ $q->where('role','teacher')->where('id',$teacher->id); })
                ->whereNotIn('status', ['cancelled','terminated'])
                ->exists();
            if ($hasThisPeriod) {
                session()->flash('error', 'You already have a test for this assessment period.');
                return redirect()->route('teacher.student', $student->id);
            }
        }

        // Enforce overall limit per teacher: max 3 non-cancelled/terminated tests by THIS teacher
        $activeCount = $student->tests()->whereNotIn('status', ['cancelled','terminated'])
            ->whereHas('observer', function($q) use ($teacher){ $q->where('role','teacher')->where('id',$teacher->id); })
            ->count();
        if ($activeCount >= 3) {
            session()->flash('error', 'Limit reached: max 3 active tests by you for this student.');
            return redirect()->route('teacher.student', $student->id);
        }

        // Determine current active assessment period
        $now = now();
        if (!$period) {
            session()->flash('error', 'No active assessment period.');
            return redirect()->route('teacher.student', $student->id);
        }
        $windowStart = \Illuminate\Support\Carbon::parse($period->starts_at)->startOfDay();
        $windowEnd = \Illuminate\Support\Carbon::parse($period->ends_at)->endOfDay();

        // Reuse the scheduled pending test for this window if present
        $scheduled = $student->tests()->where('assessment_period_id', $period->id)->where('status','pending')->first();
        if ($scheduled && $now->betweenIncluded($windowStart, $windowEnd)) {
            $scheduled->observer_id = $user->id;
            $scheduled->status = 'in_progress';
            $scheduled->started_at = now();
            $scheduled->assessment_period_id = $period->id;
            $domains = Domain::with('questions')->orderBy('id')->get();
            $order = [];
            foreach ($domains as $d) {
                $ids = $d->questions->pluck('id')->all();
                shuffle($ids);
                $order[$d->id] = $ids;
            }
            $scheduled->question_order = json_encode($order);
            $scheduled->save();
            Session::put("teacher_test_order_{$scheduled->id}", $order);
            // Seed responses for all questions with score = null to track not-answered
            $rows = [];
            foreach ($domains as $d) {
                foreach ($d->questions as $q) {
                    $rows[] = [
                        'test_id' => $scheduled->id,
                        'question_id' => $q->id,
                        'score' => null,
                        'comment' => null,
                        'updated_at' => now(),
                    ];
                }
            }
            foreach (array_chunk($rows, 500) as $chunk) { \Illuminate\Support\Facades\DB::table('test_responses')->insert($chunk); }
            $firstDomain = $domains->first();
            return redirect()->route('teacher.tests.question', [$scheduled->id, $firstDomain->id, 0]);
        }

        // If any test exists today: resume teacher test if present; else pick another date
        $existingToday = $student->tests()->whereDate('test_date', now()->toDateString())->first();
        if ($existingToday && $existingToday->observer?->role === 'teacher' && $existingToday->observer_id === $teacher->id) {
            $firstDomain = Domain::orderBy('id')->first();
            session()->flash('success', 'Resuming your test scheduled for today.');
            return redirect()->route('teacher.tests.question', [$existingToday->id, $firstDomain->id, 0]);
        }

        // Enforce 6-month interval relative to last non-cancelled TEACHER test
        $lastActive = $student->tests()->whereNotIn('status',['cancelled','terminated'])
            ->whereHas('observer', function($q) use ($teacher){ $q->where('role','teacher')->where('id',$teacher->id); })
            ->orderBy('test_date','desc')->first();
        if ($lastActive) {
            $months = \Illuminate\Support\Carbon::parse($lastActive->test_date)->diffInMonths(now());
            if ($months < 6) {
                session()->flash('error', 'You can start a new test 6 months after your last completed test.');
                return redirect()->route('teacher.student', $student->id);
            }
        }
        // Only allow ad-hoc start if within the current window and no scheduled test exists (fallback)
        if (!$now->betweenIncluded($windowStart, $windowEnd)) {
            session()->flash('error', 'Not within the current eligibility window.');
            return redirect()->route('teacher.student', $student->id);
        }
        // Find an available date within the window (prefer today)
        // Respect unique (student_id, test_date): consider ALL tests for date collision within window
        $used = $student->tests()->whereBetween('test_date', [$windowStart->toDateString(), $windowEnd->toDateString()])
            ->pluck('test_date')->map(fn($d)=> (string)$d)->toArray();
        $candidate = $now->toDateString();
        if (!in_array($candidate, $used)) {
            // ok
        } else {
            $cursor = $now->copy();
            $found = null;
            while ($cursor->lte($windowEnd)) {
                $c = $cursor->toDateString();
                if (!in_array($c, $used)) { $found = $c; break; }
                $cursor->addDay();
            }
            if ($found) { 
                $candidate = $found; 
                session()->flash('success', "Today's date is taken. Scheduled on $candidate.");
            } else { 
                session()->flash('error', 'No available date within the window.');
                return redirect()->route('teacher.student', $student->id); 
            }
        }

        $testId = DB::table('tests')->insertGetId([
            'student_id' => $student->id,
            'assessment_period_id' => $period->id,
            'observer_id' => $user->id,
            'test_date' => $candidate,
            'status' => 'in_progress',
            'started_at' => now(),
            'submitted_by' => null,
            'submitted_at' => null,
        ]);

        $domains = Domain::with('questions')->orderBy('id')->get();
        $order = [];
        foreach ($domains as $d) {
            $ids = $d->questions->pluck('id')->all();
            shuffle($ids);
            $order[$d->id] = $ids;
        }
        Session::put("teacher_test_order_$testId", $order);
        DB::table('tests')->where('id', $testId)->update(['question_order' => json_encode($order)]);

        // Seed responses for all questions with score = null to track not-answered
        $rows = [];
        foreach ($domains as $d) {
            foreach ($d->questions as $q) {
                $rows[] = [
                    'test_id' => $testId,
                    'question_id' => $q->id,
                    'score' => null,
                    'comment' => null,
                    'updated_at' => now(),
                ];
            }
        }
        foreach (array_chunk($rows, 500) as $chunk) { DB::table('test_responses')->insert($chunk); }

        $firstDomain = $domains->first();
        return redirect()->route('teacher.tests.question', [$testId, $firstDomain->id, 0]);
    }

    public function showQuestion($testId, $domainId, $index)
    {
        $user = Auth::user();
        $test = Test::with('student','observer')->findOrFail($testId);
        if (!$user || $user->role !== 'teacher' || $test->observer?->role !== 'teacher' || $test->observer_id !== $user->id || $test->status !== 'in_progress') {
            return redirect()->route('index');
        }
        $domain = Domain::with('questions')->findOrFail($domainId);
        $order = Session::get("teacher_test_order_$testId");
        if (!$order) {
            $order = $test->question_order ? json_decode($test->question_order, true) : null;
        }
        if (!$order || !isset($order[$domainId])) {
            $ids = $domain->questions->pluck('id')->all();
            shuffle($ids);
            $order = is_array($order) ? $order : [];
            $order[$domainId] = $ids;
            Session::put("teacher_test_order_$testId", $order);
            DB::table('tests')->where('id', $testId)->update(['question_order' => json_encode($order)]);
        }
        $ids = $order[$domainId];
        if (!isset($ids[$index])) {
            $allDomains = array_keys($order);
            $pos = array_search($domainId, $allDomains);
            $nextDomainId = $allDomains[$pos + 1] ?? null;
            if ($nextDomainId) {
                return redirect()->route('teacher.tests.question', [$testId, $nextDomainId, 0]);
            }
            return redirect()->route('teacher.tests.result', [$testId]);
        }
        $question = Question::findOrFail($ids[$index]);
        return view('teacher.test_question', compact('test','domain','question','index'));
    }

    public function submitQuestion(Request $request, $testId, $domainId, $index)
    {
        $validated = $request->validate(['answer' => 'required|in:yes,no,na']);
        $user = Auth::user();
        $test = Test::with('observer')->findOrFail($testId);
        if (!$user || $user->role !== 'teacher' || $test->observer?->role !== 'teacher' || $test->observer_id !== $user->id || $test->status !== 'in_progress') {
            return redirect()->route('index');
        }
        $domain = Domain::findOrFail($domainId);
        $order = Session::get("teacher_test_order_$testId");
        $questionId = $order[$domainId][$index] ?? null;
        if (!$questionId) { return redirect()->route('teacher.tests.question', [$testId, $domainId, $index]); }

        $score = null;
        if ($validated['answer'] === 'yes') { $score = 1.0; }
        if ($validated['answer'] === 'no') { $score = 0.0; }

        DB::table('test_responses')->updateOrInsert([
            'test_id' => $test->id,
            'question_id' => $questionId,
        ], [
            'score' => $score,
            'comment' => null,
            'updated_at' => now(),
        ]);

        $nextIndex = (int)$index + 1;
        return redirect()->route('teacher.tests.question', [$testId, $domainId, $nextIndex]);
    }

    public function result($testId)
    {
        $user = Auth::user();
        $test = Test::with(['student','observer','responses.question.domain'])->findOrFail($testId);
        if (!$user || $user->role !== 'teacher' || $test->observer?->role !== 'teacher' || $test->observer_id !== $user->id) {
            return redirect()->route('index');
        }
        if (in_array($test->status, ['cancelled','terminated','pending','in_progress'])) {
            return redirect()->route('teacher.student', $test->student_id);
        }
        $domains = Domain::with('questions')->orderBy('id')->get();

        // Enforce completeness: all questions (across domains) must have a response (yes/no/na)
        $totalQuestions = $domains->sum(fn($d) => $d->questions->count());
        $answeredCount = $test->responses->filter(fn($r)=> $r->score !== null)->count(); // count only answered (yes/no); NA is null
        if ($answeredCount < $totalQuestions) {
            // Find first unanswered question using stored order, else rebuild
            $order = $test->question_order ? json_decode($test->question_order, true) : null;
            if (!$order) {
                $order = [];
                foreach ($domains as $d) {
                    $ids = $d->questions->pluck('id')->all();
                    shuffle($ids);
                    $order[$d->id] = $ids;
                }
                // Persist order for consistency
                \Illuminate\Support\Facades\DB::table('tests')->where('id', $test->id)->update(['question_order' => json_encode($order)]);
            }
            foreach ($order as $domainId => $qIds) {
                foreach ($qIds as $idx => $qid) {
                    $hasResp = $test->responses->firstWhere('question_id', $qid) !== null;
                    if (!$hasResp) {
                        return redirect()->route('teacher.tests.question', [$test->id, $domainId, $idx]);
                    }
                }
            }
            // Fallback: if order mismatched, redirect to first domain/question
            $firstDomain = $domains->first();
            return redirect()->route('teacher.tests.question', [$test->id, $firstDomain->id, 0]);
        }

        foreach ($domains as $d) {
            $qIds = $d->questions->pluck('id')->all();
            $responses = $test->responses->whereIn('question_id', $qIds);
            $applicable = $responses->filter(fn($r) => $r->score !== null);
            $yesCount = $applicable->filter(fn($r) => (float)$r->score === 1.0)->count();
            $totalApplicable = max(1, $applicable->count());
            $raw = (float)$yesCount;
            $pct = ($yesCount / $totalApplicable) * 100.0;
            $scaled = \App\Services\EccdScoring::percentageToScaled($pct);
            $based = (float)$totalApplicable;

            DB::table('domain_scores')->updateOrInsert([
                'test_id' => $test->id,
                'domain_id' => $d->id,
            ], [
                'raw_score' => $raw,
                'scaled_score' => $scaled,
                'scaled_score_based' => $based,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $test->load(['scores.domain']);
        $sumScaled = $test->scores->sum('scaled_score');
        $standardScore = EccdScoring::deriveStandardScore((float)$sumScaled, $domains->count());

        // Determine 6-month window bounds anchored at enrollment
        $enroll = $test->student->enrollment_date ? \Illuminate\Support\Carbon::parse($test->student->enrollment_date) : null;
        $anchor = $enroll ?: \Illuminate\Support\Carbon::parse($test->student->tests()->orderBy('test_date','asc')->value('test_date') ?? $test->test_date);
        $tDate = \Illuminate\Support\Carbon::parse($test->test_date);
        $months = max(0, $anchor->diffInMonths($tDate));
        $windowIdx = intdiv($months, 6);
        $windowStart = $anchor->copy()->addMonths($windowIdx * 6)->startOfDay();
        $windowEnd = $windowStart->copy()->addMonths(6)->subDay();

        // Collect tests in the same 6-month window (excluding non-completed)
        $windowTests = $test->student->tests()->with(['scores','observer'])
            ->where('assessment_period_id', $test->assessment_period_id)
            ->finalized()
            ->get();

        $aggregates = EccdScoring::aggregateByRole($windowTests, $domains);
        $discrepancies = EccdScoring::analyzeDiscrepancies($aggregates['teacher'], $aggregates['family'], $domains);
        $familyOnly = $windowTests->filter(fn($t) => $t->observer?->role === 'family')->isNotEmpty()
            && $windowTests->filter(fn($t) => $t->observer?->role === 'teacher')->isEmpty();
        $allNA = $test->isAllNA();

        return view('teacher.test_result', compact('test','domains','sumScaled','standardScore','windowStart','windowEnd','aggregates','discrepancies','familyOnly','allNA'));
    }

    public function finalize($testId)
    {
        $user = Auth::user();
        $test = Test::with(['observer','responses.question.domain'])->findOrFail($testId);
        if (!$user || $user->role !== 'teacher' || $test->observer?->role !== 'teacher' || $test->observer_id !== $user->id) { return redirect()->route('index'); }
        // Ensure teacher is still assigned to the student
        // Verify active assignment using explicit pivot column
        $assigned = $test->student->teachers()
            ->where('teacher_id', $user->id)
            ->where('teacher_student.status', 'active')
            ->exists();
        if (!$assigned) {
            session()->flash('error', 'You are no longer assigned to this student. Your draft has been terminated.');
            $test->status = 'terminated';
            $test->save();
            return redirect()->route('teacher.index');
        }
        // Enforce completeness before allowing finalize
        $domains = Domain::with('questions')->orderBy('id')->get();
        $totalQuestions = $domains->sum(fn($d) => $d->questions->count());
        $answeredCount = $test->responses->filter(fn($r)=> $r->score !== null)->count();
        if ($answeredCount < $totalQuestions) {
            // Redirect to next unanswered question
            $order = $test->question_order ? json_decode($test->question_order, true) : null;
            if ($order) {
                foreach ($order as $domainId => $qIds) {
                    foreach ($qIds as $idx => $qid) {
                        $hasResp = $test->responses->firstWhere('question_id', $qid) !== null;
                        if (!$hasResp) {
                            return redirect()->route('teacher.tests.question', [$test->id, $domainId, $idx]);
                        }
                    }
                }
            }
            $firstDomain = $domains->first();
            return redirect()->route('teacher.tests.question', [$test->id, $firstDomain->id, 0]);
        }
        if (!\Illuminate\Support\Facades\Gate::allows('finalize', $test)) {
            session()->flash('error', 'Cannot finalize: eligibility or completeness not met.');
            return redirect()->route('teacher.student', $test->student_id);
        }
        $test->submitted_by = 'teacher';
        $test->submitted_at = now();
        $test->status = 'finalized';
        $test->save();
        return redirect()->route('teacher.tests.result', $test->id);
    }

    public function markIncomplete($testId)
    {
        $user = Auth::user();
        $test = Test::with('observer')->findOrFail($testId);
        if (!$user || $user->role !== 'teacher' || $test->observer?->role !== 'teacher' || $test->observer_id !== $user->id) { return redirect()->route('index'); }
        $test->status = 'paused';
        $test->save();
        return redirect()->route('teacher.student', $test->student_id);
    }

    public function cancel($testId)
    {
        $user = Auth::user();
        $test = Test::with('observer')->findOrFail($testId);
        if (!$user || $user->role !== 'teacher' || $test->observer?->role !== 'teacher' || $test->observer_id !== $user->id) { return redirect()->route('index'); }
        $test->status = 'cancelled';
        $test->save();
        return redirect()->route('teacher.student', $test->student_id);
    }

    public function terminate($testId)
    {
        $user = Auth::user();
        $test = Test::with('observer')->findOrFail($testId);
        if (!$user || $user->role !== 'teacher' || $test->observer?->role !== 'teacher' || $test->observer_id !== $user->id) { return redirect()->route('index'); }
        $test->status = 'terminated';
        $test->save();
        return redirect()->route('teacher.student', $test->student_id);
    }

    public function pause($testId)
    {
        $user = Auth::user();
        $test = Test::with('observer')->findOrFail($testId);
        if (!$user || $user->role !== 'teacher' || $test->observer?->role !== 'teacher' || $test->observer_id !== $user->id) { return redirect()->route('index'); }
        // Keep status as in_progress; simply return to dashboard
        if ($test->status !== 'in_progress') {
            $test->status = 'in_progress';
            $test->save();
        }
        return redirect()->route('teacher.index');
    }
}
