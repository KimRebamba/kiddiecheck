<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Family;
use App\Models\Student;
use App\Models\Test;
use App\Models\TestResponse;
use App\Models\Domain;
use App\Models\Question;
use App\Models\DomainScore;
use App\Services\EccdScoring;

class FamilyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if ($user->role !== 'family') {
            return redirect()->route('index');
        }
        $userId = $user->id;
        
        $family = Family::firstOrCreate(
            ['user_id' => $userId],
            ['name' => $user->name . "'s Family", 'home_address' => '']
        );
        $family->load('students.tests');
        $students = $family->students;
        $longitudinals = [];

        $currentMonth = now()->format('Y-m');
        $status = [];
        foreach ($students as $s) {
            $latest = $s->tests()->orderBy('test_date', 'desc')->first();
            $familyHasThisMonth = $s->tests()
                ->whereBetween('test_date', [now()->startOfMonth(), now()->endOfMonth()])
                ->whereHas('observer', function($q){ $q->where('role','family'); })
                ->exists();
            $latestFamily = $s->tests()
                ->whereHas('observer', function($q){ $q->where('role','family'); })
                ->orderBy('test_date','desc')
                ->first();
            $status[$s->id] = [
                'family_has_test_this_month' => $familyHasThisMonth,
                'latest' => $latest,
                'latest_family' => $latestFamily,
            ];
            $longitudinals[$s->id] = \App\Services\AssessmentLongitudinal::summarize($s)['longitudinal'] ?? null;
        }

        return view('family.dashboard', compact('family','students','status','currentMonth','longitudinals'));
    }

    public function child($studentId)
    {
        $userId = Auth::id();
        $family = Family::where('user_id', $userId)->firstOrFail();
        $student = Student::with(['family','tests' => function($q){ $q->finalized(); }, 'tests.scores.domain','tests.responses','tests.observer','assessmentPeriods'])
            ->where('family_id', $family->id)->findOrFail($studentId);

        $domains = Domain::orderBy('name')->get();
        $tests = $student->tests()->with(['scores','responses','observer'])->finalized()->orderBy('test_date','desc')->take(6)->get();
        $summary = EccdScoring::summarize($tests, $domains);

        return view('family.child', compact('student','domains','tests','summary'));
    }

    public function startTest($studentId)
    {
        $userId = Auth::id();
        $family = Family::where('user_id', $userId)->firstOrFail();
        $student = Student::where('family_id', $family->id)->with('assessmentPeriods')->findOrFail($studentId);

        // Prevent concurrent in-progress FAMILY tests for the same student
        $hasInProgress = $student->tests()->where('status', 'in_progress')
            ->whereHas('observer', function($q){ $q->where('role','family'); })
            ->exists();
        if ($hasInProgress) {
            return redirect()->route('family.child', $student->id);
        }

        // Enforce overall limit: max 3 non-cancelled/terminated tests
        $activeCount = $student->tests()->whereNotIn('status', ['cancelled','terminated'])->count();
        if ($activeCount >= 3) {
            return redirect()->route('family.child', $student->id);
        }

        // Determine current active assessment period
        $now = now();
        $period = $student->assessmentPeriods->first(function($p) use ($now){ return $now->between($p->starts_at, $p->ends_at); });
        if (!$period) { return redirect()->route('family.child', $student->id); }
        $windowStart = \Illuminate\Support\Carbon::parse($period->starts_at)->startOfDay();
        $windowEnd = \Illuminate\Support\Carbon::parse($period->ends_at)->endOfDay();

        // Enforce one test per assessment period for this family (non-cancelled/terminated)
        $hasThisPeriod = $student->tests()->where('assessment_period_id', $period->id)
            ->whereHas('observer', function($q){ $q->where('role','family'); })
            ->whereNotIn('status', ['cancelled','terminated'])
            ->exists();
        if ($hasThisPeriod) { return redirect()->route('family.child', $student->id); }

        // Reuse the scheduled pending test for this window if present
        $scheduled = $student->tests()->where('assessment_period_id', $period->id)->where('status','pending')->first();
        if ($scheduled && $now->betweenIncluded($windowStart, $windowEnd)) {
            $scheduled->observer_id = $userId;
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
            Session::put("test_order_{$scheduled->id}", $order);
            // Seed responses for all questions with score = null
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
            return redirect()->route('family.tests.question', [$scheduled->id, $firstDomain->id, 0]);
        }

        // Enforce 6-month interval relative to last non-cancelled FAMILY test
        $lastActive = $student->tests()
            ->whereNotIn('status',['cancelled','terminated'])
            ->whereHas('observer', function($q){ $q->where('role','family'); })
            ->orderBy('test_date','desc')
            ->first();
        if ($lastActive) {
            $months = \Illuminate\Support\Carbon::parse($lastActive->test_date)->diffInMonths(now());
            if ($months < 6) {
                session()->flash('error', 'Family tests are limited to one every 6 months.');
                return redirect()->route('family.child', $student->id);
            }
        }

        // Only allow ad-hoc start if within the current window and no scheduled test exists (fallback)
        if (!$now->betweenIncluded($windowStart, $windowEnd)) {
            return redirect()->route('family.child', $student->id);
        }
        // Find an available date within the window (prefer today)
        $used = $student->tests()->whereBetween('test_date', [$windowStart->toDateString(), $windowEnd->toDateString()])->pluck('test_date')->map(fn($d)=> (string)$d)->toArray();
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
            if ($found) { $candidate = $found; } else { return redirect()->route('family.child', $student->id); }
        }

        $testId = DB::table('tests')->insertGetId([
            'student_id' => $student->id,
            'assessment_period_id' => $period->id,
            'observer_id' => $userId,
            'test_date' => $candidate,
            'status' => 'in_progress',
            'started_at' => now(),
            'submitted_by' => null,
            'submitted_at' => null,
        ]);

        // Prepare randomized order per domain in session
        $domains = Domain::with('questions')->orderBy('id')->get();
        $order = [];
        foreach ($domains as $d) {
            $ids = $d->questions->pluck('id')->all();
            shuffle($ids);
            $order[$d->id] = $ids;
        }
        Session::put("test_order_$testId", $order);
        DB::table('tests')->where('id', $testId)->update(['question_order' => json_encode($order)]);

        $firstDomain = $domains->first();
        // Seed responses for all questions with score = null
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
        return redirect()->route('family.tests.question', [$testId, $firstDomain->id, 0]);
    }

    public function showQuestion($testId, $domainId, $index)
    {
        $user = Auth::user();
        $test = Test::with(['student','observer','responses'])->findOrFail($testId);
        // Strictness: Only allow answering when current user is family and owns this in-progress test
        if (!$user || $user->role !== 'family' || ($test->observer?->role ?? null) !== 'family' || $test->observer_id !== $user->id || $test->status !== 'in_progress') {
            session()->flash('error', 'Only in-progress family tests can be answered by the family who started them.');
            return redirect()->route('index');
        }
        $domain = Domain::with('questions')->findOrFail($domainId);
        $order = Session::get("test_order_$testId");
        if (!$order) {
            $order = $test->question_order ? json_decode($test->question_order, true) : null;
        }
        if (!$order || !isset($order[$domainId])) {
            // Rebuild deterministic order if missing
            $ids = $domain->questions->pluck('id')->all();
            shuffle($ids);
            $order = is_array($order) ? $order : [];
            $order[$domainId] = $ids;
            Session::put("test_order_$testId", $order);
            DB::table('tests')->where('id', $testId)->update(['question_order' => json_encode($order)]);
        }
        $ids = $order[$domainId];
        if (!isset($ids[$index])) {
            // Move to next domain or finish
            $allDomains = array_keys($order);
            $pos = array_search($domainId, $allDomains);
            $nextDomainId = $allDomains[$pos + 1] ?? null;
            if ($nextDomainId) {
                return redirect()->route('family.tests.question', [$testId, $nextDomainId, 0]);
            }
            return redirect()->route('family.tests.result', [$testId]);
        }
        $question = Question::findOrFail($ids[$index]);

        $allDomains = Domain::with('questions')->orderBy('id')->get();
        $totalQuestions = $allDomains->sum(fn($d) => $d->questions->count());
        $answeredCount = $test->responses->count();
        $progressPct = $totalQuestions ? round(($answeredCount / max(1, $totalQuestions)) * 100) : null;

        return view('family.test_question', compact('test','domain','question','index','totalQuestions','answeredCount','progressPct'));
    }

    public function submitQuestion(Request $request, $testId, $domainId, $index)
    {
        $validated = $request->validate([
            'answer' => 'required|in:yes,no,na',
        ]);
        $user = Auth::user();
        $test = Test::with('observer')->findOrFail($testId);
        if (!$user || $user->role !== 'family' || ($test->observer?->role ?? null) !== 'family' || $test->observer_id !== $user->id || $test->status !== 'in_progress') {
            session()->flash('error', 'Only in-progress family tests can be answered by the family who started them.');
            return redirect()->route('index');
        }
        $domain = Domain::findOrFail($domainId);
        $order = Session::get("test_order_$testId");
        $questionId = $order[$domainId][$index] ?? null;
        if (!$questionId) {
            return redirect()->route('family.tests.question', [$testId, $domainId, $index]);
        }

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
        return redirect()->route('family.tests.question', [$testId, $domainId, $nextIndex]);
    }

    public function result($testId)
    {
        $user = Auth::user();
        $test = Test::with(['student','observer','responses.question.domain'])->findOrFail($testId);
        // Disallow results for cancelled/terminated/incomplete/pending tests and enforce ownership
        if (!$user || $user->role !== 'family' || ($test->observer?->role ?? null) !== 'family' || $test->observer_id !== $user->id) { return redirect()->route('index'); }
        if (in_array($test->status, ['cancelled','terminated','pending'])) {
            session()->flash('error', 'Results are available only for completed tests.');
            return redirect()->route('family.child', $test->student_id);
        }
        $domains = Domain::with('questions')->orderBy('id')->get();

        // Enforce completeness: all questions must have a response (yes/no/na)
        $totalQuestions = $domains->sum(fn($d) => $d->questions->count());
        $answeredCount = $test->responses->filter(fn($r)=> $r->score !== null)->count();
        if ($answeredCount < $totalQuestions) {
            $order = $test->question_order ? json_decode($test->question_order, true) : null;
            if (!$order) {
                $order = [];
                foreach ($domains as $d) {
                    $ids = $d->questions->pluck('id')->all();
                    shuffle($ids);
                    $order[$d->id] = $ids;
                }
                \Illuminate\Support\Facades\DB::table('tests')->where('id', $test->id)->update(['question_order' => json_encode($order)]);
            }
            foreach ($order as $domainId => $qIds) {
                foreach ($qIds as $idx => $qid) {
                    $hasResp = $test->responses->firstWhere('question_id', $qid) !== null;
                    if (!$hasResp) {
                        return redirect()->route('family.tests.question', [$test->id, $domainId, $idx]);
                    }
                }
            }
            $firstDomain = $domains->first();
            return redirect()->route('family.tests.question', [$test->id, $firstDomain->id, 0]);
        }
        // Compute per-domain raw and scaled scores
        foreach ($domains as $d) {
            $qIds = $d->questions->pluck('id')->all();
            $responses = $test->responses->whereIn('question_id', $qIds);
            $applicable = $responses->filter(function($r){ return $r->score !== null; });
            $yesCount = $applicable->filter(function($r){ return (float)$r->score === 1.0; })->count();
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

        // Reload test with scores
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

        // Collect tests in the same 6-month window (completed only)
        $windowTests = $test->student->tests()->with(['scores','observer'])
            ->where('assessment_period_id', $test->assessment_period_id)
            ->finalized()
            ->get();

        $aggregates = EccdScoring::aggregateByRole($windowTests, $domains);
        $discrepancies = EccdScoring::analyzeDiscrepancies($aggregates['teacher'], $aggregates['family'], $domains);
        $familyOnly = $windowTests->filter(fn($t) => $t->observer?->role === 'family')->isNotEmpty()
            && $windowTests->filter(fn($t) => $t->observer?->role === 'teacher')->isEmpty();
        $allNA = $test->isAllNA();

        return view('family.test_result', compact('test','domains','sumScaled','standardScore','windowStart','windowEnd','aggregates','discrepancies','familyOnly','allNA'));
    }

    public function finalize($testId)
    {
        $user = Auth::user();
        $test = Test::with(['observer','responses.question.domain'])->findOrFail($testId);
        if (!$user || $user->role !== 'family' || ($test->observer?->role ?? null) !== 'family' || $test->observer_id !== $user->id || $test->status !== 'in_progress') {
            return redirect()->route('index');
        }
        // Enforce completeness before allowing finalize
        $domains = Domain::with('questions')->orderBy('id')->get();
        $totalQuestions = $domains->sum(fn($d) => $d->questions->count());
        $answeredCount = $test->responses->filter(fn($r)=> $r->score !== null)->count();
        if ($answeredCount < $totalQuestions) {
            $order = $test->question_order ? json_decode($test->question_order, true) : null;
            if ($order) {
                foreach ($order as $domainId => $qIds) {
                    foreach ($qIds as $idx => $qid) {
                        $hasResp = $test->responses->firstWhere('question_id', $qid) !== null;
                        if (!$hasResp) {
                            return redirect()->route('family.tests.question', [$test->id, $domainId, $idx]);
                        }
                    }
                }
            }
            $firstDomain = $domains->first();
            return redirect()->route('family.tests.question', [$test->id, $firstDomain->id, 0]);
        }
        if (!\Illuminate\Support\Facades\Gate::allows('finalize', $test)) {
            session()->flash('error', 'Cannot finalize: eligibility or completeness not met.');
            return redirect()->route('family.child', $test->student_id);
        }
        $test->submitted_by = 'family';
        $test->submitted_at = now();
        $test->status = 'finalized';
        $test->save();
        return redirect()->route('family.tests.result', $test->id);
    }

    public function markIncomplete($testId)
    {
        $user = Auth::user();
        $test = Test::with('observer')->findOrFail($testId);
        if (!$user || $user->role !== 'family' || ($test->observer?->role ?? null) !== 'family' || $test->observer_id !== $user->id) {
            return redirect()->route('index');
        }
        $test->status = 'paused';
        $test->save();
        session()->flash('success', 'Test marked incomplete.');
        return redirect()->route('family.child', $test->student_id);
    }

    public function cancel($testId)
    {
        $user = Auth::user();
        $test = Test::with('observer')->findOrFail($testId);
        if (!$user || $user->role !== 'family' || ($test->observer?->role ?? null) !== 'family' || $test->observer_id !== $user->id) {
            return redirect()->route('index');
        }
        $test->status = 'cancelled';
        $test->save();
        session()->flash('success', 'Test cancelled.');
        return redirect()->route('family.child', $test->student_id);
    }

    public function terminate($testId)
    {
        $user = Auth::user();
        $test = Test::with('observer')->findOrFail($testId);
        if (!$user || $user->role !== 'family' || ($test->observer?->role ?? null) !== 'family' || $test->observer_id !== $user->id) {
            return redirect()->route('index');
        }
        $test->status = 'terminated';
        $test->save();
        session()->flash('success', 'Test terminated.');
        return redirect()->route('family.child', $test->student_id);
    }

    public function pause($testId)
    {
        $user = Auth::user();
        $test = Test::with('observer')->findOrFail($testId);
        if (!$user || $user->role !== 'family' || ($test->observer?->role ?? null) !== 'family' || $test->observer_id !== $user->id || $test->status !== 'in_progress') {
            session()->flash('error', 'Only in-progress family tests can be paused by the family who started them.');
            return redirect()->route('index');
        }
        // Keep status as in_progress; simply return to dashboard
        session()->flash('success', 'Progress saved. You can continue later.');
        return redirect()->route('family.index');
    }
}
