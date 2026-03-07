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

use App\Models\AssessmentPeriod;

use App\Models\Section;

use App\Models\Family;

use App\Models\TestResponse;

use App\Models\PeriodSummaryScore;

use App\Services\EccdScoring;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;



class TeacherController extends Controller

{

    public function index()

    {

        $teacher = Auth::user();

        $teacherId = $teacher->user_id;

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

            $hasCompletedOrFinalized = $tests->firstWhere('status', 'finalized')
                ?? $tests->firstWhere('status', 'completed');

            $status[$student->student_id] = [

                'in_progress' => $inProgressTest,

                'latest_teacher' => $latestTest,

                'eligible' => $this->isStudentEligibleForTest($student, $teacher),

                'completed' => (bool) $hasCompletedOrFinalized,

            ];



            // Get longitudinal scores (tests at 6m, 12m, 18m intervals)

            $longitudinals[$student->student_id] = [];

            foreach ($tests->whereIn('status', ['completed', 'finalized']) as $test) {

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



    // ECCD Page

    public function eccd()

    {

        $teacher = Auth::user();

        

        // Get all students assigned to this teacher with their ECCD assessment data

        $students = Student::whereHas('teachers', function($q) use ($teacher) {

            $q->where('user_id', $teacher->user_id);

        })->with(['assessmentPeriods', 'tests' => function($q) use ($teacher) {

            $q->where('examiner_id', $teacher->user_id)

              ->where('status', 'finalized')

              ->with(['domainScores', 'standardScore']);

        }])->get();



        return view('teacher.eccd', compact('students', 'teacher'));

    }



    // Family Page

    public function family()

    {

        $teacher = Auth::user();

        

        // Get families of students assigned to this teacher

        $families = Family::whereHas('students.teachers', function($q) use ($teacher) {

            $q->where('user_id', $teacher->user_id);

        })->with(['user', 'students' => function($q) use ($teacher) {

            $q->whereHas('teachers', function($t) use ($teacher) {

                $t->where('user_id', $teacher->user_id);

            });

        }])->get();



        return view('teacher.family', compact('families'));

    }



    public function familyShow($familyId)

    {

        $teacher = Auth::user();

        $family = Family::with(['user', 'students' => function($q) use ($teacher) {

            $q->whereHas('teachers', function($t) use ($teacher) {

                $t->where('user_id', $teacher->user_id);

            });

        }])->findOrFail($familyId);



        return view('teacher.family_show', compact('family'));

    }



    // Sections Page

    public function sections()

    {

        $teacher = Auth::user();

        $teacherId = $teacher->user_id;

        

        // Get only sections where this teacher has assigned students

        $sections = DB::table('sections')

            ->join('students', 'sections.section_id', '=', 'students.section_id')

            ->join('student_teacher', 'students.student_id', '=', 'student_teacher.student_id')

            ->where('student_teacher.teacher_id', $teacherId)

            ->select('sections.section_id', 'sections.name', 'sections.created_at', 'sections.updated_at')

            ->distinct('sections.section_id')

            ->get();



        // Add student count manually for each section

        $sections = $sections->map(function($section) use ($teacherId) {

            // Count students in this section and assigned to this teacher

            $studentCount = DB::table('students')

                ->join('student_teacher', 'students.student_id', '=', 'student_teacher.student_id')

                ->where('students.section_id', $section->section_id)

                ->where('student_teacher.teacher_id', $teacherId)

                ->count();

            

            $section->student_count = $studentCount;

            return $section;

        });



        return view('teacher.sections', compact('sections'));

    }



    public function sectionsShow($sectionId)

    {

        $teacher = Auth::user();

        $teacherId = $teacher->user_id;

        

        // Get section with correct column names

        $section = DB::table('sections')

            ->select('section_id', 'name', 'created_at', 'updated_at')

            ->where('section_id', $sectionId)

            ->first();



        if (!$section) {

            abort(404);

        }



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



        // Add total student count (all students in this section) to section object

        $section->student_count = DB::table('students')
            ->where('section_id', $sectionId)
            ->count();



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

            'name' => 'required|string|max:255'

        ]);



        $sectionId = DB::table('sections')->insertGetId([

            'name' => $request->name,

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

            'name' => 'required|string|max:255'

        ]);



        DB::table('sections')

            ->where('section_id', $sectionId)

            ->update([

                'name' => $request->name,

                'updated_at' => now()

            ]);



        return redirect()->route('teacher.sections')->with('success', 'Section updated successfully.');

    }



