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
use App\Services\EccdScoring;

class TeacherController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        
        // Total assigned students
        $totalStudents = Student::whereHas('teachers', function($q) use ($teacher) {
            $q->where('user_id', $teacher->id);
        })->count();

        // Upcoming assessments (status = scheduled)
        $upcomingAssessments = AssessmentPeriod::whereHas('student.teachers', function($q) use ($teacher) {
            $q->where('user_id', $teacher->id);
        })
        ->where('status', 'scheduled')
        ->with(['student'])
        ->orderBy('start_date')
        ->get();

        // Overdue assessments (status = overdue)
        $overdueAssessments = AssessmentPeriod::whereHas('student.teachers', function($q) use ($teacher) {
            $q->where('user_id', $teacher->id);
        })
        ->where('status', 'overdue')
        ->with(['student'])
        ->orderBy('end_date')
        ->get();

        // Recently completed tests (status = finalized, last 5)
        $recentlyCompleted = Test::whereHas('student.teachers', function($q) use ($teacher) {
            $q->where('user_id', $teacher->id);
        })
        ->where('status', 'finalized')
        ->with(['student', 'assessmentPeriod'])
        ->orderBy('updated_at', 'desc')
        ->limit(5)
        ->get();

        return view('teacher.dashboard', compact(
            'teacher',
            'totalStudents',
            'upcomingAssessments',
            'overdueAssessments',
            'recentlyCompleted'
        ));
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
        
        // Get sections that have students assigned to this teacher
        $sections = Section::whereHas('students.teachers', function($q) use ($teacher) {
            $q->where('user_id', $teacher->id);
        })->get();

        return view('teacher.sections', compact('sections'));
    }

    public function sectionsShow($sectionId)
    {
        $teacher = Auth::user();
        $section = Section::with(['students' => function($q) use ($teacher) {
            $q->whereHas('teachers', function($t) use ($teacher) {
                $t->where('user_id', $teacher->id);
            });
        }])->findOrFail($sectionId);

        // Add eligibility and last standard score for each student
        $section->students = $section->students->map(function($student) use ($teacher) {
            $student->age = $student->date_of_birth ? (int) $student->date_of_birth->diffInYears(now()) : null;
            $student->eligible = $this->isStudentEligibleForTest($student, $teacher);
            $student->last_standard_score = $this->getLastStandardScore($student);
            return $student;
        });

        return view('teacher.sections_show', compact('section'));
    }

    // Reports Page
    public function reports()
    {
        $teacher = Auth::user();
        
        // Get all finalized tests for students assigned to this teacher
        $tests = Test::whereHas('student.teachers', function($q) use ($teacher) {
            $q->where('user_id', $teacher->id);
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
        if (!$student->teachers()->where('user_id', $teacher->id)->exists()) {
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
        if (!$student->teachers()->where('user_id', $teacher->id)->exists()) {
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

    // Helper methods
    private function isStudentEligibleForTest($student, $teacher)
    {
        $latestTest = Test::where('student_id', $student->student_id)
            ->where('status', 'finalized')
            ->where('examiner_id', $teacher->user_id)
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$latestTest) {
            return true;
        }

        // Check if 6 months have passed
        return $latestTest->test_date->addMonths(6) <= now();
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
