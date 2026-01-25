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

        $currentMonth = now()->format('Y-m');
        $status = [];
        foreach ($students as $s) {
            $latest = $s->tests()->orderBy('test_date', 'desc')->first();
            $hasThisMonth = $s->tests()->whereBetween('test_date', [now()->startOfMonth(), now()->endOfMonth()])->exists();
            $status[$s->id] = [
                'has_test_this_month' => $hasThisMonth,
                'latest' => $latest,
            ];
        }

        return view('family.dashboard', compact('family','students','status','currentMonth'));
    }

    public function child($studentId)
    {
        $userId = Auth::id();
        $family = Family::where('user_id', $userId)->firstOrFail();
        $student = Student::with(['family','tests.scores.domain','tests.responses.question'])->where('family_id', $family->id)->findOrFail($studentId);

        $domains = Domain::orderBy('name')->get();
        $tests = $student->tests()->with(['scores','responses'])->orderBy('test_date','desc')->take(6)->get();
        $summary = EccdScoring::summarize($tests, $domains);

        return view('family.child', compact('student','domains','tests','summary'));
    }

    public function startTest($studentId)
    {
        $userId = Auth::id();
        $family = Family::where('user_id', $userId)->firstOrFail();
        $student = Student::where('family_id', $family->id)->findOrFail($studentId);

        // Reuse any test already created today to avoid duplicates
        $existingToday = $student->tests()->whereDate('test_date', now()->toDateString())->first();
        if ($existingToday) {
            $firstDomain = Domain::orderBy('id')->first();
            return redirect()->route('family.tests.question', [$existingToday->id, $firstDomain->id, 0]);
        }

        // Otherwise, enforce one test per month for family
        $existingThisMonth = $student->tests()->whereBetween('test_date', [now()->startOfMonth(), now()->endOfMonth()])->first();
        if ($existingThisMonth) {
            $firstDomain = Domain::orderBy('id')->first();
            return redirect()->route('family.tests.question', [$existingThisMonth->id, $firstDomain->id, 0]);
        }

        $testId = DB::table('tests')->insertGetId([
            'student_id' => $student->id,
            'observer_id' => $userId,
            'test_date' => now()->toDateString(),
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

        $firstDomain = $domains->first();
        return redirect()->route('family.tests.question', [$testId, $firstDomain->id, 0]);
    }

    public function showQuestion($testId, $domainId, $index)
    {
        $test = Test::with('student')->findOrFail($testId);
        $domain = Domain::with('questions')->findOrFail($domainId);
        $order = Session::get("test_order_$testId");
        if (!$order || !isset($order[$domainId])) {
            // Rebuild deterministic order if missing
            $ids = $domain->questions->pluck('id')->all();
            shuffle($ids);
            $order[$domainId] = $ids;
            Session::put("test_order_$testId", $order);
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

        return view('family.test_question', compact('test','domain','question','index'));
    }

    public function submitQuestion(Request $request, $testId, $domainId, $index)
    {
        $validated = $request->validate([
            'answer' => 'required|in:yes,no,na',
        ]);
        $test = Test::findOrFail($testId);
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
        $test = Test::with(['student','responses.question.domain'])->findOrFail($testId);
        $domains = Domain::with('questions')->orderBy('id')->get();

        // Compute per-domain raw and scaled scores
        foreach ($domains as $d) {
            $qIds = $d->questions->pluck('id')->all();
            $responses = $test->responses->whereIn('question_id', $qIds);
            $applicable = $responses->filter(function($r){ return $r->score !== null; });
            $yesCount = $applicable->filter(function($r){ return (float)$r->score === 1.0; })->count();
            $totalApplicable = max(1, $applicable->count());
            $raw = (float)$yesCount;
            $scaled = round(($yesCount / $totalApplicable) * 100.0, 2);
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
        $standardScore = EccdScoring::deriveStandardScore((float)$sumScaled);

        // Mark submitted
        if (!$test->submitted_at) {
            $test->submitted_by = 'family';
            $test->submitted_at = now();
            $test->status = 'completed';
            $test->save();
        }

        return view('family.test_result', compact('test','domains','sumScaled','standardScore'));
    }
}
