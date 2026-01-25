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
        foreach ($students as $s) {
            // Last teacher-run test
            $latestTeacherTest = $s->tests()->whereHas('observer', function($q){ $q->where('role', 'teacher'); })
                ->orderBy('test_date','desc')->first();
            $monthsSince = $latestTeacherTest ? \Illuminate\Support\Carbon::parse($latestTeacherTest->test_date)->diffInMonths(now()) : null;
            $eligible = $monthsSince === null || $monthsSince >= 6;
            $status[$s->id] = [
                'latest_teacher' => $latestTeacherTest,
                'eligible' => $eligible,
            ];
        }

        return view('teacher.dashboard', compact('teacher','students','status'));
    }

    public function student($studentId)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'teacher') { return redirect()->route('index'); }
        $teacher = Teacher::firstOrCreate(['id' => $user->id], ['hire_date' => null, 'status' => 'active']);
        $student = Student::with(['family','section','teachers.user','tests.scores.domain','tests.responses'])
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
            return round($sel->map(fn($t) => optional($t->scores->firstWhere('domain_id',$domainId))->scaled_score)
                ->filter()->avg() ?? 0, 2);
        };

        return view('teacher.student', compact('student','domains','tests','teacherTests','familyTests','avg'));
    }

    public function startTest($studentId)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'teacher') { return redirect()->route('index'); }
        $teacher = Teacher::firstOrCreate(['id' => $user->id], ['hire_date' => null, 'status' => 'active']);
        $student = Student::whereHas('teachers', function($q) use ($teacher){ $q->where('teachers.id', $teacher->id); })->findOrFail($studentId);

        // If a test already exists today for this student, avoid duplicate insert
        $existingToday = $student->tests()->whereDate('test_date', now()->toDateString())->first();
        if ($existingToday) {
            // If it's a teacher-run test, resume it; otherwise go back to student page
            if ($existingToday->observer?->role === 'teacher') {
                $firstDomain = Domain::orderBy('id')->first();
                return redirect()->route('teacher.tests.question', [$existingToday->id, $firstDomain->id, 0]);
            }
            return redirect()->route('teacher.student', $student->id);
        }

        // Enforce 6-month interval for teacher tests
        $lastTeacher = $student->tests()->whereHas('observer', function($q){ $q->where('role','teacher'); })
            ->orderBy('test_date','desc')->first();
        if ($lastTeacher) {
            $months = \Illuminate\Support\Carbon::parse($lastTeacher->test_date)->diffInMonths(now());
            if ($months < 6) {
                return redirect()->route('teacher.student', $student->id);
            }
        }

        $testId = DB::table('tests')->insertGetId([
            'student_id' => $student->id,
            'observer_id' => $user->id,
            'test_date' => now()->toDateString(),
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

        $firstDomain = $domains->first();
        return redirect()->route('teacher.tests.question', [$testId, $firstDomain->id, 0]);
    }

    public function showQuestion($testId, $domainId, $index)
    {
        $test = Test::with('student','observer')->findOrFail($testId);
        if ($test->observer?->role !== 'teacher') { return redirect()->route('index'); }
        $domain = Domain::with('questions')->findOrFail($domainId);
        $order = Session::get("teacher_test_order_$testId");
        if (!$order || !isset($order[$domainId])) {
            $ids = $domain->questions->pluck('id')->all();
            shuffle($ids);
            $order[$domainId] = $ids;
            Session::put("teacher_test_order_$testId", $order);
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
        $test = Test::with('observer')->findOrFail($testId);
        if ($test->observer?->role !== 'teacher') { return redirect()->route('index'); }
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
        $test = Test::with(['student','observer','responses.question.domain'])->findOrFail($testId);
        if ($test->observer?->role !== 'teacher') { return redirect()->route('index'); }
        $domains = Domain::with('questions')->orderBy('id')->get();

        foreach ($domains as $d) {
            $qIds = $d->questions->pluck('id')->all();
            $responses = $test->responses->whereIn('question_id', $qIds);
            $applicable = $responses->filter(fn($r) => $r->score !== null);
            $yesCount = $applicable->filter(fn($r) => (float)$r->score === 1.0)->count();
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

        $test->load(['scores.domain']);
        $sumScaled = $test->scores->sum('scaled_score');
        $standardScore = EccdScoring::deriveStandardScore((float)$sumScaled);
        if (!$test->submitted_at) {
            $test->submitted_by = 'teacher';
            $test->submitted_at = now();
            $test->status = 'completed';
            $test->save();
        }

        return view('teacher.test_result', compact('test','domains','sumScaled','standardScore'));
    }
}
