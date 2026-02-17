<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Family;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Domain;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestResponse;
use App\Models\DomainScore;
use App\Models\StudentTag;
use App\Models\TestPicture;
use App\Services\EccdScoring;

class AdminController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        // 1. System overview
        $totalStudents = DB::table('students')->count();
        $totalFamilies = DB::table('families')->count();
        $totalTeachers = DB::table('teachers')->count();

        $activeAssessmentPeriods = DB::table('assessment_periods')
            ->where('status', 'scheduled')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        $testsInProgress = DB::table('tests')
            ->where('status', 'in_progress')
            ->count();

        $completedAssessmentsThisMonth = DB::table('tests')
            ->whereIn('status', ['completed', 'finalized'])
            ->whereBetween('test_date', [$startOfMonth, $endOfMonth])
            ->count();

        // 2. Assessment status overview
        $scheduledAssessments = DB::table('assessment_periods')
            ->where('status', 'scheduled')
            ->whereDate('start_date', '>', $today)
            ->count();

        $ongoingTests = $testsInProgress;

        $completedTests = DB::table('tests')
            ->whereIn('status', ['completed', 'finalized'])
            ->count();

        $overdueAssessments = DB::table('assessment_periods')
            ->where('status', 'overdue')
            ->count();

        // 3. Recent activity feed
        $activities = collect();

        // Teacher finalized a test
        $teacherFinalized = DB::table('tests as t')
            ->join('users as u', 't.examiner_id', '=', 'u.user_id')
            ->join('students as s', 't.student_id', '=', 's.student_id')
            ->where('t.status', 'finalized')
            ->where('u.role', 'teacher')
            ->orderByDesc('t.updated_at')
            ->limit(10)
            ->get([
                't.updated_at as at',
                'u.username as actor',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student"),
            ]);

        foreach ($teacherFinalized as $row) {
            if ($row->at) {
                $activities->push([
                    'time' => $row->at,
                    'label' => "Teacher {$row->actor} finalized a test for {$row->student}",
                ]);
            }
        }

        // Family submitted an assessment
        $familySubmitted = DB::table('tests as t')
            ->join('users as u', 't.examiner_id', '=', 'u.user_id')
            ->join('students as s', 't.student_id', '=', 's.student_id')
            ->whereIn('t.status', ['completed', 'finalized'])
            ->where('u.role', 'family')
            ->orderByDesc('t.updated_at')
            ->limit(10)
            ->get([
                't.updated_at as at',
                'u.username as actor',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student"),
            ]);

        foreach ($familySubmitted as $row) {
            if ($row->at) {
                $activities->push([
                    'time' => $row->at,
                    'label' => "Family {$row->actor} submitted an assessment for {$row->student}",
                ]);
            }
        }

        // New student registered
        $newStudents = DB::table('students as s')
            ->orderByDesc('s.created_at')
            ->limit(10)
            ->get([
                's.created_at as at',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student"),
            ]);

        foreach ($newStudents as $row) {
            if ($row->at) {
                $activities->push([
                    'time' => $row->at,
                    'label' => "New student registered: {$row->student}",
                ]);
            }
        }

        // Assessment period created
        $newPeriods = DB::table('assessment_periods as p')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->orderByDesc('p.created_at')
            ->limit(10)
            ->get([
                'p.created_at as at',
                'p.description as period_description',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student"),
            ]);

        foreach ($newPeriods as $row) {
            if ($row->at) {
                $activities->push([
                    'time' => $row->at,
                    'label' => "Assessment period created for {$row->student}: {$row->period_description}",
                ]);
            }
        }

        $recentActivities = $activities
            ->sortByDesc('time')
            ->take(12)
            ->values();

        // 4. Students requiring attention
        $attention = collect();

        // Overdue assessment periods
        $overduePeriodsList = DB::table('assessment_periods as p')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->where('p.status', 'overdue')
            ->limit(20)
            ->get([
                'p.description as period_description',
                'p.end_date',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student"),
            ]);

        foreach ($overduePeriodsList as $row) {
            $attention->push([
                'type' => 'Overdue assessment period',
                'student' => $row->student,
                'detail' => $row->period_description . ' (ended ' . $row->end_date . ')',
            ]);
        }

        // Major teacher discrepancies
        $majorTeacherDisc = DB::table('period_summary_scores as ps')
            ->join('assessment_periods as p', 'ps.period_id', '=', 'p.period_id')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->where('ps.teacher_discrepancy', 'major')
            ->limit(20)
            ->get([
                'p.description as period_description',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student"),
            ]);

        foreach ($majorTeacherDisc as $row) {
            $attention->push([
                'type' => 'Major teacher discrepancy',
                'student' => $row->student,
                'detail' => $row->period_description,
            ]);
        }

        // Major teacher-family discrepancies
        $majorTeacherFamilyDisc = DB::table('period_summary_scores as ps')
            ->join('assessment_periods as p', 'ps.period_id', '=', 'p.period_id')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->where('ps.teacher_family_discrepancy', 'major')
            ->limit(20)
            ->get([
                'p.description as period_description',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student"),
            ]);

        foreach ($majorTeacherFamilyDisc as $row) {
            $attention->push([
                'type' => 'Major teacher–family discrepancy',
                'student' => $row->student,
                'detail' => $row->period_description,
            ]);
        }

        // Missing family submissions (teacher scores present, family score missing)
        $missingFamily = DB::table('period_summary_scores as ps')
            ->join('assessment_periods as p', 'ps.period_id', '=', 'p.period_id')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->whereNotNull('ps.teachers_standard_score_avg')
            ->whereNull('ps.family_standard_score')
            ->limit(20)
            ->get([
                'p.description as period_description',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student"),
            ]);

        foreach ($missingFamily as $row) {
            $attention->push([
                'type' => 'Missing family submission',
                'student' => $row->student,
                'detail' => $row->period_description,
            ]);
        }

        $studentsRequiringAttention = $attention->take(15)->values();

        // 5. Development summary snapshot
        $developmentSnapshot = [
            'advanced' => 0,
            'average' => 0,
            'monitor' => 0,
        ];

        $interpretations = DB::table('period_summary_scores')
            ->whereNotNull('final_interpretation')
            ->pluck('final_interpretation');

        foreach ($interpretations as $interpretation) {
            $label = strtolower($interpretation);
            if (str_contains($label, 'advanced')) {
                $developmentSnapshot['advanced']++;
            } elseif (str_contains($label, 'average')) {
                $developmentSnapshot['average']++;
            } else {
                $developmentSnapshot['monitor']++;
            }
        }

        return view('admin.index', [
            'totalStudents' => $totalStudents,
            'totalFamilies' => $totalFamilies,
            'totalTeachers' => $totalTeachers,
            'activeAssessmentPeriods' => $activeAssessmentPeriods,
            'testsInProgress' => $testsInProgress,
            'completedAssessmentsThisMonth' => $completedAssessmentsThisMonth,
            'scheduledAssessments' => $scheduledAssessments,
            'ongoingTests' => $ongoingTests,
            'completedTests' => $completedTests,
            'overdueAssessments' => $overdueAssessments,
            'recentActivities' => $recentActivities,
            'studentsRequiringAttention' => $studentsRequiringAttention,
            'developmentSnapshot' => $developmentSnapshot,
        ]);
    }

    public function users(Request $request)
    {
        // Activity overview
        $totalTeachers = DB::table('users as u')
            ->where('u.role', 'teacher')
            ->count();

        $totalFamilies = DB::table('users as u')
            ->where('u.role', 'family')
            ->count();

        $totalActiveUsers = DB::table('users as u')
            ->where(function ($q) {
                $q->where('u.status', 'active')
                  ->orWhereNull('u.status');
            })
            ->count();

        $recentUsers = DB::table('users as u')
            ->orderByDesc('u.created_at')
            ->limit(5)
            ->get([
                'u.user_id',
                'u.username',
                'u.role',
                'u.created_at',
            ]);

        // User list with filters/search
        $query = DB::table('users as u')
            ->leftJoin('teachers as t', 't.user_id', '=', 'u.user_id')
            ->leftJoin('families as f', 'f.user_id', '=', 'u.user_id')
            ->select(
                'u.user_id',
                'u.username',
                'u.email',
                'u.role',
                'u.status',
                'u.profile_path',
                'u.created_at',
                'u.updated_at'
            );

        $role = $request->input('role');
        if (in_array($role, ['admin', 'teacher', 'family'], true)) {
            $query->where('u.role', $role);
        }

        if ($request->boolean('recent_only')) {
            $sevenDaysAgo = Carbon::now()->subDays(7);
            $query->where('u.created_at', '>=', $sevenDaysAgo);
        }

        if ($request->boolean('incomplete_only')) {
            $query->where(function ($q) {
                $q->whereNull('u.email')
                  ->orWhere(function ($q2) {
                      $q2->where('u.role', 'teacher')
                          ->whereNull('t.user_id');
                  })
                  ->orWhere(function ($q2) {
                      $q2->where('u.role', 'family')
                          ->whereNull('f.user_id');
                  });
            });
        }

        if ($username = trim((string) $request->input('username'))) {
            $query->where('u.username', 'like', '%' . $username . '%');
        }

        if ($email = trim((string) $request->input('email'))) {
            $query->where('u.email', 'like', '%' . $email . '%');
        }

        if ($teacherName = trim((string) $request->input('teacher_name'))) {
            $teacherNameLike = '%' . $teacherName . '%';
            $query->where(function ($q) use ($teacherNameLike) {
                $q->whereRaw("CONCAT(COALESCE(t.first_name, ''), ' ', COALESCE(t.last_name, '')) LIKE ?", [$teacherNameLike]);
            });
        }

        if ($familyName = trim((string) $request->input('family_name'))) {
            $familyNameLike = '%' . $familyName . '%';
            $query->where('f.family_name', 'like', $familyNameLike);
        }

        $users = $query
            ->orderByDesc('u.created_at')
            ->paginate(20)
            ->appends($request->query());

        return view('admin.users', [
            'users' => $users,
            'filters' => [
                'role' => $role,
                'recent_only' => $request->boolean('recent_only'),
                'incomplete_only' => $request->boolean('incomplete_only'),
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'teacher_name' => $request->input('teacher_name'),
                'family_name' => $request->input('family_name'),
            ],
            'totalTeachers' => $totalTeachers,
            'totalFamilies' => $totalFamilies,
            'totalActiveUsers' => $totalActiveUsers,
            'recentUsers' => $recentUsers,
        ]);
    }

    public function students()
    {
        $now = Carbon::now();

        $studentsQuery = DB::table('students as s')
            ->leftJoin('families as f', 's.family_id', '=', 'f.user_id')
            ->leftJoin('sections as sec', 's.section_id', '=', 'sec.section_id')
            ->select(
                's.student_id',
                's.first_name',
                's.last_name',
                's.date_of_birth',
                's.family_id',
                's.feature_path',
                's.created_at',
                's.updated_at',
                'f.family_name',
                'sec.name as section_name'
            );

        // Search filters
        $studentName = trim((string) request('student_name'));
        if ($studentName !== '') {
            $like = '%' . $studentName . '%';
            $studentsQuery->whereRaw("CONCAT(s.first_name, ' ', s.last_name) LIKE ?", [$like]);
        }

        $familyName = trim((string) request('family_name'));
        if ($familyName !== '') {
            $studentsQuery->where('f.family_name', 'like', '%' . $familyName . '%');
        }

        // Section filter
        $sectionId = request('section_id');
        if ($sectionId) {
            $studentsQuery->where('s.section_id', $sectionId);
        }

        // Age range filter (in years)
        $ageMin = request('age_min');
        $ageMax = request('age_max');
        if ($ageMin !== null || $ageMax !== null) {
            if ($ageMin !== null && is_numeric($ageMin)) {
                $maxDob = $now->copy()->subYears((int) $ageMin)->toDateString();
                $studentsQuery->whereDate('s.date_of_birth', '<=', $maxDob);
            }
            if ($ageMax !== null && is_numeric($ageMax)) {
                $minDob = $now->copy()->subYears((int) $ageMax + 1)->addDay()->toDateString();
                $studentsQuery->whereDate('s.date_of_birth', '>=', $minDob);
            }
        }

        // Assigned teacher filter
        $teacherId = request('teacher_id');
        if ($teacherId) {
            $studentsQuery->whereExists(function ($q) use ($teacherId) {
                $q->from('student_teacher as st')
                    ->whereColumn('st.student_id', 's.student_id')
                    ->where('st.teacher_id', $teacherId);
            });
        }

        // Assessment status / interpretation filters via exists
        $statusFilter = request('assessment_status');
        if ($statusFilter === 'overdue') {
            $studentsQuery->whereExists(function ($q) {
                $q->from('assessment_periods as p')
                    ->whereColumn('p.student_id', 's.student_id')
                    ->where('p.status', 'overdue');
            });
        }

        if (request()->boolean('with_completed_tests')) {
            $studentsQuery->whereExists(function ($q) {
                $q->from('tests as t')
                    ->whereColumn('t.student_id', 's.student_id')
                    ->whereIn('t.status', ['completed', 'finalized']);
            });
        }

        if (request()->boolean('without_completed_tests')) {
            $studentsQuery->whereNotExists(function ($q) {
                $q->from('tests as t')
                    ->whereColumn('t.student_id', 's.student_id')
                    ->whereIn('t.status', ['completed', 'finalized']);
            });
        }

        $interpretationFilter = request('interpretation');
        if ($interpretationFilter) {
            $interpretationLike = null;
            if ($interpretationFilter === 'advanced') {
                $interpretationLike = '%advanced%';
            } elseif ($interpretationFilter === 'average') {
                $interpretationLike = '%average%';
            } elseif ($interpretationFilter === 'needs_retest') {
                $interpretationLike = '%re-test%';
            }

            if ($interpretationLike) {
                $studentsQuery->whereExists(function ($q) use ($interpretationLike) {
                    $q->from('assessment_periods as p')
                        ->join('period_summary_scores as ps', 'ps.period_id', '=', 'p.period_id')
                        ->whereColumn('p.student_id', 's.student_id')
                        ->where('ps.final_interpretation', 'like', $interpretationLike);
                });
            }
        }

        $students = $studentsQuery
            ->orderBy('s.last_name')
            ->orderBy('s.first_name')
            ->paginate(20)
            ->appends(request()->query());

        $studentIds = $students->pluck('student_id')->all();

        $teachersByStudent = collect();
        $testsByStudent = collect();
        $periodsByStudent = collect();
        $summariesByStudent = collect();

        if (!empty($studentIds)) {
            $teachersByStudent = DB::table('student_teacher as st')
                ->join('teachers as t', 'st.teacher_id', '=', 't.user_id')
                ->join('users as u', 't.user_id', '=', 'u.user_id')
                ->whereIn('st.student_id', $studentIds)
                ->select('st.student_id', 'u.username', 't.first_name', 't.last_name')
                ->get()
                ->groupBy('student_id');

            $testsByStudent = DB::table('tests as t')
                ->whereIn('t.student_id', $studentIds)
                ->select('t.student_id', 't.status')
                ->get()
                ->groupBy('student_id');

            $periodsByStudent = DB::table('assessment_periods as p')
                ->whereIn('p.student_id', $studentIds)
                ->select('p.student_id', 'p.status')
                ->get()
                ->groupBy('student_id');

            $summariesByStudent = DB::table('period_summary_scores as ps')
                ->join('assessment_periods as p', 'ps.period_id', '=', 'p.period_id')
                ->whereIn('p.student_id', $studentIds)
                ->orderByDesc('ps.created_at')
                ->select(
                    'p.student_id',
                    'ps.final_standard_score',
                    'ps.final_interpretation'
                )
                ->get()
                ->groupBy('student_id');
        }

        $rows = $students->getCollection()->map(function ($s) use ($teachersByStudent, $testsByStudent, $periodsByStudent, $summariesByStudent, $now) {
            $teachers = $teachersByStudent->get($s->student_id, collect());
            $tests = $testsByStudent->get($s->student_id, collect());
            $periods = $periodsByStudent->get($s->student_id, collect());
            $summaries = $summariesByStudent->get($s->student_id, collect());

            $hasOngoing = $tests->contains(function ($t) {
                return $t->status === 'in_progress';
            });
            $hasCompleted = $tests->contains(function ($t) {
                return in_array($t->status, ['completed', 'finalized'], true);
            });
            $hasOverdue = $periods->contains(function ($p) {
                return $p->status === 'overdue';
            });
            $hasScheduled = $periods->contains(function ($p) {
                return $p->status === 'scheduled';
            });

            $status = 'No assessment';
            if ($hasOngoing) {
                $status = 'Ongoing';
            } elseif ($hasOverdue) {
                $status = 'Overdue';
            } elseif ($hasCompleted) {
                $status = 'Completed';
            } elseif ($hasScheduled) {
                $status = 'Scheduled';
            }

            $latestSummary = $summaries->first();
            $latestScore = $latestSummary->final_standard_score ?? null;
            $latestInterp = $latestSummary->final_interpretation ?? null;

            $ageYears = null;
            if ($s->date_of_birth) {
                try {
                    $dob = Carbon::parse($s->date_of_birth);
                    $ageYears = $dob->diffInYears($now);
                } catch (\Throwable $e) {
                    $ageYears = null;
                }
            }

            $s->computed_age_years = $ageYears;
            $s->computed_status = $status;
            $s->computed_latest_score = $latestScore;
            $s->computed_latest_interpretation = $latestInterp;
            $s->computed_teachers = $teachers;

            return $s;
        });

        $students->setCollection($rows);

        // Alerts panel data
        $alerts = [
            'overdue' => [],
            'no_teachers' => [],
            'missing_family_eval' => [],
            'scheduled_no_tests' => [],
        ];

        $alerts['overdue'] = DB::table('students as s')
            ->join('assessment_periods as p', 'p.student_id', '=', 's.student_id')
            ->where('p.status', 'overdue')
            ->orderBy('p.end_date')
            ->limit(10)
            ->get([
                's.student_id',
                's.first_name',
                's.last_name',
                'p.description as period_description',
                'p.end_date',
            ]);

        $alerts['no_teachers'] = DB::table('students as s')
            ->leftJoin('student_teacher as st', 'st.student_id', '=', 's.student_id')
            ->whereNull('st.teacher_id')
            ->orderBy('s.last_name')
            ->limit(10)
            ->get([
                's.student_id',
                's.first_name',
                's.last_name',
            ]);

        $alerts['missing_family_eval'] = DB::table('students as s')
            ->join('assessment_periods as p', 'p.student_id', '=', 's.student_id')
            ->join('period_summary_scores as ps', 'ps.period_id', '=', 'p.period_id')
            ->whereNotNull('ps.teachers_standard_score_avg')
            ->whereNull('ps.family_standard_score')
            ->orderByDesc('ps.created_at')
            ->limit(10)
            ->get([
                's.student_id',
                's.first_name',
                's.last_name',
                'p.description as period_description',
            ]);

        $alerts['scheduled_no_tests'] = DB::table('students as s')
            ->join('assessment_periods as p', 'p.student_id', '=', 's.student_id')
            ->where('p.status', 'scheduled')
            ->whereNotExists(function ($q) {
                $q->from('tests as t')
                    ->whereColumn('t.period_id', 'p.period_id');
            })
            ->orderBy('p.start_date')
            ->limit(10)
            ->get([
                's.student_id',
                's.first_name',
                's.last_name',
                'p.description as period_description',
                'p.start_date',
            ]);

        // Teacher options for filters and assignments
        $teacherOptions = DB::table('teachers as t')
            ->join('users as u', 't.user_id', '=', 'u.user_id')
            ->orderBy('t.last_name')
            ->orderBy('t.first_name')
            ->get([
                't.user_id',
                't.first_name',
                't.last_name',
                'u.username',
            ]);

        $sectionOptions = DB::table('sections')
            ->orderBy('name')
            ->get([
                'section_id',
                'name',
            ]);

        return view('admin.students', [
            'students' => $students,
            'teacherOptions' => $teacherOptions,
            'sectionOptions' => $sectionOptions,
            'alerts' => $alerts,
        ]);
    }

    public function assessments(Request $request)
    {
        $now = Carbon::now();

        // Top-level summary
        $totalPeriods = DB::table('assessment_periods')->count();

        $scheduledPeriods = DB::table('assessment_periods')
            ->where('status', 'scheduled')
            ->count();

        $completedPeriods = DB::table('assessment_periods')
            ->where('status', 'completed')
            ->count();

        $overduePeriods = DB::table('assessment_periods')
            ->where('status', 'overdue')
            ->count();

        $ongoingAssessments = DB::table('assessment_periods as p')
            ->whereExists(function ($q) {
                $q->from('tests as t')
                    ->whereColumn('t.period_id', 'p.period_id')
                    ->where('t.status', 'in_progress');
            })
            ->distinct('p.period_id')
            ->count('p.period_id');

        $testsAwaitingFinalization = DB::table('tests')
            ->where('status', 'completed')
            ->count();

        // Main list query
        $query = DB::table('assessment_periods as p')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->leftJoin('families as f', 's.family_id', '=', 'f.user_id')
            ->select(
                'p.period_id',
                'p.student_id',
                'p.description',
                'p.start_date',
                'p.end_date',
                'p.status',
                'p.created_at',
                'p.updated_at',
                's.first_name as student_first_name',
                's.last_name as student_last_name',
                'f.family_name'
            );

        // Filters
        $status = $request->input('status');
        if (in_array($status, ['scheduled', 'completed', 'overdue'], true)) {
            $query->where('p.status', $status);
        } elseif ($status === 'ongoing') {
            $query->whereExists(function ($q) {
                $q->from('tests as t')
                    ->whereColumn('t.period_id', 'p.period_id')
                    ->where('t.status', 'in_progress');
            });
        }

        $teacherId = $request->input('teacher_id');
        if ($teacherId) {
            $query->whereExists(function ($q) use ($teacherId) {
                $q->from('student_teacher as st')
                    ->whereColumn('st.student_id', 'p.student_id')
                    ->where('st.teacher_id', $teacherId);
            });
        }

        if ($studentName = trim((string) $request->input('student_name'))) {
            $like = '%' . $studentName . '%';
            $query->whereRaw("CONCAT(s.first_name, ' ', s.last_name) LIKE ?", [$like]);
        }

        if ($familyName = trim((string) $request->input('family_name'))) {
            $query->where('f.family_name', 'like', '%' . $familyName . '%');
        }

        $startFrom = $request->input('start_from');
        $startTo = $request->input('start_to');
        if ($startFrom) {
            $query->whereDate('p.start_date', '>=', $startFrom);
        }
        if ($startTo) {
            $query->whereDate('p.start_date', '<=', $startTo);
        }

        if ($request->boolean('with_discrepancies')) {
            $query->whereExists(function ($q) {
                $q->from('period_summary_scores as ps')
                    ->whereColumn('ps.period_id', 'p.period_id')
                    ->where(function ($qq) {
                        $qq->whereIn('ps.teacher_discrepancy', ['minor', 'major'])
                            ->orWhereIn('ps.teacher_family_discrepancy', ['minor', 'major']);
                    });
            });
        }

        if ($request->boolean('missing_family_test')) {
            $query->whereNotExists(function ($q) {
                $q->from('tests as t')
                    ->join('users as u', 't.examiner_id', '=', 'u.user_id')
                    ->whereColumn('t.period_id', 'p.period_id')
                    ->where('u.role', 'family')
                    ->whereIn('t.status', ['completed', 'finalized']);
            });
        }

        if ($request->boolean('missing_teacher_test')) {
            $query->whereNotExists(function ($q) {
                $q->from('tests as t')
                    ->join('users as u', 't.examiner_id', '=', 'u.user_id')
                    ->whereColumn('t.period_id', 'p.period_id')
                    ->where('u.role', 'teacher')
                    ->whereIn('t.status', ['completed', 'finalized']);
            });
        }

        $periods = $query
            ->orderByDesc('p.start_date')
            ->orderByDesc('p.period_id')
            ->paginate(20)
            ->appends($request->query());

        $periodIds = $periods->pluck('period_id')->all();
        $studentIds = $periods->pluck('student_id')->all();

        $assignedTeachersByStudent = collect();
        $testsByPeriod = collect();
        $summariesByPeriod = collect();

        if (!empty($periodIds)) {
            if (!empty($studentIds)) {
                $assignedTeachersByStudent = DB::table('student_teacher as st')
                    ->join('teachers as t', 'st.teacher_id', '=', 't.user_id')
                    ->join('users as u', 't.user_id', '=', 'u.user_id')
                    ->whereIn('st.student_id', $studentIds)
                    ->select(
                        'st.student_id',
                        'u.user_id',
                        'u.username',
                        't.first_name',
                        't.last_name'
                    )
                    ->get()
                    ->groupBy('student_id');
            }

            $testsByPeriod = DB::table('tests as t')
                ->leftJoin('users as u', 't.examiner_id', '=', 'u.user_id')
                ->whereIn('t.period_id', $periodIds)
                ->select(
                    't.period_id',
                    't.status',
                    't.test_date',
                    't.updated_at',
                    'u.role as examiner_role'
                )
                ->get()
                ->groupBy('period_id');

            $summariesByPeriod = DB::table('period_summary_scores as ps')
                ->whereIn('ps.period_id', $periodIds)
                ->select(
                    'ps.period_id',
                    'ps.teachers_standard_score_avg',
                    'ps.family_standard_score',
                    'ps.final_standard_score',
                    'ps.final_interpretation',
                    'ps.teacher_discrepancy',
                    'ps.teacher_family_discrepancy',
                    'ps.created_at',
                    'ps.updated_at'
                )
                ->get()
                ->keyBy('period_id');
        }

        $rows = $periods->getCollection()->map(function ($p) use ($assignedTeachersByStudent, $testsByPeriod, $summariesByPeriod, $now) {
            $tests = $testsByPeriod->get($p->period_id, collect());
            $summary = $summariesByPeriod->get($p->period_id);

            $teacherTests = $tests->where('examiner_role', 'teacher');
            $familyTests = $tests->where('examiner_role', 'family');

            $totalTeacherTests = $teacherTests->count();
            $completedTeacherTests = $teacherTests->filter(function ($t) {
                return in_array($t->status, ['completed', 'finalized'], true);
            })->count();

            $teacherProgressLabel = $totalTeacherTests > 0
                ? $completedTeacherTests . ' / ' . $totalTeacherTests . ' completed'
                : 'No tests';

            $familyStatusLabel = 'Not Started';
            if ($familyTests->isNotEmpty()) {
                $hasInProgress = $familyTests->contains(function ($t) {
                    return $t->status === 'in_progress';
                });
                $hasCompleted = $familyTests->contains(function ($t) {
                    return in_array($t->status, ['completed', 'finalized'], true);
                });
                if ($hasInProgress) {
                    $familyStatusLabel = 'In Progress';
                } elseif ($hasCompleted) {
                    $familyStatusLabel = 'Completed';
                }
            }

            $finalScoreStatus = ($summary && $summary->final_standard_score !== null)
                ? 'Computed'
                : 'Not Computed';

            $lastActivity = null;
            if ($tests->isNotEmpty()) {
                $lastActivity = $tests->max(function ($t) {
                    return $t->updated_at ?? $t->test_date;
                });
            }
            if ($summary && $summary->updated_at) {
                if ($lastActivity === null || $summary->updated_at > $lastActivity) {
                    $lastActivity = $summary->updated_at;
                }
            }
            if ($lastActivity === null) {
                $lastActivity = $p->updated_at ?? $p->start_date;
            }

            $computedStatus = $p->status;
            $hasOngoing = $tests->contains(function ($t) {
                return $t->status === 'in_progress';
            });
            if ($hasOngoing && $p->status !== 'completed' && $p->status !== 'overdue') {
                $computedStatus = 'ongoing';
            }

            $p->computed_status = $computedStatus;
            $p->teacher_progress_label = $teacherProgressLabel;
            $p->family_status_label = $familyStatusLabel;
            $p->final_score_status = $finalScoreStatus;
            $p->last_activity = $lastActivity ? Carbon::parse($lastActivity) : null;
            $p->assigned_teachers = $assignedTeachersByStudent->get($p->student_id, collect());
            $p->summary = $summary;

            return $p;
        });

        $periods->setCollection($rows);

        // Alerts panel
        $alerts = [
            'overdue' => [],
            'missing_teacher' => [],
            'missing_family' => [],
            'major_discrepancy' => [],
            'stuck_tests' => [],
        ];

        $alerts['overdue'] = DB::table('assessment_periods as p')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->leftJoin('families as f', 's.family_id', '=', 'f.user_id')
            ->where('p.status', 'overdue')
            ->orderBy('p.end_date')
            ->limit(10)
            ->get([
                'p.period_id',
                'p.description as period_description',
                'p.end_date',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
                'f.family_name',
            ]);

        $alerts['missing_teacher'] = DB::table('assessment_periods as p')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->whereNotExists(function ($q) {
                $q->from('tests as t')
                    ->join('users as u', 't.examiner_id', '=', 'u.user_id')
                    ->whereColumn('t.period_id', 'p.period_id')
                    ->where('u.role', 'teacher')
                    ->whereIn('t.status', ['completed', 'finalized']);
            })
            ->orderBy('p.start_date')
            ->limit(10)
            ->get([
                'p.period_id',
                'p.description as period_description',
                'p.start_date',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
            ]);

        $alerts['missing_family'] = DB::table('assessment_periods as p')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->whereNotExists(function ($q) {
                $q->from('tests as t')
                    ->join('users as u', 't.examiner_id', '=', 'u.user_id')
                    ->whereColumn('t.period_id', 'p.period_id')
                    ->where('u.role', 'family')
                    ->whereIn('t.status', ['completed', 'finalized']);
            })
            ->orderBy('p.start_date')
            ->limit(10)
            ->get([
                'p.period_id',
                'p.description as period_description',
                'p.start_date',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
            ]);

        $alerts['major_discrepancy'] = DB::table('assessment_periods as p')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->join('period_summary_scores as ps', 'ps.period_id', '=', 'p.period_id')
            ->where(function ($q) {
                $q->where('ps.teacher_discrepancy', 'major')
                    ->orWhere('ps.teacher_family_discrepancy', 'major');
            })
            ->orderByDesc('ps.updated_at')
            ->limit(10)
            ->get([
                'p.period_id',
                'p.description as period_description',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
                'ps.teacher_discrepancy',
                'ps.teacher_family_discrepancy',
            ]);

        $threshold = $now->copy()->subDays(7)->toDateString();
        $alerts['stuck_tests'] = DB::table('tests as t')
            ->join('assessment_periods as p', 't.period_id', '=', 'p.period_id')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->where('t.status', 'in_progress')
            ->whereDate('t.test_date', '<=', $threshold)
            ->orderBy('t.test_date')
            ->limit(10)
            ->get([
                't.test_id',
                't.test_date',
                'p.period_id',
                'p.description as period_description',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
            ]);

        $teacherOptions = DB::table('teachers as t')
            ->join('users as u', 't.user_id', '=', 'u.user_id')
            ->orderBy('t.last_name')
            ->orderBy('t.first_name')
            ->get([
                't.user_id',
                't.first_name',
                't.last_name',
                'u.username',
            ]);

        return view('admin.assessments', [
            'periods' => $periods,
            'teacherOptions' => $teacherOptions,
            'alerts' => $alerts,
            'totalPeriods' => $totalPeriods,
            'scheduledPeriods' => $scheduledPeriods,
            'completedPeriods' => $completedPeriods,
            'overduePeriods' => $overduePeriods,
            'ongoingAssessments' => $ongoingAssessments,
            'testsAwaitingFinalization' => $testsAwaitingFinalization,
        ]);
    }


    public function reports(Request $request)
    {
        $now = Carbon::now();

        // Shared filters
        $ageMinMonths = $request->input('age_min_months');
        $ageMaxMonths = $request->input('age_max_months');
        $teacherId = $request->input('teacher_id');
        $domainId = $request->input('domain_id');
        $interpretationFilter = $request->input('interpretation');
        $scaleVersionId = $request->input('scale_version_id');
        $periodId = $request->input('period_id');

        $dobMin = null;
        $dobMax = null;
        if ($ageMinMonths !== null && is_numeric($ageMinMonths)) {
            $dobMax = $now->copy()->subMonths((int) $ageMinMonths)->toDateString();
        }
        if ($ageMaxMonths !== null && is_numeric($ageMaxMonths)) {
            $dobMin = $now->copy()->subMonths((int) $ageMaxMonths + 1)->addDay()->toDateString();
        }

        $interpretationLike = null;
        if ($interpretationFilter === 'advanced') {
            $interpretationLike = '%advanced%';
        } elseif ($interpretationFilter === 'average') {
            $interpretationLike = '%average%';
        } elseif ($interpretationFilter === 'retest') {
            $interpretationLike = '%re-test%';
        }

        // 1. Student Development Overview
        $totalStudents = DB::table('students')->count();

        $completedPeriodsQuery = DB::table('assessment_periods as p')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->where('p.status', 'completed');

        if ($dobMin) {
            $completedPeriodsQuery->whereDate('s.date_of_birth', '>=', $dobMin);
        }
        if ($dobMax) {
            $completedPeriodsQuery->whereDate('s.date_of_birth', '<=', $dobMax);
        }
        if ($periodId) {
            $completedPeriodsQuery->where('p.period_id', $periodId);
        }

        $totalCompletedPeriods = $completedPeriodsQuery->count();

        $summaryQuery = DB::table('period_summary_scores as ps')
            ->join('assessment_periods as p', 'ps.period_id', '=', 'p.period_id')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->leftJoin('families as f', 's.family_id', '=', 'f.user_id')
            ->whereNotNull('ps.final_interpretation');

        if ($dobMin) {
            $summaryQuery->whereDate('s.date_of_birth', '>=', $dobMin);
        }
        if ($dobMax) {
            $summaryQuery->whereDate('s.date_of_birth', '<=', $dobMax);
        }
        if ($periodId) {
            $summaryQuery->where('p.period_id', $periodId);
        }
        if ($interpretationLike) {
            $summaryQuery->where('ps.final_interpretation', 'like', $interpretationLike);
        }
        if ($teacherId) {
            $summaryQuery->whereExists(function ($q) use ($teacherId) {
                $q->from('tests as t')
                    ->join('users as u', 't.examiner_id', '=', 'u.user_id')
                    ->whereColumn('t.period_id', 'p.period_id')
                    ->where('u.role', 'teacher')
                    ->where('u.user_id', $teacherId)
                    ->whereIn('t.status', ['completed', 'finalized']);
            });
        }

        $summaryRows = $summaryQuery
            ->orderByDesc('ps.created_at')
            ->get([
                'ps.*',
                'p.period_id',
                'p.status as period_status',
                'p.end_date',
                'p.description as period_description',
                's.student_id',
                's.first_name',
                's.last_name',
                'f.family_name',
            ]);

        $latestByStudent = $summaryRows
            ->groupBy('student_id')
            ->map(function ($items) {
                return $items->first();
            });

        $studentInterpretationCounts = [
            'advanced' => 0,
            'average' => 0,
            'retest' => 0,
        ];

        foreach ($latestByStudent as $row) {
            $label = strtolower((string) $row->final_interpretation);
            if (str_contains($label, 'advanced')) {
                $studentInterpretationCounts['advanced']++;
            } elseif (str_contains($label, 'average')) {
                $studentInterpretationCounts['average']++;
            } elseif (str_contains($label, 're-test') || str_contains($label, 'retest')) {
                $studentInterpretationCounts['retest']++;
            }
        }

        $majorDiscrepancyStudentsCount = $latestByStudent
            ->filter(function ($row) {
                return $row->teacher_discrepancy === 'major'
                    || $row->teacher_family_discrepancy === 'major';
            })
            ->count();

        $recentCompletedAssessments = $summaryRows
            ->where('period_status', 'completed')
            ->sortByDesc('end_date')
            ->take(10)
            ->values();

        // 3. Domain Performance Report (school-level)
        $domainBase = DB::table('test_domain_scaled_scores as ds')
            ->join('domains as d', 'ds.domain_id', '=', 'd.domain_id')
            ->join('tests as t', 'ds.test_id', '=', 't.test_id')
            ->join('assessment_periods as p', 't.period_id', '=', 'p.period_id')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->whereIn('t.status', ['completed', 'finalized']);

        if ($domainId) {
            $domainBase->where('ds.domain_id', $domainId);
        }
        if ($scaleVersionId) {
            $domainBase->where('ds.scale_version_id', $scaleVersionId);
        }
        if ($dobMin) {
            $domainBase->whereDate('s.date_of_birth', '>=', $dobMin);
        }
        if ($dobMax) {
            $domainBase->whereDate('s.date_of_birth', '<=', $dobMax);
        }
        if ($periodId) {
            $domainBase->where('p.period_id', $periodId);
        }
        if ($teacherId) {
            $domainBase->join('users as du', 't.examiner_id', '=', 'du.user_id')
                ->where('du.role', 'teacher')
                ->where('du.user_id', $teacherId);
        }

        $domainRows = $domainBase->get([
            'd.domain_id',
            'd.name as domain_name',
            'ds.raw_score',
            'ds.scaled_score',
            's.date_of_birth',
            't.test_date',
        ]);

        $domainPerformance = [];
        $domainOverall = [];

        $ageRange = function (int $months): string {
            if ($months < 37) {
                return '0–3.0 yrs';
            } elseif ($months <= 48) {
                return '3.1–4.0 yrs';
            } elseif ($months <= 60) {
                return '4.1–5.0 yrs';
            } elseif ($months <= 72) {
                return '5.1–6.0 yrs';
            }
            return '6+ yrs';
        };

        foreach ($domainRows as $row) {
            if (!$row->date_of_birth || !$row->test_date) {
                continue;
            }
            try {
                $dob = Carbon::parse($row->date_of_birth);
                $testDate = Carbon::parse($row->test_date);
                $months = $dob->diffInMonths($testDate);
            } catch (\Throwable $e) {
                continue;
            }

            $bucket = $ageRange($months);
            $dKey = $row->domain_id;

            if (!isset($domainPerformance[$dKey])) {
                $domainPerformance[$dKey] = [
                    'domain_name' => $row->domain_name,
                    'age_buckets' => [],
                ];
                $domainOverall[$dKey] = [
                    'sum_scaled' => 0,
                    'count' => 0,
                ];
            }

            if (!isset($domainPerformance[$dKey]['age_buckets'][$bucket])) {
                $domainPerformance[$dKey]['age_buckets'][$bucket] = [
                    'sum_raw' => 0,
                    'sum_scaled' => 0,
                    'count' => 0,
                ];
            }

            $domainPerformance[$dKey]['age_buckets'][$bucket]['sum_raw'] += (float) $row->raw_score;
            $domainPerformance[$dKey]['age_buckets'][$bucket]['sum_scaled'] += (float) $row->scaled_score;
            $domainPerformance[$dKey]['age_buckets'][$bucket]['count']++;

            $domainOverall[$dKey]['sum_scaled'] += (float) $row->scaled_score;
            $domainOverall[$dKey]['count']++;
        }

        $strongestDomain = null;
        $weakestDomain = null;
        $strongestAvg = null;
        $weakestAvg = null;

        foreach ($domainOverall as $dKey => $agg) {
            if ($agg['count'] <= 0) {
                continue;
            }
            $avgScaled = $agg['sum_scaled'] / $agg['count'];
            if ($strongestAvg === null || $avgScaled > $strongestAvg) {
                $strongestAvg = $avgScaled;
                $strongestDomain = $domainPerformance[$dKey]['domain_name'];
            }
            if ($weakestAvg === null || $avgScaled < $weakestAvg) {
                $weakestAvg = $avgScaled;
                $weakestDomain = $domainPerformance[$dKey]['domain_name'];
            }
        }

        // 4. Teacher Consistency Report
        $teacherBase = DB::table('tests as t')
            ->join('users as u', 't.examiner_id', '=', 'u.user_id')
            ->join('assessment_periods as p', 't.period_id', '=', 'p.period_id')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->leftJoin('period_summary_scores as ps', 'ps.period_id', '=', 'p.period_id')
            ->leftJoin('test_standard_scores as ts', 'ts.test_id', '=', 't.test_id')
            ->where('u.role', 'teacher')
            ->whereIn('t.status', ['completed', 'finalized']);

        if ($dobMin) {
            $teacherBase->whereDate('s.date_of_birth', '>=', $dobMin);
        }
        if ($dobMax) {
            $teacherBase->whereDate('s.date_of_birth', '<=', $dobMax);
        }
        if ($periodId) {
            $teacherBase->where('p.period_id', $periodId);
        }
        if ($scaleVersionId) {
            $teacherBase->where('ts.scale_version_id', $scaleVersionId);
        }

        $teacherRows = $teacherBase->get([
            'u.user_id',
            'u.username',
            't.test_id',
            'p.period_id',
            'ts.standard_score',
            'ps.teacher_discrepancy',
            'ps.teacher_family_discrepancy',
        ]);

        $teacherConsistency = [];

        foreach ($teacherRows->groupBy('user_id') as $teacherIdKey => $rows) {
            $completedAssessments = $rows->pluck('test_id')->unique()->count();
            $avgStandard = $rows->whereNotNull('standard_score')->avg('standard_score');

            $periods = $rows->pluck('period_id')->unique();
            $periodStats = $rows
                ->whereIn('period_id', $periods)
                ->groupBy('period_id')
                ->map(function ($pr) {
                    $first = $pr->first();
                    return [
                        'teacher_discrepancy' => $first->teacher_discrepancy,
                        'teacher_family_discrepancy' => $first->teacher_family_discrepancy,
                    ];
                });

            $totalPeriods = max(1, $periodStats->count());
            $withTeacherDisc = $periodStats->filter(function ($p) {
                return $p['teacher_discrepancy'] && $p['teacher_discrepancy'] !== 'none';
            })->count();
            $withFamilyDisc = $periodStats->filter(function ($p) {
                return $p['teacher_family_discrepancy'] && $p['teacher_family_discrepancy'] !== 'none';
            })->count();

            $first = $rows->first();
            $teacherConsistency[] = (object) [
                'user_id' => $teacherIdKey,
                'username' => $first->username,
                'completed_assessments' => $completedAssessments,
                'avg_standard_score' => $avgStandard,
                'discrepancy_with_teachers_rate' => $withTeacherDisc / $totalPeriods,
                'discrepancy_with_families_rate' => $withFamilyDisc / $totalPeriods,
            ];
        }

        // 5. Teacher vs Family Comparison Report
        $tfBase = DB::table('period_summary_scores as ps')
            ->join('assessment_periods as p', 'ps.period_id', '=', 'p.period_id')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->whereNotNull('ps.teachers_standard_score_avg')
            ->whereNotNull('ps.family_standard_score');

        if ($dobMin) {
            $tfBase->whereDate('s.date_of_birth', '>=', $dobMin);
        }
        if ($dobMax) {
            $tfBase->whereDate('s.date_of_birth', '<=', $dobMax);
        }
        if ($periodId) {
            $tfBase->where('p.period_id', $periodId);
        }
        if ($interpretationLike) {
            $tfBase->where('ps.final_interpretation', 'like', $interpretationLike);
        }

        $tfRows = $tfBase->get([
            'ps.teachers_standard_score_avg',
            'ps.family_standard_score',
            'ps.teacher_family_discrepancy',
        ]);

        $teacherFamilyComparison = [
            'avg_teacher_score' => null,
            'avg_family_score' => null,
            'pct_minor_discrepancy' => null,
            'pct_major_discrepancy' => null,
        ];

        if ($tfRows->count() > 0) {
            $teacherFamilyComparison['avg_teacher_score'] = $tfRows->avg('teachers_standard_score_avg');
            $teacherFamilyComparison['avg_family_score'] = $tfRows->avg('family_standard_score');

            $total = $tfRows->count();
            $minor = $tfRows->where('teacher_family_discrepancy', 'minor')->count();
            $major = $tfRows->where('teacher_family_discrepancy', 'major')->count();
            $teacherFamilyComparison['pct_minor_discrepancy'] = $total ? ($minor / $total) : 0;
            $teacherFamilyComparison['pct_major_discrepancy'] = $total ? ($major / $total) : 0;
        }

        // 6. Assessment Monitoring Report
        $monitorOverdue = DB::table('assessment_periods as p')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->leftJoin('families as f', 's.family_id', '=', 'f.user_id')
            ->where('p.status', 'overdue')
            ->orderBy('p.end_date')
            ->limit(10)
            ->get([
                'p.period_id',
                'p.description as period_description',
                'p.end_date',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
                'f.family_name',
            ]);

        $monitorMissingTeacher = DB::table('assessment_periods as p')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->whereNotExists(function ($q) {
                $q->from('tests as t')
                    ->join('users as u', 't.examiner_id', '=', 'u.user_id')
                    ->whereColumn('t.period_id', 'p.period_id')
                    ->where('u.role', 'teacher')
                    ->whereIn('t.status', ['completed', 'finalized']);
            })
            ->orderBy('p.start_date')
            ->limit(10)
            ->get([
                'p.period_id',
                'p.description as period_description',
                'p.start_date',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
            ]);

        $monitorMissingFamily = DB::table('assessment_periods as p')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->whereNotExists(function ($q) {
                $q->from('tests as t')
                    ->join('users as u', 't.examiner_id', '=', 'u.user_id')
                    ->whereColumn('t.period_id', 'p.period_id')
                    ->where('u.role', 'family')
                    ->whereIn('t.status', ['completed', 'finalized']);
            })
            ->orderBy('p.start_date')
            ->limit(10)
            ->get([
                'p.period_id',
                'p.description as period_description',
                'p.start_date',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
            ]);

        $monitorInProgressTests = DB::table('tests as t')
            ->join('assessment_periods as p', 't.period_id', '=', 'p.period_id')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->where('t.status', 'in_progress')
            ->orderBy('t.test_date')
            ->limit(10)
            ->get([
                't.test_id',
                't.test_date',
                'p.period_id',
                'p.description as period_description',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
            ]);

        // 7. Scale Version Usage Report
        $scaleVersions = DB::table('scale_versions')
            ->orderBy('name')
            ->get(['scale_version_id', 'name']);

        $scaleUsage = DB::table('test_standard_scores as ts')
            ->select('ts.scale_version_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('ts.scale_version_id')
            ->pluck('cnt', 'scale_version_id');

        // 8. Red Flag Report
        $redFlagBase = DB::table('period_summary_scores as ps')
            ->join('assessment_periods as p', 'ps.period_id', '=', 'p.period_id')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->leftJoin('families as f', 's.family_id', '=', 'f.user_id');

        if ($dobMin) {
            $redFlagBase->whereDate('s.date_of_birth', '>=', $dobMin);
        }
        if ($dobMax) {
            $redFlagBase->whereDate('s.date_of_birth', '<=', $dobMax);
        }
        if ($periodId) {
            $redFlagBase->where('p.period_id', $periodId);
        }

        $redFlags = $redFlagBase
            ->where(function ($q) {
                $q->where('ps.teacher_discrepancy', 'major')
                    ->orWhere('ps.teacher_family_discrepancy', 'major')
                    ->orWhere(function ($qq) {
                        $qq->whereNotNull('ps.final_interpretation')
                            ->where('ps.final_interpretation', 'like', '%re-test%');
                    })
                    ->orWhere(function ($qq) {
                        $qq->whereNotNull('ps.final_standard_score')
                            ->where('ps.final_standard_score', '<', 85);
                    });
            })
            ->orderByDesc('ps.updated_at')
            ->limit(50)
            ->get([
                'p.period_id',
                'p.description as period_description',
                'p.status as period_status',
                'ps.final_standard_score',
                'ps.final_interpretation',
                'ps.teacher_discrepancy',
                'ps.teacher_family_discrepancy',
                's.student_id',
                's.first_name',
                's.last_name',
                'f.family_name',
            ]);

        // Filter dropdown data
        $teacherOptions = DB::table('teachers as t')
            ->join('users as u', 't.user_id', '=', 'u.user_id')
            ->orderBy('t.last_name')
            ->orderBy('t.first_name')
            ->get([
                't.user_id',
                't.first_name',
                't.last_name',
                'u.username',
            ]);

        $domainOptions = DB::table('domains')
            ->orderBy('name')
            ->get([
                'domain_id',
                'name',
            ]);

        $periodOptions = DB::table('assessment_periods')
            ->orderByDesc('start_date')
            ->limit(30)
            ->get([
                'period_id',
                'description',
            ]);

        return view('admin.reports', [
            'filters' => [
                'age_min_months' => $ageMinMonths,
                'age_max_months' => $ageMaxMonths,
                'teacher_id' => $teacherId,
                'domain_id' => $domainId,
                'interpretation' => $interpretationFilter,
                'scale_version_id' => $scaleVersionId,
                'period_id' => $periodId,
            ],
            'totalStudents' => $totalStudents,
            'totalCompletedPeriods' => $totalCompletedPeriods,
            'studentInterpretationCounts' => $studentInterpretationCounts,
            'majorDiscrepancyStudentsCount' => $majorDiscrepancyStudentsCount,
            'recentCompletedAssessments' => $recentCompletedAssessments,
            'domainPerformance' => $domainPerformance,
            'strongestDomain' => $strongestDomain,
            'weakestDomain' => $weakestDomain,
            'teacherConsistency' => $teacherConsistency,
            'teacherFamilyComparison' => $teacherFamilyComparison,
            'monitorOverdue' => $monitorOverdue,
            'monitorMissingTeacher' => $monitorMissingTeacher,
            'monitorMissingFamily' => $monitorMissingFamily,
            'monitorInProgressTests' => $monitorInProgressTests,
            'scaleVersions' => $scaleVersions,
            'scaleUsage' => $scaleUsage,
            'redFlags' => $redFlags,
            'teacherOptions' => $teacherOptions,
            'domainOptions' => $domainOptions,
            'periodOptions' => $periodOptions,
        ]);
    }

    public function reportsExport(Request $request, $format)
    {
        // Simple CSV export of red-flagged periods; PDF is stubbed for later integration.
        if ($format === 'excel' || $format === 'csv') {
            $rows = DB::table('period_summary_scores as ps')
                ->join('assessment_periods as p', 'ps.period_id', '=', 'p.period_id')
                ->join('students as s', 'p.student_id', '=', 's.student_id')
                ->leftJoin('families as f', 's.family_id', '=', 'f.user_id')
                ->where(function ($q) {
                    $q->where('ps.teacher_discrepancy', 'major')
                        ->orWhere('ps.teacher_family_discrepancy', 'major')
                        ->orWhere(function ($qq) {
                            $qq->whereNotNull('ps.final_interpretation')
                                ->where('ps.final_interpretation', 'like', '%re-test%');
                        })
                        ->orWhere(function ($qq) {
                            $qq->whereNotNull('ps.final_standard_score')
                                ->where('ps.final_standard_score', '<', 85);
                        });
                })
                ->orderByDesc('ps.updated_at')
                ->limit(500)
                ->get([
                    's.first_name',
                    's.last_name',
                    'f.family_name',
                    'p.description as period_description',
                    'ps.final_standard_score',
                    'ps.final_interpretation',
                    'ps.teacher_discrepancy',
                    'ps.teacher_family_discrepancy',
                ]);

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="admin_reports_red_flags.csv"',
            ];

            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, [
                    'Student',
                    'Family',
                    'Period',
                    'Final Score',
                    'Final Interpretation',
                    'Teacher Discrepancy',
                    'Teacher–Family Discrepancy',
                ]);
                foreach ($rows as $r) {
                    fputcsv($out, [
                        trim($r->first_name . ' ' . $r->last_name),
                        $r->family_name,
                        $r->period_description,
                        $r->final_standard_score,
                        $r->final_interpretation,
                        $r->teacher_discrepancy,
                        $r->teacher_family_discrepancy,
                    ]);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        }

        // PDF not wired yet; keep UX consistent with assessments export.
        return redirect()
            ->route('admin.reports', $request->query())
            ->with('info', 'PDF export is not yet configured.');
    }

    public function scales()
    {
        return view('admin.scales');
    }

    public function profile()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        return view('admin.profile', [
            'user' => $user,
        ]);
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->user_id . ',user_id',
            'email' => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $updateData = [
            'username' => $validated['username'],
            'email' => $validated['email'],
            'updated_at' => Carbon::now(),
        ];

        if ($request->hasFile('profile_image')) {
            try {
                $storedPath = $request->file('profile_image')->store('avatars', 'public');
                $updateData['profile_path'] = '/storage/' . $storedPath;
            } catch (\Throwable $e) {
                return back()->withInput()->with('error', 'Failed to upload profile picture.');
            }
        }

        DB::table('users')
            ->where('user_id', $user->user_id)
            ->update($updateData);

        return redirect()
            ->route('admin.profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function profilePassword(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        DB::table('users')
            ->where('user_id', $user->user_id)
            ->update([
                'password' => Hash::make($validated['password']),
                'updated_at' => Carbon::now(),
            ]);

        return redirect()
            ->route('admin.profile')
            ->with('success', 'Password updated successfully.');
    }

    public function assessmentsShow($periodId)
    {
        $period = DB::table('assessment_periods as p')
            ->join('students as s', 'p.student_id', '=', 's.student_id')
            ->leftJoin('families as f', 's.family_id', '=', 'f.user_id')
            ->leftJoin('users as fu', 'f.user_id', '=', 'fu.user_id')
            ->where('p.period_id', $periodId)
            ->select(
                'p.*',
                's.student_id',
                's.first_name as student_first_name',
                's.last_name as student_last_name',
                's.date_of_birth',
                's.feature_path as student_feature_path',
                'f.family_name',
                'f.home_address as family_home_address',
                'f.emergency_contact',
                'f.emergency_phone',
                'fu.email as family_email'
            )
            ->first();

        if (!$period) {
            abort(404);
        }

        $ageYearsAtStart = null;
        if ($period->date_of_birth && $period->start_date) {
            try {
                $dob = Carbon::parse($period->date_of_birth);
                $start = Carbon::parse($period->start_date);
                $ageYearsAtStart = $dob->diffInYears($start);
            } catch (\Throwable $e) {
                $ageYearsAtStart = null;
            }
        }

        $teachers = DB::table('student_teacher as st')
            ->join('teachers as t', 'st.teacher_id', '=', 't.user_id')
            ->join('users as u', 't.user_id', '=', 'u.user_id')
            ->where('st.student_id', $period->student_id)
            ->orderBy('t.last_name')
            ->orderBy('t.first_name')
            ->get([
                'u.user_id',
                'u.username',
                't.first_name',
                't.last_name',
            ]);

        $tests = DB::table('tests as t')
            ->leftJoin('users as u', 't.examiner_id', '=', 'u.user_id')
            ->where('t.period_id', $periodId)
            ->orderByDesc('t.test_date')
            ->get([
                't.test_id',
                't.test_date',
                't.status',
                't.notes',
                't.updated_at',
                'u.username as examiner_username',
                'u.role as examiner_role',
            ]);

        $summary = DB::table('period_summary_scores as ps')
            ->where('ps.period_id', $periodId)
            ->select(
                'ps.period_id',
                'ps.teachers_standard_score_avg',
                'ps.family_standard_score',
                'ps.final_standard_score',
                'ps.final_interpretation',
                'ps.teacher_discrepancy',
                'ps.teacher_family_discrepancy'
            )
            ->first();

        $latestCompletedTest = null;
        foreach ($tests as $t) {
            if (in_array($t->status, ['completed', 'finalized'], true)) {
                $latestCompletedTest = $t;
                break;
            }
        }

        $domainScores = collect();
        if ($latestCompletedTest) {
            $domainScores = DB::table('test_domain_scaled_scores as ds')
                ->join('domains as d', 'ds.domain_id', '=', 'd.domain_id')
                ->where('ds.test_id', $latestCompletedTest->test_id)
                ->orderBy('d.name')
                ->get([
                    'd.name as domain_name',
                    'ds.raw_score',
                    'ds.scaled_score',
                ]);
        }

        $teacherStandardScores = collect();
        if ($tests->isNotEmpty()) {
            $teacherTestIds = $tests
                ->filter(function ($t) {
                    return $t->examiner_role === 'teacher'
                        && in_array($t->status, ['completed', 'finalized'], true);
                })
                ->pluck('test_id')
                ->all();

            if (!empty($teacherTestIds)) {
                $teacherStandardScores = DB::table('test_standard_scores as ts')
                    ->join('tests as t', 'ts.test_id', '=', 't.test_id')
                    ->join('users as u', 't.examiner_id', '=', 'u.user_id')
                    ->whereIn('ts.test_id', $teacherTestIds)
                    ->get([
                        'ts.test_id',
                        'ts.standard_score',
                        'ts.interpretation',
                        'u.username as teacher_username',
                    ]);
            }
        }

        // Simple examiner list (unique usernames/roles from tests)
        $examiners = $tests
            ->filter(function ($t) {
                return $t->examiner_username !== null;
            })
            ->map(function ($t) {
                return $t->examiner_username . '|' . ($t->examiner_role ?? '');
            })
            ->unique()
            ->map(function ($key) {
                [$name, $role] = explode('|', $key . '|');
                return (object) [
                    'username' => $name,
                    'role' => $role,
                ];
            });

        return view('admin.assessments_show', [
            'period' => $period,
            'ageYearsAtStart' => $ageYearsAtStart,
            'teachers' => $teachers,
            'tests' => $tests,
            'summary' => $summary,
            'domainScores' => $domainScores,
            'teacherStandardScores' => $teacherStandardScores,
            'examiners' => $examiners,
        ]);
    }

    public function assessmentsExtend(Request $request, $periodId)
    {
        $validated = $request->validate([
            'end_date' => 'required|date',
        ]);

        $updated = DB::table('assessment_periods')
            ->where('period_id', $periodId)
            ->update([
                'end_date' => $validated['end_date'],
            ]);

        if (!$updated) {
            return back()->with('error', 'Unable to update assessment period end date.');
        }

        return redirect()
            ->route('admin.assessments.show', $periodId)
            ->with('success', 'Assessment period deadline updated.');
    }

    public function assessmentsClose(Request $request, $periodId)
    {
        $updated = DB::table('assessment_periods')
            ->where('period_id', $periodId)
            ->update([
                'status' => 'completed',
            ]);

        if (!$updated) {
            return back()->with('error', 'Unable to mark assessment period as closed.');
        }

        return redirect()
            ->route('admin.assessments.show', $periodId)
            ->with('success', 'Assessment period marked as closed.');
    }

    public function assessmentsRecompute($periodId)
    {
        // Placeholder: hook up to scoring service when available.
        return redirect()
            ->route('admin.assessments.show', $periodId)
            ->with('info', 'Recompute requested. Connect this action to the scoring engine.');
    }

    public function assessmentsExport($periodId)
    {
        // For now, export is a simple HTML view; wire to a PDF generator later.
        return redirect()
            ->route('admin.assessments.show', $periodId)
            ->with('info', 'PDF export is not yet configured.');
    }

    public function assessmentsNotify(Request $request, $periodId)
    {
        $target = $request->input('target', 'teachers');

        // Stub: integrate with notification/email system later.
        $message = $target === 'family'
            ? 'Notification to family queued (stub only).'
            : 'Notifications to teachers queued (stub only).';

        return redirect()
            ->route('admin.assessments.show', $periodId)
            ->with('info', $message);
    }

    public function usersShow($userId)
    {
        $user = DB::table('users as u')
            ->leftJoin('teachers as t', 't.user_id', '=', 'u.user_id')
            ->leftJoin('families as f', 'f.user_id', '=', 'u.user_id')
            ->where('u.user_id', $userId)
            ->select(
                'u.*',
                't.first_name as teacher_first_name',
                't.last_name as teacher_last_name',
                't.home_address as teacher_home_address',
                't.phone_number as teacher_phone_number',
                't.hire_date as teacher_hire_date',
                't.feature_path as teacher_feature_path',
                'f.family_name as family_name',
                'f.home_address as family_home_address',
                'f.emergency_contact as family_emergency_contact',
                'f.emergency_phone as family_emergency_phone',
                'f.feature_path as family_feature_path'
            )
            ->first();

        if (!$user) {
            abort(404);
        }

        $teacherStudents = collect();
        $teacherAssessmentsCount = 0;
        $familyChildren = collect();
        $familyChildrenTeachers = collect();
        $familyCompletedTests = 0;

        if ($user->role === 'teacher') {
            $teacherStudents = DB::table('student_teacher as st')
                ->join('students as s', 'st.student_id', '=', 's.student_id')
                ->where('st.teacher_id', $user->user_id)
                ->orderBy('s.last_name')
                ->orderBy('s.first_name')
                ->get([
                    's.student_id',
                    's.first_name',
                    's.last_name',
                    's.date_of_birth',
                ]);

            $teacherAssessmentsCount = DB::table('tests as t')
                ->where('t.examiner_id', $user->user_id)
                ->count();
        } elseif ($user->role === 'family') {
            $familyChildren = DB::table('students as s')
                ->where('s.family_id', $user->user_id)
                ->orderBy('s.last_name')
                ->orderBy('s.first_name')
                ->get([
                    's.student_id',
                    's.first_name',
                    's.last_name',
                    's.date_of_birth',
                ]);

            if ($familyChildren->isNotEmpty()) {
                $childIds = $familyChildren->pluck('student_id')->all();

                $familyChildrenTeachers = DB::table('student_teacher as st')
                    ->join('teachers as t', 'st.teacher_id', '=', 't.user_id')
                    ->join('users as u', 't.user_id', '=', 'u.user_id')
                    ->whereIn('st.student_id', $childIds)
                    ->get([
                        'st.student_id',
                        'u.username as teacher_username',
                        't.first_name',
                        't.last_name',
                    ]);
            }

            $familyCompletedTests = DB::table('tests as t')
                ->where('t.examiner_id', $user->user_id)
                ->whereIn('t.status', ['completed', 'finalized'])
                ->count();
        }

        return view('admin.users_show', [
            'user' => $user,
            'teacherStudents' => $teacherStudents,
            'teacherAssessmentsCount' => $teacherAssessmentsCount,
            'familyChildren' => $familyChildren,
            'familyChildrenTeachers' => $familyChildrenTeachers,
            'familyCompletedTests' => $familyCompletedTests,
        ]);
    }

    public function usersCreate()
    {
        return view('admin.users_form', [
            'mode' => 'create',
            'user' => null,
        ]);
    }

    public function usersStore(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,teacher,family',
            'teacher_first_name' => 'required_if:role,teacher|nullable|string|max:255',
            'teacher_last_name' => 'required_if:role,teacher|nullable|string|max:255',
            'teacher_home_address' => 'required_if:role,teacher|nullable|string|max:255',
            'teacher_phone_number' => 'required_if:role,teacher|nullable|string|max:50',
            'teacher_hire_date' => 'required_if:role,teacher|nullable|date',
            'family_name' => 'required_if:role,family|nullable|string|max:255',
            'family_home_address' => 'required_if:role,family|nullable|string|max:255',
            'family_emergency_contact' => 'required_if:role,family|nullable|string|max:255',
            'family_emergency_phone' => 'required_if:role,family|nullable|string|max:50',
        ]);

        DB::beginTransaction();

        try {
            $userId = DB::table('users')->insertGetId([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'status' => 'active',
                'profile_path' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if ($validated['role'] === 'teacher') {
                DB::table('teachers')->insert([
                    'user_id' => $userId,
                    'first_name' => $validated['teacher_first_name'] ?? '',
                    'last_name' => $validated['teacher_last_name'] ?? '',
                    'home_address' => $validated['teacher_home_address'] ?? '',
                    'phone_number' => $validated['teacher_phone_number'] ?? '',
                    'hire_date' => $validated['teacher_hire_date'] ?? Carbon::now()->toDateString(),
                    'feature_path' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            } elseif ($validated['role'] === 'family') {
                DB::table('families')->insert([
                    'user_id' => $userId,
                    'family_name' => $validated['family_name'] ?? '',
                    'home_address' => $validated['family_home_address'] ?? '',
                    'emergency_contact' => $validated['family_emergency_contact'] ?? '',
                    'emergency_phone' => $validated['family_emergency_phone'] ?? '',
                    'feature_path' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.users.show', $userId)
                ->with('success', 'User created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create user.');
        }
    }

    public function usersEdit($userId)
    {
        $user = DB::table('users as u')
            ->leftJoin('teachers as t', 't.user_id', '=', 'u.user_id')
            ->leftJoin('families as f', 'f.user_id', '=', 'u.user_id')
            ->where('u.user_id', $userId)
            ->select(
                'u.*',
                't.first_name as teacher_first_name',
                't.last_name as teacher_last_name',
                't.home_address as teacher_home_address',
                't.phone_number as teacher_phone_number',
                't.hire_date as teacher_hire_date',
                'f.family_name as family_name',
                'f.home_address as family_home_address',
                'f.emergency_contact as family_emergency_contact',
                'f.emergency_phone as family_emergency_phone'
            )
            ->first();

        if (!$user) {
            abort(404);
        }

        return view('admin.users_form', [
            'mode' => 'edit',
            'user' => $user,
        ]);
    }

    public function usersUpdate(Request $request, $userId)
    {
        $existing = DB::table('users')->where('user_id', $userId)->first();
        if (!$existing) {
            abort(404);
        }

        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $userId . ',user_id',
            'email' => 'required|email|max:255|unique:users,email,' . $userId . ',user_id',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,teacher,family',
            'profile_path' => 'nullable|string|max:255',
            'confirm_role_change' => 'nullable|boolean',
            'teacher_first_name' => 'nullable|string|max:255',
            'teacher_last_name' => 'nullable|string|max:255',
            'teacher_home_address' => 'nullable|string|max:255',
            'teacher_phone_number' => 'nullable|string|max:50',
            'teacher_hire_date' => 'nullable|date',
            'family_name' => 'nullable|string|max:255',
            'family_home_address' => 'nullable|string|max:255',
            'family_emergency_contact' => 'nullable|string|max:255',
            'family_emergency_phone' => 'nullable|string|max:50',
        ]);

        if ($existing->role !== $validated['role'] && !$request->boolean('confirm_role_change')) {
            return back()->withInput()->with('error', 'Changing user role requires confirmation.');
        }

        DB::beginTransaction();

        try {
            $updateData = [
                'username' => $validated['username'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'profile_path' => $validated['profile_path'] ?? $existing->profile_path,
                'updated_at' => Carbon::now(),
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            DB::table('users')->where('user_id', $userId)->update($updateData);

            if ($validated['role'] === 'teacher') {
                $teacherRow = DB::table('teachers')->where('user_id', $userId)->first();
                $teacherData = [
                    'first_name' => $validated['teacher_first_name'] ?? ($teacherRow->first_name ?? ''),
                    'last_name' => $validated['teacher_last_name'] ?? ($teacherRow->last_name ?? ''),
                    'home_address' => $validated['teacher_home_address'] ?? ($teacherRow->home_address ?? ''),
                    'phone_number' => $validated['teacher_phone_number'] ?? ($teacherRow->phone_number ?? ''),
                    'hire_date' => $validated['teacher_hire_date'] ?? ($teacherRow->hire_date ?? Carbon::now()->toDateString()),
                    'updated_at' => Carbon::now(),
                ];
                if ($teacherRow) {
                    DB::table('teachers')->where('user_id', $userId)->update($teacherData);
                } else {
                    $teacherData['user_id'] = $userId;
                    $teacherData['feature_path'] = null;
                    $teacherData['created_at'] = Carbon::now();
                    DB::table('teachers')->insert($teacherData);
                }
            }

            if ($validated['role'] === 'family') {
                $familyRow = DB::table('families')->where('user_id', $userId)->first();
                $familyData = [
                    'family_name' => $validated['family_name'] ?? ($familyRow->family_name ?? ''),
                    'home_address' => $validated['family_home_address'] ?? ($familyRow->home_address ?? ''),
                    'emergency_contact' => $validated['family_emergency_contact'] ?? ($familyRow->emergency_contact ?? ''),
                    'emergency_phone' => $validated['family_emergency_phone'] ?? ($familyRow->emergency_phone ?? ''),
                    'updated_at' => Carbon::now(),
                ];
                if ($familyRow) {
                    DB::table('families')->where('user_id', $userId)->update($familyData);
                } else {
                    $familyData['user_id'] = $userId;
                    $familyData['feature_path'] = null;
                    $familyData['created_at'] = Carbon::now();
                    DB::table('families')->insert($familyData);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.users.show', $userId)
                ->with('success', 'User updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update user.');
        }
    }

    public function usersUpdateStatus(Request $request, $userId)
    {
        $status = $request->input('status');
        if (!in_array($status, ['active', 'disabled'], true)) {
            return back()->with('error', 'Invalid status.');
        }

        DB::table('users')->where('user_id', $userId)->update([
            'status' => $status,
            'updated_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Account status updated.');
    }

    public function usersForcePasswordReset($userId)
    {
        // Mark account as requiring password reset on next login via status flag
        DB::table('users')->where('user_id', $userId)->update([
            'status' => 'reset_required',
            'updated_at' => Carbon::now(),
        ]);

        return back()->with('success', 'User will be required to reset password on next login.');
    }

    public function usersResetPassword($userId)
    {
        $user = DB::table('users')->where('user_id', $userId)->first();
        if (!$user) {
            abort(404);
        }

        $tempPassword = Str::random(10);

        DB::table('users')->where('user_id', $userId)->update([
            'password' => Hash::make($tempPassword),
            'updated_at' => Carbon::now(),
        ]);

        // In a real system this would be emailed; here we just flash.
        return back()->with('success', 'Password has been reset. Provide the new temporary password to the user: ' . $tempPassword);
    }

    public function usersResendNotification($userId)
    {
        // Stub: In a real system, this would resend an email invite or notification.
        $exists = DB::table('users')->where('user_id', $userId)->exists();
        if (!$exists) {
            abort(404);
        }

        return back()->with('success', 'Account access notification has been resent.');
    }

    public function usersDestroy($userId)
    {
        $user = DB::table('users')->where('user_id', $userId)->first();
        if (!$user) {
            abort(404);
        }

        $linkedStudentsAsFamily = DB::table('students')->where('family_id', $userId)->count();
        $linkedTeacherRelations = DB::table('student_teacher')->where('teacher_id', $userId)->count();
        $linkedTests = DB::table('tests')->where('examiner_id', $userId)->count();

        if ($linkedStudentsAsFamily > 0 || $linkedTeacherRelations > 0 || $linkedTests > 0) {
            return back()->with('error', 'User has linked students or tests. Prefer disabling or archiving the account instead of deleting.');
        }

        DB::table('users')->where('user_id', $userId)->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    public function studentsCreate()
    {
        $families = DB::table('families as f')
            ->join('users as u', 'f.user_id', '=', 'u.user_id')
            ->orderBy('f.family_name')
            ->get([
                'f.user_id',
                'f.family_name',
                'u.email',
            ]);

        return view('admin.students_form', [
            'mode' => 'create',
            'student' => null,
            'families' => $families,
            'sections' => DB::table('sections')->orderBy('name')->get(),
        ]);
    }

    public function studentsStore(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'family_id' => 'required|exists:families,user_id',
            'section_id' => 'required|exists:sections,section_id',
            'feature_path' => 'nullable|string|max:255',
        ]);

        $studentId = DB::table('students')->insertGetId([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'date_of_birth' => $validated['date_of_birth'],
            'family_id' => $validated['family_id'],
            'section_id' => $validated['section_id'],
            'feature_path' => $validated['feature_path'] ?? null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Auto-create assessment periods (mirror Student::created logic)
        try {
            $start = Carbon::now()->startOfDay();
            for ($i = 1; $i <= 3; $i++) {
                $pStart = (clone $start)->addMonths(6 * ($i - 1));
                $pEnd = (clone $pStart)->addMonths(6);

                DB::table('assessment_periods')->insert([
                    'description' => "Assessment Period $i",
                    'student_id' => $studentId,
                    'start_date' => $pStart->toDateString(),
                    'end_date' => $pEnd->toDateString(),
                    'status' => 'scheduled',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        } catch (\Throwable $e) {
            // ignore and log
            Log::warning('Failed to auto-generate assessment periods for student ' . $studentId . ': ' . $e->getMessage());
        }

        return redirect()->route('admin.students.show', $studentId)->with('success', 'Student created successfully.');
    }

    public function studentsShow($studentId)
    {
        $student = DB::table('students as s')
            ->leftJoin('families as f', 's.family_id', '=', 'f.user_id')
            ->leftJoin('users as fu', 'f.user_id', '=', 'fu.user_id')
            ->leftJoin('sections as sec', 's.section_id', '=', 'sec.section_id')
            ->where('s.student_id', $studentId)
            ->select(
                's.*',
                'f.family_name',
                'f.home_address as family_home_address',
                'f.emergency_contact',
                'f.emergency_phone',
                'fu.email as family_email',
                'sec.name as section_name'
            )
            ->first();

        if (!$student) {
            abort(404);
        }

        $now = Carbon::now();
        $ageYears = null;
        if ($student->date_of_birth) {
            try {
                $dob = Carbon::parse($student->date_of_birth);
                $ageYears = $dob->diffInYears($now);
            } catch (\Throwable $e) {
                $ageYears = null;
            }
        }

        $teachers = DB::table('student_teacher as st')
            ->join('teachers as t', 'st.teacher_id', '=', 't.user_id')
            ->join('users as u', 't.user_id', '=', 'u.user_id')
            ->where('st.student_id', $studentId)
            ->orderBy('t.last_name')
            ->orderBy('t.first_name')
            ->get([
                't.user_id',
                't.first_name',
                't.last_name',
                'u.username',
            ]);

        $allTeacherOptions = DB::table('teachers as t')
            ->join('users as u', 't.user_id', '=', 'u.user_id')
            ->orderBy('t.last_name')
            ->orderBy('t.first_name')
            ->get([
                't.user_id',
                't.first_name',
                't.last_name',
                'u.username',
            ]);

        $periods = DB::table('assessment_periods as p')
            ->leftJoin('period_summary_scores as ps', 'ps.period_id', '=', 'p.period_id')
            ->where('p.student_id', $studentId)
            ->orderBy('p.start_date')
            ->get([
                'p.period_id',
                'p.description',
                'p.start_date',
                'p.end_date',
                'p.status',
                'ps.teachers_standard_score_avg',
                'ps.family_standard_score',
                'ps.final_standard_score',
                'ps.final_interpretation',
                'ps.teacher_discrepancy',
                'ps.teacher_family_discrepancy',
            ]);

        $tests = DB::table('tests as t')
            ->leftJoin('users as u', 't.examiner_id', '=', 'u.user_id')
            ->where('t.student_id', $studentId)
            ->orderByDesc('t.test_date')
            ->get([
                't.test_id',
                't.period_id',
                't.test_date',
                't.status',
                't.notes',
                'u.username as examiner_username',
                'u.role as examiner_role',
            ]);

        $picturesCountByTest = DB::table('test_picture as tp')
            ->join('documentation_pictures as dp', 'tp.picture_id', '=', 'dp.picture_id')
            ->whereIn('tp.test_id', $tests->pluck('test_id'))
            ->groupBy('tp.test_id')
            ->pluck(DB::raw('COUNT(*)'), 'tp.test_id');

        $latestCompletedTestId = DB::table('tests as t')
            ->where('t.student_id', $studentId)
            ->whereIn('t.status', ['completed', 'finalized'])
            ->orderByDesc('t.test_date')
            ->value('t.test_id');

        $domainScores = collect();
        if ($latestCompletedTestId) {
            $domainScores = DB::table('test_domain_scaled_scores as tds')
                ->join('domains as d', 'tds.domain_id', '=', 'd.domain_id')
                ->where('tds.test_id', $latestCompletedTestId)
                ->orderBy('d.name')
                ->get([
                    'd.name as domain_name',
                    'tds.raw_score',
                    'tds.scaled_score',
                ]);
        }

        $discrepancySummaries = $periods->map(function ($p) {
            return $p;
        });

        return view('admin.students_show', [
            'student' => $student,
            'ageYears' => $ageYears,
            'teachers' => $teachers,
            'allTeacherOptions' => $allTeacherOptions,
            'periods' => $periods,
            'tests' => $tests,
            'picturesCountByTest' => $picturesCountByTest,
            'domainScores' => $domainScores,
            'discrepancySummaries' => $discrepancySummaries,
        ]);
    }

    public function studentsEdit($studentId)
    {
        $student = DB::table('students as s')
            ->leftJoin('families as f', 's.family_id', '=', 'f.user_id')
            ->where('s.student_id', $studentId)
            ->select('s.*', 'f.family_name')
            ->first();

        if (!$student) {
            abort(404);
        }

        $families = DB::table('families as f')
            ->join('users as u', 'f.user_id', '=', 'u.user_id')
            ->orderBy('f.family_name')
            ->get([
                'f.user_id',
                'f.family_name',
                'u.email',
            ]);

        return view('admin.students_form', [
            'mode' => 'edit',
            'student' => $student,
            'families' => $families,
            'sections' => DB::table('sections')->orderBy('name')->get(),
        ]);
    }

    public function studentsUpdate(Request $request, $studentId)
    {
        $existing = DB::table('students')->where('student_id', $studentId)->first();
        if (!$existing) {
            abort(404);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'family_id' => 'required|exists:families,user_id',
            'section_id' => 'required|exists:sections,section_id',
            'feature_path' => 'nullable|string|max:255',
        ]);

        DB::table('students')->where('student_id', $studentId)->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'date_of_birth' => $validated['date_of_birth'],
            'family_id' => $validated['family_id'],
            'section_id' => $validated['section_id'],
            'feature_path' => $validated['feature_path'] ?? $existing->feature_path,
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.students.show', $studentId)->with('success', 'Student updated successfully.');
    }

    public function studentsAssignTeacher(Request $request, $studentId)
    {
        $teacherId = $request->input('teacher_id');
        if (!$teacherId) {
            return back()->with('error', 'Please select a teacher to assign.');
        }

        $exists = DB::table('student_teacher')
            ->where('student_id', $studentId)
            ->where('teacher_id', $teacherId)
            ->exists();

        if (!$exists) {
            DB::table('student_teacher')->insert([
                'student_id' => $studentId,
                'teacher_id' => $teacherId,
            ]);
        }

        return back()->with('success', 'Teacher assignment updated.');
    }

    public function studentsRemoveTeacher($studentId, $teacherId)
    {
        DB::table('student_teacher')
            ->where('student_id', $studentId)
            ->where('teacher_id', $teacherId)
            ->delete();

        return back()->with('success', 'Teacher removed from student.');
    }

    public function studentsTransferFamily(Request $request, $studentId)
    {
        $validated = $request->validate([
            'family_id' => 'required|exists:families,user_id',
        ]);

        DB::table('students')->where('student_id', $studentId)->update([
            'family_id' => $validated['family_id'],
            'updated_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Student family updated.');
    }

    public function studentsBulkAssignTeacher(Request $request)
    {
        $teacherId = $request->input('teacher_id');
        $studentIds = (array) $request->input('student_ids', []);

        if (!$teacherId || empty($studentIds)) {
            return back()->with('error', 'Select a teacher and at least one student.');
        }

        foreach ($studentIds as $sid) {
            $exists = DB::table('student_teacher')
                ->where('student_id', $sid)
                ->where('teacher_id', $teacherId)
                ->exists();
            if (!$exists) {
                DB::table('student_teacher')->insert([
                    'student_id' => $sid,
                    'teacher_id' => $teacherId,
                ]);
            }
        }

        return back()->with('success', 'Teacher assigned to selected students.');
    }

    public function studentsExport()
    {
        $rows = DB::table('students as s')
            ->leftJoin('families as f', 's.family_id', '=', 'f.user_id')
            ->select('s.first_name', 's.last_name', 's.date_of_birth', 'f.family_name')
            ->orderBy('s.last_name')
            ->orderBy('s.first_name')
            ->get();

        $output = "First Name,Last Name,Date of Birth,Family\n";
        foreach ($rows as $r) {
            $output .= '"' . $r->first_name . '","' . $r->last_name . '","' . $r->date_of_birth . '","' . ($r->family_name ?? '') . "\n";
        }

        return response($output, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students.csv"',
        ]);
    }

    public function testsCancel($testId)
    {
        $test = DB::table('tests')->where('test_id', $testId)->first();
        if (!$test) {
            abort(404);
        }

        DB::table('tests')->where('test_id', $testId)->update([
            'status' => 'canceled',
            'updated_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Test has been canceled.');
    }
    
    public function eccd(Request $request)
    {
        // List of available scale versions for selection
        $scaleVersions = DB::table('scale_versions')
            ->select('scale_version_id as id', 'name')
            ->orderBy('name')
            ->get();

        $selectedScaleId = $request->input('scale_version_id');

        $questionsByDomain = collect();
        $domainScaledScores = collect();
        $standardScoreScales = collect();
        $testDomainScores = collect();
        $testStandardScores = collect();

        if ($selectedScaleId) {
            // Questions for the selected scale version, grouped by domain
            $questions = DB::table('questions as q')
                ->join('domains as d', 'q.domain_id', '=', 'd.domain_id')
                ->where('q.scale_version_id', $selectedScaleId)
                ->orderBy('d.name')
                ->orderBy('q.order')
                ->select(
                    'd.name as domain_name',
                    'q.order as question_order',
                    'q.text as question_text',
                    'q.display_text as question_display_text',
                    'q.question_type as question_type'
                )
                ->get();

            $questionsByDomain = $questions->groupBy('domain_name');

            // Domain scaled score lookup rows for this scale version
            $domainScaledScores = DB::table('domain_scaled_scores as ds')
                ->join('domains as d', 'ds.domain_id', '=', 'd.domain_id')
                ->where('ds.scale_version_id', $selectedScaleId)
                ->orderBy('d.name')
                ->orderBy('ds.age_min_months')
                ->select(
                    'd.name as domain_name',
                    'ds.age_min_months as age_min',
                    'ds.age_max_months as age_max',
                    'ds.raw_min as raw_min',
                    'ds.raw_max as raw_max',
                    'ds.scaled_score as scaled_score'
                )
                ->get();

            // Standard score scale rows for this scale version
            $standardScoreScales = DB::table('standard_score_scales as ss')
                ->where('ss.scale_version_id', $selectedScaleId)
                ->orderBy('ss.sum_scaled_min')
                ->select(
                    'ss.sum_scaled_min as sum_scaled_min',
                    'ss.sum_scaled_max as sum_scaled_max',
                    'ss.standard_score as standard_score'
                )
                ->get();

            // Per-test domain scaled scores for this scale version
            $testDomainScores = DB::table('test_domain_scaled_scores as tds')
                ->join('tests as t', 'tds.test_id', '=', 't.test_id')
                ->join('domains as d', 'tds.domain_id', '=', 'd.domain_id')
                ->join('students as s', 't.student_id', '=', 's.student_id')
                ->where('tds.scale_version_id', $selectedScaleId)
                ->orderBy('t.test_id')
                ->orderBy('d.name')
                ->select(
                    't.test_id as test_id',
                    DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
                    'd.name as domain_name',
                    'tds.raw_score as raw_score',
                    'tds.scaled_score as scaled_score'
                )
                ->get();

            // Test standard scores for this scale version
            $testStandardScores = DB::table('test_standard_scores as ts')
                ->join('tests as t', 'ts.test_id', '=', 't.test_id')
                ->join('students as s', 't.student_id', '=', 's.student_id')
                ->where('ts.scale_version_id', $selectedScaleId)
                ->orderBy('t.test_id')
                ->select(
                    't.test_id as test_id',
                    DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
                    'ts.sum_scaled_scores as sum_scaled_scores',
                    'ts.standard_score as standard_score',
                    'ts.interpretation as interpretation'
                )
                ->get();
        }

        return view('admin.eccd', [
            'scaleVersions' => $scaleVersions,
            'selectedScaleId' => $selectedScaleId,
            'questionsByDomain' => $questionsByDomain,
            'domainScaledScores' => $domainScaledScores,
            'standardScoreScales' => $standardScoreScales,
            'testDomainScores' => $testDomainScores,
            'testStandardScores' => $testStandardScores,
        ]);
    }


}