    // Delete Section

    public function sectionsDestroy($sectionId)

    {

        // Check if section has any students at all before allowing delete

        $studentCount = DB::table('students')

            ->where('students.section_id', $sectionId)

            ->count();



        if ($studentCount > 0) {

            return back()->with('error', 'Cannot delete section with assigned students.');

        }



        $deleted = DB::table('sections')->where('section_id', $sectionId)->delete();



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

        ->with(['student', 'assessmentPeriod', 'standardScore'])

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

            ->where('period_id', $periodId)

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



        $student->age = $student->age;

        $student->eligible = $this->isStudentEligibleForTest($student, $teacher);

        $student->last_standard_score = $this->getLastStandardScore($student);



        return view('teacher.student', compact('student'));

    }



    // Finalize Test
    public function finalize($testId)
    {
        $teacher = Auth::user();
        $test = Test::with(['assessmentPeriod', 'student'])->findOrFail($testId);

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

        // Compute scores and update period summary for this test/period
        app(EccdScoring::class)->scoreTestAndRecompute($test);

        return redirect()->route('teacher.index')->with('success', 'Test finalized successfully.');
    }

    // Start Test
    public function startTest(Request $request, $studentId)
    {
        $teacher = Auth::user();

        // Ensure the current user is a teacher
        if ($teacher->role !== 'teacher') {
            abort(403, 'Only teachers can start tests.');
        }

        $student = Student::with('teachers')->findOrFail($studentId);

        // Verify teacher has access to this student
        if (!$student->teachers->contains('user_id', $teacher->user_id)) {
            abort(403);
        }

        // Enforce shared eligibility rules
        if (!$this->isStudentEligibleForTest($student, $teacher)) {
            return back()->with('error', 'Student is not yet eligible for a new test.');
        }

        $periodId = $request->input('period_id');

        // If no period_id was provided (e.g. from dashboard links), try to pick a valid period
        if (!$periodId) {
            $autoPeriod = AssessmentPeriod::where('student_id', $studentId)
                ->where('status', '!=', 'overdue')
                ->where('status', '!=', 'completed')
                ->orderBy('start_date')
                ->first();

            if (!$autoPeriod) {
                return back()->with('error', 'No active assessment period available for this student.');
            }

            $periodId = $autoPeriod->period_id;
        }

        Log::debug('StartTest received:', [
            'periodId' => $periodId,
            'all_inputs' => $request->all(),
            'studentId' => $studentId,
        ]);

        $period = AssessmentPeriod::findOrFail($periodId);

        // Prevent starting tests for overdue, completed or already-ended periods
        if ($period->status === 'overdue') {
            return back()->with('error', 'Cannot start test for an overdue assessment period.');
        }

        if ($period->status === 'completed') {
            return back()->with('error', 'Cannot start test for a completed assessment period.');
        }

        if (Carbon::parse($period->end_date)->lt(Carbon::now()->startOfDay())) {
            return back()->with('error', 'Cannot start test for an assessment period that has already ended.');
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
            return redirect()->route('teacher.tests.form', $existingTest->test_id);
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

        return redirect()->route('teacher.tests.form', $test->test_id);
    }



    // Show full test form (all domains/questions)
    public function showForm($testId)
    {
        $teacher = Auth::user();

        $test = Test::with(['student', 'responses'])->findOrFail($testId);

        if ($test->examiner_id != $teacher->user_id) {
            abort(403);
        }

        $domains = Domain::with(['questions' => function ($q) {
            $q->orderBy('order');
        }])->orderBy('domain_id')->get();

        $existing = $test->responses->pluck('response', 'question_id');

        $totalQuestions = $domains->sum(function ($d) {
            return $d->questions->count();
        });
        $answeredCount = $existing->filter(function ($v) {
            return $v !== null && $v !== '';
        })->count();
        $progressPct = $totalQuestions ? round(($answeredCount / max(1, $totalQuestions)) * 100) : null;

        return view('teacher.test_form', compact('test', 'domains', 'existing', 'totalQuestions', 'answeredCount', 'progressPct'));
    }



    // Submit full test form
    public function submitForm(Request $request, $testId)
    {
        $teacher = Auth::user();
        $test = Test::with('student')->findOrFail($testId);

        if ($test->examiner_id != $teacher->user_id) {
            abort(403);
        }

        $domains = Domain::with('questions')->orderBy('domain_id')->get();

        $totalQuestions = 0;
        $answeredCount = 0;

        foreach ($domains as $domain) {
            foreach ($domain->questions as $question) {
                $totalQuestions++;
                $key = 'q_' . $question->question_id;
                if (!$request->has($key)) {
                    continue;
                }
                $answer = $request->input($key);
                if ($answer === null || $answer === '') {
                    continue;
                }

                TestResponse::updateOrCreate(
                    ['test_id' => $testId, 'question_id' => $question->question_id],
                    ['response' => $answer, 'is_assumed' => false]
                );

                $answeredCount++;
            }
        }

        // Require all questions to be answered before proceeding to result/completed state
        if ($totalQuestions > 0 && $answeredCount < $totalQuestions) {
            return redirect()
                ->route('teacher.tests.form', $testId)
                ->with('error', "Please answer all questions before viewing the result. You have answered $answeredCount of $totalQuestions.");
        }

        // Mark test as completed and compute scores/period summary
        $test->status = 'completed';
        $test->save();

        $freshTest = Test::with('assessmentPeriod', 'student')->find($testId);
        if ($freshTest) {
            app(\App\Services\EccdScoring::class)->scoreTestAndRecompute($freshTest);
        }

        // After saving complete responses and scoring, send teacher to result page
        return redirect()
            ->route('teacher.tests.result', $testId)
            ->with('success', 'Test completed and scored successfully.');
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



        // Mark as completed and ensure scoring if still in progress

        if ($test->status === 'in_progress') {

            $test->status = 'completed';

            $test->save();

            app(EccdScoring::class)->scoreTestAndRecompute($test);

            $test->load('standardScore', 'domainScores');

        }



        // Prepare data for view

        $domains = Domain::all();

        $sumScaled = $test->standardScore->sum_scaled_scores ?? 0;

        $standardScore = $test->standardScore->standard_score ?? null;

        $interpretation = $test->standardScore->interpretation ?? null;



        return view('teacher.test_result', compact('test', 'domains', 'sumScaled', 'standardScore', 'interpretation'));

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



        return redirect()->route('teacher.tests.form', $testId);

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



    // Terminate Test (teacher or admin)

    public function terminate($testId)

    {

        $user = Auth::user();

        $test = Test::findOrFail($testId);

        // Teachers may terminate only their own tests; admins may terminate any test

        if ($user->role === 'teacher' && $test->examiner_id != $user->user_id) {

            abort(403);

        }



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



        // Paused tests maintain their current status; clarify message for teachers

        return redirect()->route('teacher.index')->with('success', 'Test left in progress. You can resume it later from the dashboard.');

    }



    // Helper methods

    private function isStudentEligibleForTest($student, $teacher)

    {

        // Handle both Eloquent models and stdClass objects
        $studentId = $student->student_id ?? null;

        

        if (!$studentId) {

            return false;

        }



        $teacherId = $teacher->user_id;

        $latestTest = Test::where('student_id', $studentId)

            ->where('status', 'finalized')

            ->where('examiner_id', $teacherId)

            ->orderBy('updated_at', 'desc')

            ->first();



        if (!$latestTest) {

            // Check if student has any non-overdue assessment periods using query builder

            return DB::table('assessment_periods')

                ->where('student_id', $studentId)

                ->where('status', '!=', 'overdue')

                ->where('status', '!=', 'completed')

                ->exists();

        }



        // Check if 6 months have passed and student has non-overdue periods

        $sixMonthsPassed = $latestTest->test_date->addMonths(6) <= now();

        $hasNonOverduePeriods = DB::table('assessment_periods')

            ->where('student_id', $studentId)

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

