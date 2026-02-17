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
use App\Models\AssessmentPeriod;
use App\Models\Section;
use App\Models\Family;
use App\Models\TestResponse;
use App\Models\PeriodSummaryScore;
use App\Services\EccdScoring;
use Illuminate\Support\Facades\Log;

class TeacherController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        $teacherId = $teacher->id ?? $teacher->user_id;
        
        // Get all students assigned to this teacher
        $students = Student::whereHas('teachers', function($q) use ($teacherId) {
            $q->where('user_id', $teacherId);
        })->get();

        // Build status array for each student
        $status = [];
        $longitudinals = [];

        foreach ($students as $student) {
            $tests = Test::where('student_id', $student->student_id)
                ->where('examiner_id', $teacherId)
                ->orderBy('test_date', 'desc')
                ->get();

            $inProgressTest = $tests->firstWhere('status', 'in_progress');
            $latestTest = $tests->first();

            // Check if student is eligible for a new test
            $latestCompleted = $tests->firstWhere('status', 'completed');
            $eligible = false;

            if (!$latestCompleted) {
                // Check if student has any non-overdue assessment periods
                $eligible = $student->assessmentPeriods()
                    ->where('status', '!=', 'overdue')
                    ->where('status', '!=', 'completed')
                    ->exists();
            } else {
                // Eligible 6 months after last completed test AND has non-overdue periods
                $sixMonthsAgo = now()->subMonths(6);
                $sixMonthsPassed = $latestCompleted->test_date <= $sixMonthsAgo;
                $hasNonOverduePeriods = $student->assessmentPeriods()
                    ->where('status', '!=', 'overdue')
                    ->where('status', '!=', 'completed')
                    ->exists();
                $eligible = $sixMonthsPassed && $hasNonOverduePeriods;
            }

            $status[$student->student_id] = [
                'in_progress' => $inProgressTest,
                'latest_teacher' => $latestTest,
                'eligible' => $eligible,
            ];

            // Get longitudinal scores (tests at 6m, 12m, 18m intervals)
            $longitudinals[$student->student_id] = [];
            foreach ($tests->where('status', 'completed') as $test) {
                $periods = $student->assessmentPeriods()->orderBy('start_date')->get();
                foreach ($periods as $idx => $period) {
                    if ($period->tests->contains('test_id', $test->test_id)) {
                        // Get standard score from test_standard_scores table
                        $standardScore = DB::table('test_standard_scores')
                            ->where('test_id', $test->test_id)
                            ->value('standard_score');
                        
                        if ($standardScore) {
                            $longitudinals[$student->student_id][$idx + 1] = [
                                'standardScore' => $standardScore,
                            ];
                        }
                    }
                }
            }
        }

        return view('teacher.dashboard', compact('teacher', 'students', 'status', 'longitudinals'));
    }

    // Family Page
    public function family()
    {
        $teacher = Auth::user();
        
        // Get families of students assigned to this teacher
        $families = Family::whereHas('students.teachers', function($q) use ($teacher) {
            $q->where('user_id', $teacher->id);
        })->with(['user', 'students' => function($q) use ($teacher) {
            $q->whereHas('teachers', function($t) use ($teacher) {
                $t->where('user_id', $teacher->id);
            });
        }])->get();

        return view('teacher.family', compact('families'));
    }

    public function familyShow($familyId)
    {
        $teacher = Auth::user();
        $family = Family::with(['user', 'students' => function($q) use ($teacher) {
            $q->whereHas('teachers', function($t) use ($teacher) {
                $t->where('user_id', $teacher->id);
            });
        }])->findOrFail($familyId);

        return view('teacher.family_show', compact('family'));
    }

    // Sections Page
    public function sections()
    {
        $teacher = Auth::user();
        $teacherId = $teacher->id ?? $teacher->user_id;
        
        // Get sections with correct column names
        $sections = DB::table('sections')
            ->select('id', 'name', 'description', 'created_at', 'updated_at')
            ->get();

        // Add student count manually for each section
        $sections = $sections->map(function($section) use ($teacherId) {
            $studentCount = DB::table('students')
                ->join('student_teacher', 'students.student_id', '=', 'student_teacher.student_id')
                ->where('students.section_id', $section->id)
                ->where('student_teacher.teacher_id', $teacherId)
                ->count();
            
            $section->student_count = $studentCount;
            $section->section_id = $section->id; // Add section_id for compatibility
            return $section;
        });

        return view('teacher.sections', compact('sections'));
    }

    public function sectionsShow($sectionId)
    {
        $teacher = Auth::user();
        $teacherId = $teacher->id ?? $teacher->user_id;
        
        // Get section with students using correct column names
        $section = DB::table('sections')
            ->select('id', 'name', 'description', 'created_at', 'updated_at')
            ->where('id', $sectionId)
            ->first();

        if (!$section) {
            abort(404);
        }

        // Add section_id for compatibility
        $section->section_id = $section->id;

        // Get students in this section assigned to this teacher
        $students = DB::table('students')
            ->select('students.*')
            ->join('student_teacher', 'students.student_id', '=', 'student_teacher.student_id')
            ->where('student_teacher.teacher_id', $teacherId)
            ->where('students.section_id', $sectionId)
            ->get();

        // Add eligibility and last standard score for each student
        $students = $students->map(function($student) use ($teacher) {
            $student->age = $student->date_of_birth ? (int) \Carbon\Carbon::parse($student->date_of_birth)->diffInYears(now()) : null;
            $student->eligible = $this->isStudentEligibleForTest((object)$student, $teacher);
            $student->last_standard_score = $this->getLastStandardScore((object)$student);
            return $student;
        });

        return view('teacher.sections_show', compact('section', 'students'));
    }

    // Create Section
    public function sectionsCreate()
    {
        return view('teacher.sections_create');
    }

    public function sectionsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $sectionId = DB::table('sections')->insertGetId([
            'name' => $request->name,
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('teacher.sections')->with('success', 'Section created successfully.');
    }

    // Edit Section
    public function sectionsEdit($sectionId)
    {
        $section = DB::table('sections')->where('section_id', $sectionId)->first();
        
        if (!$section) {
            abort(404);
        }

        return view('teacher.sections_edit', compact('section'));
    }

    public function sectionsUpdate(Request $request, $sectionId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        DB::table('sections')
            ->where('id', $sectionId)
            ->update([
                'name' => $request->name,
                'description' => $request->description,
                'updated_at' => now()
            ]);

        return redirect()->route('teacher.sections')->with('success', 'Section updated successfully.');
    }

    // Delete Section
    public function sectionsDestroy($sectionId)
    {
        // Check if section has students
        $studentCount = DB::table('students')
            ->where('section_id', $sectionId)
            ->count();

        if ($studentCount > 0) {
            return back()->with('error', 'Cannot delete section with assigned students.');
        }

        DB::table('sections')->where('id', $sectionId)->delete();

        return redirect()->route('teacher.sections')->with('success', 'Section deleted successfully.');
    }

    // Reports Page
    public function reports()
    {
        $teacher = Auth::user();
        
        // Get all finalized tests for students assigned to this teacher
        $tests = Test::whereHas('student.teachers', function($q) use ($teacher) {
            $q->where('user_id', $teacher->user_id);
        })
        ->where('status', 'finalized')
        ->with(['student', 'assessmentPeriod'])
        ->orderBy('updated_at', 'desc')
        ->get();

        return view('teacher.reports', compact('tests'));
    }

    public function reportShow($studentId, $periodId)
    {
        $teacher = Auth::user();
        $student = Student::with(['assessmentPeriods'])->findOrFail($studentId);
        $period = AssessmentPeriod::findOrFail($periodId);
        
        // Verify teacher has access
        if (!$student->teachers()->where('user_id', $teacher->user_id)->exists()) {
            abort(403);
        }

        // Get all finalized tests for this period
        $tests = Test::where('student_id', $studentId)
            ->where('assessment_period_id', $periodId)
            ->where('status', 'finalized')
            ->with(['domainScores', 'standardScore'])
            ->get();

        return view('teacher.report_show', compact('student', 'period', 'tests'));
    }

    public function reportDetail($studentId, $periodId, $testId)
    {
        $teacher = Auth::user();
        $student = Student::findOrFail($studentId);
        
        // Verify teacher has access
        if (!$student->teachers()->where('user_id', $teacher->user_id)->exists()) {
            abort(403);
        }

        $test = Test::with(['domainScores', 'standardScore', 'assessmentPeriod', 'student'])->findOrFail($testId);
        $period = AssessmentPeriod::findOrFail($periodId);

        return view('teacher.report_detail', compact('test', 'period', 'student'));
    }

    // Help Page
    public function help()
    {
        return view('teacher.help');
    }

    // Profile Page
    public function profile()
    {
        $teacher = Auth::user()->load('teacher');
        
        return view('teacher.profile', compact('teacher'));
    }

    // Student Detail Page
    public function student($studentId)
    {
        $teacher = Auth::user();
        $student = Student::with(['assessmentPeriods', 'family', 'section'])->findOrFail($studentId);
        
        // Verify teacher has access
        if (!$student->teachers()->where('user_id', $teacher->user_id)->exists()) {
            abort(403);
        }

        $student->age = $student->date_of_birth ? (int)$student->date_of_birth->diffInYears(now()) : null;
        $student->eligible = $this->isStudentEligibleForTest($student, $teacher);
        $student->last_standard_score = $this->getLastStandardScore($student);

        return view('teacher.student', compact('student'));
    }

    // Start Test
    public function startTest(Request $request, $studentId)
    {
        $teacher = Auth::user();
        $student = Student::findOrFail($studentId);
        
        // Verify teacher has access
        if (!$student->teachers()->where('user_id', $teacher->user_id)->exists()) {
            abort(403);
        }

        $periodId = $request->input('period_id');
        
        // Debug: log what we received
        Log::debug('StartTest received:', [
            'periodId' => $periodId,
            'all_inputs' => $request->all(),
            'studentId' => $studentId
        ]);
        
        if (!$periodId) {
            return back()->with('error', 'Period ID is required. Received: ' . json_encode($request->all()));
        }
        
        $period = AssessmentPeriod::findOrFail($periodId);

        // Prevent starting tests for overdue periods
        if ($period->status === 'overdue') {
            return back()->with('error', 'Cannot start test for an overdue assessment period.');
        }

        // Prevent starting tests for completed periods
        if ($period->status === 'completed') {
            return back()->with('error', 'Cannot start test for a completed assessment period.');
        }

        // Get first domain
        $firstDomain = Domain::orderBy('domain_id')->first();
        if (!$firstDomain) {
            return back()->with('error', 'No domains configured in the system.');
        }

        // Check if student already has a test for this period by this teacher
        $existingTest = Test::where('period_id', $periodId)
            ->where('student_id', $studentId)
            ->where('examiner_id', $teacher->user_id)
            ->whereIn('status', ['in_progress', 'completed'])
            ->first();

        if ($existingTest) {
            return redirect()->route('teacher.tests.question', [
                'test' => $existingTest->test_id, 
                'domain' => $firstDomain->domain_id, 
                'index' => 0
            ]);
        }

        // Create new test
        $test = Test::create([
            'period_id' => $periodId,
            'student_id' => $studentId,
            'examiner_id' => $teacher->user_id,
            'test_date' => now()->toDateString(),
            'status' => 'in_progress',
        ]);

        if (!$test || !$test->test_id) {
            return back()->with('error', 'Failed to create test. Please try again.');
        }

        return redirect()->route('teacher.tests.question', [
            'test' => $test->test_id,
            'domain' => $firstDomain->domain_id,
            'index' => 0
        ]);
    }

    // Show Question
    public function showQuestion($test, $domain, $index)
    {
        $teacher = Auth::user();
        $test = Test::with(['student'])->findOrFail($test);
        
        // Verify access
        if ($test->examiner_id != $teacher->user_id) {
            abort(403);
        }

        $domain = Domain::findOrFail($domain);
        $questions = $domain->questions()->orderBy('order')->get();
        
        if ($index >= $questions->count()) {
            return redirect()->route('teacher.tests.result', ['test' => $test->test_id]);
        }

        $question = $questions[$index];
        $test->load('responses');

        return view('teacher.test_question', compact('test', 'domain', 'question', 'index'));
    }

    // Submit Question Answer
    public function submitQuestion(Request $request, $testId, $domainId, $index)
    {
        $teacher = Auth::user();
        $test = Test::findOrFail($testId);
        
        // Verify access
        if ($test->examiner_id != $teacher->user_id) {
            abort(403);
        }

        $answer = $request->input('answer');
        $domain = Domain::findOrFail($domainId);
        $questions = $domain->questions()->orderBy('order')->get();
        $question = $questions[$index];

        // Save response
        TestResponse::updateOrCreate(
            ['test_id' => $testId, 'question_id' => $question->question_id],
            ['response' => $answer, 'is_assumed' => false]
        );

        // Check if more questions in this domain
        if ($index + 1 < $questions->count()) {
            return redirect()->route('teacher.tests.question', [$testId, $domainId, $index + 1]);
        }

        // Move to next domain
        $allDomains = Domain::orderBy('domain_id')->pluck('domain_id')->toArray();
        $currentDomainIndex = array_search($domainId, $allDomains);
        
        if ($currentDomainIndex + 1 < count($allDomains)) {
            $nextDomainId = $allDomains[$currentDomainIndex + 1];
            return redirect()->route('teacher.tests.question', [$testId, $nextDomainId, 0]);
        }

        // All domains complete
        return redirect()->route('teacher.tests.result', $testId);
    }

    // Show Test Result
    public function result($testId)
    {
        $teacher = Auth::user();
        $test = Test::with(['student', 'assessmentPeriod', 'responses', 'domainScores', 'standardScore'])->findOrFail($testId);
        
        // Verify access
        if ($test->examiner_id != $teacher->user_id) {
            abort(403);
        }

        // Mark as completed if still in progress
        if ($test->status === 'in_progress') {
            $test->status = 'completed';
            $test->save();
        }

        // Prepare data for view
        $domains = Domain::all();
        $sumScaled = $test->standardScore->sum_scaled_scores ?? 0;
        $standardScore = $test->standardScore->standard_score ?? null;
        $interpretation = $test->standardScore->interpretation ?? null;

        return view('teacher.test_result', compact('test', 'domains', 'sumScaled', 'standardScore', 'interpretation'));
    }

    // Finalize Test
    public function finalize($testId)
    {
        $teacher = Auth::user();
        $test = Test::findOrFail($testId);
        
        // Verify access
        if ($test->examiner_id != $teacher->user_id) {
            abort(403);
        }

        // Only completed tests can be finalized
        if ($test->status !== 'completed') {
            return back()->with('error', 'Only completed tests can be finalized.');
        }

        $test->status = 'finalized';
        $test->save();

        return redirect()->route('teacher.index')->with('success', 'Test finalized successfully.');
    }

    // Mark Incomplete
    public function markIncomplete($testId)
    {
        $teacher = Auth::user();
        $test = Test::findOrFail($testId);
        
        // Verify access
        if ($test->examiner_id != $teacher->user_id) {
            abort(403);
        }

        if (!in_array($test->status, ['in_progress', 'completed'])) {
            return back()->with('error', 'Cannot mark this test as incomplete.');
        }

        $test->status = 'in_progress';
        $test->save();

        $firstDomain = Domain::first();
        return redirect()->route('teacher.tests.question', [$testId, $firstDomain->domain_id ?? 1, 0]);
    }

    // Cancel Test
    public function cancel($testId)
    {
        $teacher = Auth::user();
        $test = Test::findOrFail($testId);
        
        // Verify access
        if ($test->examiner_id != $teacher->user_id) {
            abort(403);
        }

        if (!in_array($test->status, ['in_progress', 'completed'])) {
            return back()->with('error', 'Cannot cancel this test.');
        }

        $test->status = 'canceled';
        $test->save();

        return redirect()->route('teacher.index')->with('success', 'Test canceled.');
    }

    // Terminate Test (admin only, but including for completeness)
    public function terminate($testId)
    {
        $teacher = Auth::user();
        
        // Only admins can terminate
        if ($teacher->role !== 'admin') {
            abort(403, 'Only administrators can terminate tests.');
        }

        $test = Test::findOrFail($testId);
        $test->status = 'terminated';
        $test->save();

        return back()->with('success', 'Test terminated.');
    }

    // Pause Test
    public function pause($testId)
    {
        $teacher = Auth::user();
        $test = Test::findOrFail($testId);
        
        // Verify access
        if ($test->examiner_id != $teacher->user_id) {
            abort(403);
        }

        // Paused tests maintain their current status to allow resuming
        return redirect()->route('teacher.index')->with('success', 'Test paused. You can resume it later.');
    }

    // Helper methods
    private function isStudentEligibleForTest($student, $teacher)
    {
        $latestTest = Test::where('student_id', $student->student_id)
            ->where('status', 'finalized')
            ->where('examiner_id', $teacher->user_id)
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$latestTest) {
            // Check if student has any non-overdue assessment periods
            return $student->assessmentPeriods()
                ->where('status', '!=', 'overdue')
                ->where('status', '!=', 'completed')
                ->exists();
        }

        // Check if 6 months have passed and student has non-overdue periods
        $sixMonthsPassed = $latestTest->test_date->addMonths(6) <= now();
        $hasNonOverduePeriods = $student->assessmentPeriods()
            ->where('status', '!=', 'overdue')
            ->where('status', '!=', 'completed')
            ->exists();

        return $sixMonthsPassed && $hasNonOverduePeriods;
    }

    private function getLastStandardScore($student)
    {
        return DB::table('tests')
            ->join('test_standard_scores', 'tests.test_id', '=', 'test_standard_scores.test_id')
            ->where('tests.student_id', $student->student_id)
            ->where('tests.status', 'finalized')
            ->orderBy('tests.updated_at', 'desc')
            ->value('test_standard_scores.standard_score');
    }
}
