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







            // Check if student is eligible for a new test



            $latestCompleted = $tests->firstWhere('status', 'finalized');



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



                $t->where('user_id', $teacher->id);



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



            ->select('sections.section_id', 'sections.name as section_name', 'sections.created_at', 'sections.updated_at')



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







        // Add student count to section object



        $section->student_count = $students->count();







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



        $teacher = Auth::user();



        $teacherId = $teacher->user_id;



        



        // Check if section has students assigned to this teacher - consistent with display logic



        $studentCount = DB::table('students')



            ->join('student_teacher', 'students.student_id', '=', 'student_teacher.student_id')



            ->where('students.section_id', $sectionId)



            ->where('student_teacher.teacher_id', $teacherId)



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

    // Show Test Form - checklist approach for teachers
    public function showForm($testId)
    {
        $teacher = Auth::user();
        $test = Test::with(['student', 'responses'])->findOrFail($testId);
        
        // Verify access
        if ($test->examiner_id != $teacher->user_id) {
            abort(403);
        }

        // Verify test is in appropriate status
        if (!in_array($test->status, ['in_progress', 'completed'])) {
            return redirect()->route('teacher.index')
                ->with('error', 'This test cannot be edited.');
        }

        // Get scale version ID (same as family side)
        $scaleVersionId = DB::table('scale_versions')
            ->where('name', 'ECCD 2004')
            ->value('scale_version_id');

        // Get all domains with their questions (filtered by scale version like family side)
        $domains = Domain::with(['questions' => function($query) use ($scaleVersionId) {
            $query->where('scale_version_id', $scaleVersionId)
                   ->orderBy('order');
        }])->orderBy('domain_id')->get();

        // Get existing answers
        $existing = $test->responses->pluck('response', 'question_id');

        // Calculate progress
        $totalQuestions = $domains->sum(fn($d) => $d->questions->count());
        $answeredCount = $test->responses->count();
        $progressPct = $totalQuestions ? round(($answeredCount / $totalQuestions) * 100) : null;

        return view('teacher.test_form', compact(
            'test', 'domains', 'existing', 'totalQuestions', 'answeredCount', 'progressPct'
        ));
    }

    // Submit Test Form - save all answers at once
    public function submitForm(Request $request, $testId)
    {
        $teacher = Auth::user();
        $test = Test::findOrFail($testId);
        
        // Verify access
        if ($test->examiner_id != $teacher->user_id) {
            abort(403);
        }

        // Get scale version ID (same as family side)
        $scaleVersionId = DB::table('scale_versions')
            ->where('name', 'ECCD 2004')
            ->value('scale_version_id');

        // Get all domains with their questions (filtered by scale version like family side)
        $domains = Domain::with(['questions' => function($query) use ($scaleVersionId) {
            $query->where('scale_version_id', $scaleVersionId)
                   ->orderBy('order');
        }])->orderBy('domain_id')->get();

        // Delete existing responses for this test
        DB::table('test_responses')->where('test_id', $testId)->delete();

        // Save all responses
        foreach ($domains as $domain) {
            foreach ($domain->questions as $question) {
                $answer = $request->input("q_{$question->question_id}");
                
                if ($answer && in_array($answer, ['yes', 'no'])) {
                    DB::table('test_responses')->insert([
                        'test_id' => $testId,
                        'question_id' => $question->question_id,
                        'response' => $answer,
                        'is_assumed' => false,
                    ]);
                }
            }
        }

        // Update test status to completed
        $test->status = 'completed';
        $test->save();

        // Use EccdScoring service to calculate scores (same as family side)
        app(EccdScoring::class)->scoreTest($test);

        return redirect()->route('teacher.tests.result', $test->test_id)
            ->with('success', 'Test completed successfully! You can finalize it when ready.');
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

        // Calculate sum of scaled scores
        $sumScaled = $test->domainScores->sum('scaled_score');
        
        // Get standard score and interpretation from EccdScoring service
        $standardScore = $test->standardScore->standard_score ?? null;
        $interpretation = $test->standardScore->interpretation ?? null;

        return view('teacher.test_result', compact(
            'test', 'sumScaled', 'standardScore', 'interpretation'
        ));
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

        // Only allow finalizing completed tests
        if ($test->status !== 'completed') {
            return back()->with('error', 'Only completed tests can be finalized.');
        }

        $test->status = 'finalized';
        $test->save();

        return back()->with('success', 'Test finalized successfully.');
    }

    // Mark Test as Incomplete
    public function markIncomplete($testId)
    {
        $teacher = Auth::user();
        $test = Test::findOrFail($testId);
        
        // Verify access
        if ($test->examiner_id != $teacher->user_id) {
            abort(403);
        }

        // Only allow marking completed tests as incomplete
        if ($test->status !== 'completed') {
            return back()->with('error', 'Only completed tests can be marked as incomplete.');
        }

        $test->status = 'in_progress';
        $test->save();

        return back()->with('info', 'Test marked as incomplete. You can continue answering later.');
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
        $test = Test::findOrFail($testId);
        
        // Teachers may terminate only their own tests; admins may terminate any test
        // Only admins can terminate
        if ($teacher->role !== 'admin') {
            abort(403, 'Only administrators can terminate tests.');
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

        // Paused tests maintain their current status to allow resuming
        return redirect()->route('teacher.index')->with('success', 'Test paused. You can resume it later.');
    }

    // Helper methods
    public function showGame($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        $currentDomain = $domains[$domainNumber - 1];
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.matching-game', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showColorGame($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        $currentDomain = $domains[$domainNumber - 1];
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.color-game', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showPictureGame($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        $currentDomain = $domains[$domainNumber - 1];
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.picture-game', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showShapeGame($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        $currentDomain = $domains[$domainNumber - 1];
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.shape-game', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showSizeColorGame($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        $currentDomain = $domains[$domainNumber - 1];
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.size-color-game', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showSizeOrderGame($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        $currentDomain = $domains[$domainNumber - 1];
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.size-order-game', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showNameGame($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        $currentDomain = $domains[$domainNumber - 1];
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.name-game', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showColorNameGame($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        $currentDomain = $domains[$domainNumber - 1];
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.color-name-game', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showAnimalVeggieGame($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        $currentDomain = $domains[$domainNumber - 1];
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.animal-veggie-game', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showWhatsWrongGame($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        $currentDomain = $domains[$domainNumber - 1];
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.whats-wrong-game', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showPuzzleGame($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        $currentDomain = $domains[$domainNumber - 1];
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.puzzle-game', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showLetterMatchGame($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        $currentDomain = $domains[$domainNumber - 1];
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.letter-match-game', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showFeelingsGame($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        $currentDomain = $domains[$domainNumber - 1];
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.feelings-game', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showPointObjectsGame($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        $currentDomain = $domains[$domainNumber - 1];
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.point-objects-game', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showFollowInstructionsGame($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        $currentDomain = $domains[$domainNumber - 1];
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1];

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.follow-instructions-game', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    public function showQuestion($testId, $domainNumber, $questionIndex)
    {
        $test = $this->verifyTestOwnership($testId);
        $scaleVersionId = $this->getScaleVersionId();
        $domains = $this->getDomains($scaleVersionId);
        
        // Convert to array if it's a collection
        if ($domains instanceof \Illuminate\Support\Collection) {
            $domains = $domains->toArray();
        }
        
        // Convert parameters to integers
        $domainNumber = (int)$domainNumber;
        $questionIndex = (int)$questionIndex;
        
        $currentDomain = $domains[$domainNumber - 1] ?? null;
        if (!$currentDomain) {
            abort(404, 'Domain not found');
        }
        
        $questions = $this->getDomainQuestions($currentDomain->domain_id, $scaleVersionId);
        $question = $questions[$questionIndex - 1] ?? null;
        if (!$question) {
            abort(404, 'Question not found');
        }

        $existingResponse = DB::table('test_responses as tr')
            ->where('tr.test_id', $testId)
            ->where('tr.question_id', $question->question_id)
            ->value('tr.response');

        $totalAnswered = DB::table('test_responses')->where('test_id', $testId)->count();
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();

        [$prevDomain, $prevIndex] = $this->prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId);
        [$nextDomain, $nextIndex] = $this->nextNav($domainNumber, $questionIndex, count($questions), count($domains));

        return view('teacher.question', compact(
            'test', 'testId', 'currentDomain', 'question',
            'domainNumber', 'questionIndex', 'existingResponse',
            'totalAnswered', 'totalQuestions',
            'prevDomain', 'prevIndex', 'nextDomain', 'nextIndex'
        ));
    }

    private function isStudentEligibleForTest($student, $teacher)
    {
        // Handle both Eloquent models and stdClass objects
        $studentId = $student->student_id ?? $student->section_id ?? null;
        
        if (!$studentId) {
            return false;
        }

        // Get the examiner ID from the test record
        $test = Test::where('student_id', $studentId)
            ->where('status', 'finalized')
            ->orderBy('updated_at', 'desc')
            ->first();
        
        if (!$test) {
            return false;
        }

        // Check if the authenticated user is the examiner OR is an admin
        if (Auth::user()->role !== 'admin' && Auth::user()->user_id !== $test->examiner_id) {
            abort(403, 'Unauthorized access to this test');
        }

        // Check if the authenticated user is the teacher (using the examiner ID from test record)
        if (Auth::user()->role === 'teacher' && Auth::user()->user_id === $test->examiner_id) {
            return true;
        }
        
        // Check if there are any non-overdue assessment periods
        $hasNonOverduePeriods = DB::table('assessment_periods')
            ->where('student_id', $studentId)
            ->where('status', '!=', 'overdue')
            ->where('status', '!=', 'completed')
            ->exists();
        
        // Check if 6 months have passed since last test
        $sixMonthsPassed = $test ? $test->test_date->addMonths(6) <= now() : false;
        
        // Initialize variables to null to avoid undefined variable errors
        $hasNonOverduePeriods = null;
        
        return $sixMonthsPassed && $hasNonOverduePeriods;
    }

    private function getDomains($scaleVersionId)
    {
        // Get all domains (the domains table doesn't have scale_version_id column)
        return DB::table('domains')
            ->orderBy('domain_id')
            ->get();
    }

    private function getScaleVersionId()
    {
        // Get the scale version ID for ECCD 2004
        $scaleVersion = DB::table('scale_versions')
            ->where('name', 'ECCD 2004')
            ->first();
        
        return $scaleVersion ? $scaleVersion->scale_version_id : 1;
    }

    private function prevNav($domainNumber, $questionIndex, $domains, $scaleVersionId)
    {
        // Find previous question
        if ($domainNumber > 1) {
            $prevDomain = $domainNumber - 1;
            $prevQuestions = $this->getDomainQuestions($domains[$prevDomain - 1]->domain_id, $scaleVersionId);
            $prevIndex = count($prevQuestions) - 1;
            return [$prevDomain, $prevIndex];
        }
        
        // If in first domain, find previous domain's last question
        $prevQuestions = $this->getDomainQuestions($domains[$domainNumber - 2]->domain_id, $scaleVersionId);
        $prevIndex = count($prevQuestions) - 1;
        return [$domainNumber - 1, $prevIndex];
    }

    private function nextNav($domainNumber, $questionIndex, $questionCount, $domainCount)
    {
        // Find next question
        if ($questionIndex < $questionCount - 1) {
            return [$domainNumber, $questionIndex + 1];
        }
        
        // If last question in domain, go to next domain
        if ($domainNumber < $domainCount) {
            return [$domainNumber + 1, 0];
        }
        
        // If last domain, return to same domain
        return [$domainNumber, 0];
    }

    private function isStudentEligibleForTest($student, $teacher)
    {
        // Handle both Eloquent models and stdClass objects
        $studentId = $student->student_id ?? $student->section_id ?? null;

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



