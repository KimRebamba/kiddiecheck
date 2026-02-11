<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
        $stats = [
            'userCount' => User::count(),
            'familyCount' => Family::count(),
            'teacherCount' => Teacher::count(),
            'studentCount' => Student::count(),
            'domainCount' => Domain::count(),
            'questionCount' => Question::count(),
            'testCount' => Test::count(),
        ];

        // Test status metrics
        $completedCount = Test::finalized()->count();
        $inProgressCount = Test::where('status','in_progress')->count();
        $pendingCount = Test::where('status','pending')->count();
        $cancelledCount = Test::where('status','cancelled')->count();
        $terminatedCount = Test::where('status','terminated')->count();

        // Recent lists for tables (still used below)
        $inProgressTests = Test::with(['student.family','observer'])
            ->where('status','in_progress')->orderByDesc('test_date')->limit(10)->get();
        $pendingTests = Test::with(['student.family'])
            ->where('status','pending')->orderBy('test_date')->limit(10)->get();
        $recentCompleted = Test::with(['student.family','observer'])
            ->finalized()->orderByDesc('test_date')->limit(10)->get();

        // Eligibility metrics: students currently eligible for teacher/family tests
        $now = now();
        $students = Student::with(['teachers','assessmentPeriods','tests.observer'])->get();
        $eligibleTeacherCount = 0;
        $eligibleFamilyCount = 0;
        foreach ($students as $s) {
            $hasWindow = (bool) $s->assessmentPeriods->first(function($p) use ($now){ return $now->between($p->starts_at, $p->ends_at); });
            if (!$hasWindow) { continue; }

            // Teacher eligibility: no in-progress teacher test and 6-month rule since last non-cancelled/terminated teacher test by any assigned teacher
            $hasTeacherInProgress = (bool) $s->tests->first(function($t){ return $t->status === 'in_progress' && optional($t->observer)->role === 'teacher'; });
            if (!$hasTeacherInProgress && $s->teachers->isNotEmpty()) {
                $teacherEligible = false;
                foreach ($s->teachers as $tchr) {
                    if (($tchr->pivot?->status ?? 'active') !== 'active') { continue; }
                    $lastActive = $s->tests->filter(function($t) use ($tchr){
                        return optional($t->observer)->role === 'teacher' && $t->observer_id === $tchr->id && !in_array($t->status, ['cancelled','terminated']);
                    })->sortByDesc('test_date')->first();
                    $months = $lastActive ? \Illuminate\Support\Carbon::parse($lastActive->test_date)->diffInMonths($now) : null;
                    if ($months === null || $months >= 6) { $teacherEligible = true; break; }
                }
                if ($teacherEligible) { $eligibleTeacherCount++; }
            }

            // Family eligibility: student has a family, no in-progress family test, 6-month rule since last non-cancelled/terminated family test
            if ($s->family) {
                $hasFamilyInProgress = (bool) $s->tests->first(function($t){ return $t->status === 'in_progress' && optional($t->observer)->role === 'family'; });
                if (!$hasFamilyInProgress) {
                    $lastFamilyActive = $s->tests->filter(function($t){
                        return optional($t->observer)->role === 'family' && !in_array($t->status, ['cancelled','terminated']);
                    })->sortByDesc('test_date')->first();
                    $monthsFam = $lastFamilyActive ? \Illuminate\Support\Carbon::parse($lastFamilyActive->test_date)->diffInMonths($now) : null;
                    if ($monthsFam === null || $monthsFam >= 6) { $eligibleFamilyCount++; }
                }
            }
        }

        // Summaries
        $unassignedStudents = Student::with(['family','section'])
            ->whereDoesntHave('teachers')->orderBy('id','desc')->limit(10)->get();
        $familiesSummary = Family::withCount('students')
            ->orderByDesc('students_count')->limit(10)->get();
        $teachersSummary = Teacher::with(['user'])->withCount('students')
            ->orderByDesc('students_count')->limit(10)->get();
        $sectionsSummary = \App\Models\Section::withCount('students')
            ->orderByDesc('students_count')->limit(10)->get();

        $today = $now->format('F j, Y');
        $greeting = 'Hello, '.(Auth::user()?->name ?? 'Admin');

        // Dashboard tables
        // Exclude drafts/pending from recent activities to avoid showing newly enrolled students with no active tests
        $testsTable = Test::with(['student','assessmentPeriod','observer','responses'])
            ->whereNotIn('status', ['draft','pending'])
            ->orderByDesc('test_date')
            ->paginate(5, ['*'], 'tests_page')
            ->through(function(Test $t) {
                $order = $t->question_order ? json_decode($t->question_order, true) : null;
                $qTotal = 0;
                if (is_array($order)) {
                    foreach ($order as $domainId => $qIds) { $qTotal += is_array($qIds) ? count($qIds) : 0; }
                }
                if ($qTotal <= 0) {
                    $qTotal = (int) \App\Models\Domain::withCount('questions')->get()->sum('questions_count');
                }
                $answered = $t->responses->filter(fn($r)=> $r->score !== null)->count();
                $progress = $qTotal > 0 ? round(($answered / max(1, $qTotal)) * 100) : 0;
                $examinerRole = null;
                if ($t->examiner_id) {
                    $examinerRole = optional(\App\Models\User::find($t->examiner_id))->role;
                }
                if (!$examinerRole) { $examinerRole = optional($t->observer)->role; }
                $dateFmt = $t->test_date ? \Illuminate\Support\Carbon::parse($t->test_date)->format('F j, Y') : null;
                $periodStr = null;
                if ($t->assessmentPeriod) {
                    $start = $t->assessmentPeriod->starts_at ? \Illuminate\Support\Carbon::parse($t->assessmentPeriod->starts_at)->format('F j, Y') : null;
                    $end = $t->assessmentPeriod->ends_at ? \Illuminate\Support\Carbon::parse($t->assessmentPeriod->ends_at)->format('F j, Y') : null;
                    $periodStr = ($start && $end) ? ($start.' → '.$end) : null;
                }
                return [
                    'id' => $t->id,
                    'student' => $t->student?->name,
                    'date' => $t->test_date,
                    'date_fmt' => $dateFmt,
                    'period_str' => $periodStr,
                    'examiner_role' => $examinerRole,
                    'progress' => $progress,
                    'status' => $t->status,
                ];
            })->withQueryString();

        $studentsTable = Student::with(['section','tests','assessmentPeriods'])
            ->orderByDesc('id')
            ->paginate(8, ['*'], 'students_page')
            ->through(function(Student $s){
                $lastTest = $s->tests->sortByDesc('test_date')->first();
                $lastFmt = $lastTest?->test_date ? \Illuminate\Support\Carbon::parse($lastTest->test_date)->format('F j, Y') : null;
                $statuses = ['in_progress','pending','paused'];
                $testsLeft = $s->assessmentPeriods->filter(function($p) use ($s, $statuses){
                    return $s->tests->first(function($t) use ($p, $statuses){
                        return $t->assessment_period_id === $p->id && in_array($t->status, $statuses);
                    });
                })->count();
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'section' => optional($s->section)->name,
                    'last_test' => $lastTest?->test_date,
                    'last_test_fmt' => $lastFmt,
                    'tests_left' => $testsLeft,
                ];
            })->withQueryString();

        $teachersTable = Teacher::with(['user','students'])
            ->orderByDesc('id')
            ->paginate(5, ['*'], 'teachers_page')
            ->through(function(Teacher $t){
                $assigned = $t->students->count();
                $testsDone = Test::whereHas('observer', function($q) use ($t){ $q->where('role','teacher')->where('id',$t->id); })
                    ->finalized()->count();
                return [
                    'id' => $t->id,
                    'teacher_name' => $t->user?->name ?? ('Teacher #'.$t->id),
                    'user_name' => $t->user?->name,
                    'assigned_students' => $assigned,
                    'tests_done' => $testsDone,
                ];
            })->withQueryString();

        $familiesTable = Family::withCount('students')
            ->orderByDesc('id')
            ->paginate(5, ['*'], 'families_page')
            ->through(function(Family $f){
                $last = Test::whereHas('student', function($q) use ($f){ $q->where('family_id', $f->id); })
                    ->whereHas('observer', function($q){ $q->where('role','family'); })
                    ->finalized()->orderByDesc('test_date')->value('test_date');
                $lastFmt = $last ? \Illuminate\Support\Carbon::parse($last)->format('F j, Y') : null;
                return [
                    'id' => $f->id,
                    'name' => $f->name,
                    'children' => $f->students_count,
                    'last_test' => $last,
                    'last_test_fmt' => $lastFmt,
                ];
            })->withQueryString();

        $usersTable = User::orderByDesc('id')->paginate(7, ['*'], 'users_page')->withQueryString();

        return view('admin.index', $stats + compact(
            'completedCount','inProgressCount','pendingCount','cancelledCount','terminatedCount',
            'eligibleTeacherCount','eligibleFamilyCount','today','greeting',
            'inProgressTests','pendingTests','recentCompleted',
            'unassignedStudents','familiesSummary','teachersSummary','sectionsSummary',
            'testsTable','studentsTable','teachersTable','familiesTable','usersTable'
        ));
    }

    // Users
    public function users(Request $request)
    {
        $query = User::query();
        if ($request->filled('name')) { $query->where('name','like','%'.$request->string('name').'%'); }
        if ($request->filled('email')) { $query->where('email','like','%'.$request->string('email').'%'); }
        if ($request->filled('role')) { $query->where('role', $request->string('role')); }
        if ($request->filled('status')) { $query->where('status', $request->string('status')); }
        $users = $query->orderByDesc('id')->paginate(25)->withQueryString();
        return view('admin.users.index', [
            'users' => $users,
            'filters' => [
                'name' => $request->string('name'),
                'email' => $request->string('email'),
                'role' => $request->string('role'),
                'status' => $request->string('status'),
            ],
        ]);
    }

    public function usersExport(Request $request)
    {
        $query = User::query();
        if ($request->filled('name')) { $query->where('name','like','%'.$request->string('name').'%'); }
        if ($request->filled('email')) { $query->where('email','like','%'.$request->string('email').'%'); }
        if ($request->filled('role')) { $query->where('role', $request->string('role')); }
        if ($request->filled('status')) { $query->where('status', $request->string('status')); }
        $rows = $query->orderByDesc('id')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_export.csv"'
        ];
        $callback = function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','Name','Email','Role','Status','Profile Path']);
            foreach ($rows as $u) { fputcsv($out, [$u->id, $u->name, $u->email, $u->role, $u->status, $u->profile_path]); }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function usersStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:family,teacher,admin',
            'status' => 'required|in:active,inactive',
            'profile_path' => 'nullable|string|max:255',
        ]);
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
            'profile_path' => $validated['profile_path'] ?? null,
        ]);
        return redirect()->route('admin.users');
    }

    public function usersUpdate($userId, Request $request)
    {
        $user = User::findOrFail($userId);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:family,teacher,admin',
            'status' => 'required|in:active,inactive',
            'profile_path' => 'nullable|string|max:255',
        ]);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }
        $user->role = $validated['role'];
        $user->status = $validated['status'];
        $user->profile_path = $validated['profile_path'] ?? null;
        $user->save();
        return redirect()->route('admin.users');
    }

    public function usersDestroy($userId)
    {
        $user = User::findOrFail($userId);
        // Prevent deleting if linked teacher/family exists to avoid orphaned records
        if ($user->role === 'teacher' && Teacher::where('id', $user->id)->exists()) {
            return redirect()->route('admin.users')->withErrors(['Cannot delete: user has teacher profile.']);
        }
        if ($user->role === 'family' && Family::where('user_id', $user->id)->exists()) {
            return redirect()->route('admin.users')->withErrors(['Cannot delete: user has family profile.']);
        }
        $user->delete();
        return redirect()->route('admin.users');
    }

    // Families
    public function families(Request $request)
    {
        $query = Family::with(['user'])->withCount('students');
        if ($request->filled('name')) {
            $query->where('name','like','%'.$request->string('name').'%');
        }
        if ($request->filled('user')) {
            $needle = '%'.$request->string('user').'%';
            $query->whereHas('user', function($q) use ($needle){ $q->where('name','like',$needle)->orWhere('email','like',$needle); });
        }
        if ($request->filled('has')) {
            $has = $request->string('has');
            if ($has === 'students') { $query->has('students'); }
            if ($has === 'none') { $query->doesntHave('students'); }
        }
        $families = $query->orderByDesc('id')->paginate(25)->withQueryString();

        return view('admin.families.index', [
            'families' => $families,
            'familyUsers' => User::where('role', 'family')->orderBy('name')->get(),
            'allStudents' => \App\Models\Student::orderBy('name')->get(),
            'filters' => [
                'name' => $request->string('name'),
                'user' => $request->string('user'),
                'has' => $request->string('has'),
            ],
        ]);
    }

    public function familiesExport(Request $request)
    {
        $query = Family::with(['user'])->withCount('students');
        if ($request->filled('name')) { $query->where('name','like','%'.$request->string('name').'%'); }
        if ($request->filled('user')) {
            $needle = '%'.$request->string('user').'%';
            $query->whereHas('user', function($q) use ($needle){ $q->where('name','like',$needle)->orWhere('email','like',$needle); });
        }
        if ($request->filled('has')) {
            $has = $request->string('has');
            if ($has === 'students') { $query->has('students'); }
            if ($has === 'none') { $query->doesntHave('students'); }
        }
        $rows = $query->orderByDesc('id')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="families_export.csv"'
        ];
        $callback = function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','Family','User','Email','Home','Students']);
            foreach ($rows as $f) {
                fputcsv($out, [
                    $f->id,
                    $f->name,
                    optional($f->user)->name,
                    optional($f->user)->email,
                    $f->home_address,
                    $f->students_count,
                ]);
            }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function familiesStore(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'home_address' => 'required|string|max:255',
        ]);
        Family::create($validated);
        return redirect()->route('admin.families');
    }

    public function familiesUpdate($familyId, Request $request)
    {
        $family = Family::findOrFail($familyId);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'home_address' => 'required|string|max:255',
        ]);
        $family->update($validated);
        return redirect()->route('admin.families');
    }

    public function familiesDestroy($familyId)
    {
        $family = Family::withCount('students')->findOrFail($familyId);
        if ($family->students_count > 0) {
            return redirect()->route('admin.families')->withErrors(['Cannot delete: family has students.']);
        }
        $family->delete();
        return redirect()->route('admin.families');
    }

    public function familyAssignStudent($familyId, Request $request)
    {
        $family = Family::findOrFail($familyId);
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);
        $student = \App\Models\Student::findOrFail($validated['student_id']);
        $student->family_id = $family->id;
        $student->save();
        return redirect()->route('admin.families');
    }

    // Teachers
    public function teachers(Request $request)
    {
        $query = Teacher::with(['user'])->withCount('students');
        if ($request->filled('name')) { $query->whereHas('user', function($q) use ($request){ $q->where('name','like','%'.$request->string('name').'%'); }); }
        if ($request->filled('email')) { $query->whereHas('user', function($q) use ($request){ $q->where('email','like','%'.$request->string('email').'%'); }); }
        if ($request->filled('status')) { $query->where('status', $request->string('status')); }
        if ($request->filled('min')) { $query->having('students_count', '>=', (int)$request->string('min')); }
        if ($request->filled('max')) { $query->having('students_count', '<=', (int)$request->string('max')); }
        $teachers = $query->orderByDesc('id')->paginate(25)->withQueryString();
        return view('admin.teachers.index', [
            'teachers' => $teachers,
            'teacherUsers' => User::where('role', 'teacher')->orderBy('name')->get(),
            'allStudents' => \App\Models\Student::orderBy('name')->get(),
            'filters' => [
                'name' => $request->string('name'),
                'email' => $request->string('email'),
                'status' => $request->string('status'),
                'min' => $request->string('min'),
                'max' => $request->string('max'),
            ],
        ]);
    }

    public function teachersExport(Request $request)
    {
        $query = Teacher::with(['user'])->withCount('students');
        if ($request->filled('name')) { $query->whereHas('user', function($q) use ($request){ $q->where('name','like','%'.$request->string('name').'%'); }); }
        if ($request->filled('email')) { $query->whereHas('user', function($q) use ($request){ $q->where('email','like','%'.$request->string('email').'%'); }); }
        if ($request->filled('status')) { $query->where('status', $request->string('status')); }
        if ($request->filled('min')) { $query->having('students_count', '>=', (int)$request->string('min')); }
        if ($request->filled('max')) { $query->having('students_count', '<=', (int)$request->string('max')); }
        $rows = $query->orderByDesc('id')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="teachers_export.csv"'
        ];
        $callback = function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','Name','Email','Hire Date','Status','Students']);
            foreach ($rows as $t) { fputcsv($out, [$t->id, optional($t->user)->name, optional($t->user)->email, $t->hire_date, $t->status, $t->students_count]); }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function teachersStore(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'hire_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);
        // teachers table uses users.id as PK
        DB::table('teachers')->updateOrInsert(['id' => $validated['user_id']], [
            'hire_date' => $validated['hire_date'] ?? null,
            'status' => $validated['status'],
        ]);
        return redirect()->route('admin.teachers');
    }

    public function teachersUpdate($teacherId, Request $request)
    {
        $teacher = Teacher::findOrFail($teacherId);
        $validated = $request->validate([
            'hire_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);
        $teacher->hire_date = $validated['hire_date'] ?? null;
        $teacher->status = $validated['status'];
        $teacher->save();
        return redirect()->route('admin.teachers');
    }

    public function teachersDestroy($teacherId)
    {
        $teacher = Teacher::with('students')->findOrFail($teacherId);
        $teacher->students()->detach();
        DB::table('teachers')->where('id', $teacher->id)->delete();
        return redirect()->route('admin.teachers');
    }

    public function teacherAssignStudent($teacherId, Request $request)
    {
        $teacher = Teacher::with('students')->findOrFail($teacherId);
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'role' => 'nullable|in:homeroom,specialist,others',
        ]);
        $role = $validated['role'] ?? 'homeroom';
        $teacher->students()->syncWithoutDetaching([
            $validated['student_id'] => [
                'role' => $role,
                'assigned_at' => now(),
                'status' => 'active',
            ]
        ]);
        return redirect()->route('admin.teachers');
    }

    // Sections (class sections & students)
    public function sections(Request $request)
    {
        $query = \App\Models\Section::withCount('students');
        if ($request->filled('name')) { $query->where('name','like','%'.$request->string('name').'%'); }
        if ($request->filled('min')) { $query->having('students_count', '>=', (int)$request->string('min')); }
        if ($request->filled('max')) { $query->having('students_count', '<=', (int)$request->string('max')); }
        $sections = $query->orderByDesc('id')->paginate(25)->withQueryString();
        return view('admin.sections.index', [
            'sections' => $sections,
            'filters' => [
                'name' => $request->string('name'),
                'min' => $request->string('min'),
                'max' => $request->string('max'),
            ],
        ]);
    }

    public function sectionsExport(Request $request)
    {
        $query = \App\Models\Section::withCount('students');
        if ($request->filled('name')) { $query->where('name','like','%'.$request->string('name').'%'); }
        if ($request->filled('min')) { $query->having('students_count', '>=', (int)$request->string('min')); }
        if ($request->filled('max')) { $query->having('students_count', '<=', (int)$request->string('max')); }
        $rows = $query->orderByDesc('id')->get();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sections_export.csv"'
        ];
        $callback = function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','Section','Description','Students']);
            foreach ($rows as $s) { fputcsv($out, [$s->id, $s->name, $s->description, $s->students_count]); }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function sectionsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        \App\Models\Section::create($validated);
        return redirect()->route('admin.sections');
    }

    public function sectionsUpdate($sectionId, Request $request)
    {
        $section = \App\Models\Section::findOrFail($sectionId);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $section->update($validated);
        return redirect()->route('admin.sections');
    }

    public function sectionsDestroy($sectionId)
    {
        $section = \App\Models\Section::withCount('students')->findOrFail($sectionId);
        if ($section->students_count > 0) {
            return redirect()->route('admin.sections')->withErrors(['Cannot delete: section has students.']);
        }
        $section->delete();
        return redirect()->route('admin.sections');
    }

    public function sectionStudents($sectionId)
    {
        $section = \App\Models\Section::with('students.family','students.teachers.user','students.tags')->findOrFail($sectionId);
        $families = Family::orderBy('name')->get();
        return view('admin.sections.students', compact('section','families'));
    }

    public function studentsStore($sectionId, Request $request)
    {
        $section = \App\Models\Section::findOrFail($sectionId);
        $validated = $request->validate([
            'family_id' => 'required|exists:families,id',
            'name' => 'required|string|max:255',
            'dob' => 'required|date',
            'emergency_contact' => 'nullable|string|max:50',
            'gender' => 'required|in:male,female,other',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,transferred,graduated',
            'profile_path' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $student = \App\Models\Student::create($validated + ['section_id' => $section->id]);

        // Auto-schedule three tests with windows:
        // Window 1: months 1–6 (start at +1 month)
        // Window 2: months 7–13 (start at +7 months)
        // Window 3: months 14–20 (start at +14 months)
        try {
            $enroll = \Illuminate\Support\Carbon::parse($validated['enrollment_date']);
            $dates = [
                $enroll->copy()->addMonths(1),
                $enroll->copy()->addMonths(7),
                $enroll->copy()->addMonths(14),
            ];
            foreach ($dates as $dt) {
                \App\Models\Test::firstOrCreate([
                    'student_id' => $student->id,
                    'test_date' => $dt->toDateString(),
                ], [
                    'observer_id' => null,
                    'status' => 'pending',
                    'started_at' => null,
                    'submitted_by' => null,
                    'submitted_at' => null,
                ]);
            }
        } catch (\Throwable $e) {
            // swallow scheduling errors; admin can create manually if needed
        }

        return redirect()->route('admin.sections.students', $section->id);
    }

    public function studentsDestroy($studentId)
    {
        $student = \App\Models\Student::findOrFail($studentId);
        // Safety: do not delete if has tests to preserve records
        if (Test::where('student_id', $student->id)->exists()) {
            return redirect()->route('admin.students.show', $student->id)->withErrors(['Cannot delete: student has tests.']);
        }
        $student->delete();
        return redirect()->route('admin.sections');
    }

    // Reports (Tests & Scores)
    public function reports(Request $request)
    {
        $query = Test::with(['student.family', 'observer', 'responses.question', 'scores.domain']);
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('role')) {
            $role = $request->string('role');
            $query->whereHas('observer', function($q) use ($role){ $q->where('role', $role); });
        }
        if ($request->filled('student')) {
            $needle = '%' . $request->string('student') . '%';
            $query->whereHas('student', function($q) use ($needle){ $q->where('name','like',$needle); });
        }
        if ($request->filled('from')) {
            $query->whereDate('test_date', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('test_date', '<=', $request->date('to'));
        }
        $tests = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();
        $filters = [
            'status' => $request->string('status'),
            'role' => $request->string('role'),
            'student' => $request->string('student'),
            'from' => $request->string('from'),
            'to' => $request->string('to'),
        ];
        return view('admin.reports.index', compact('tests','filters'));
    }

    public function reportsExport(Request $request)
    {
        $query = Test::with(['student.family', 'observer']);
        if ($request->filled('status')) { $query->where('status', $request->string('status')); }
        if ($request->filled('role')) { $role = $request->string('role'); $query->whereHas('observer', function($q) use ($role){ $q->where('role',$role); }); }
        if ($request->filled('student')) { $needle = '%' . $request->string('student') . '%'; $query->whereHas('student', function($q) use ($needle){ $q->where('name','like',$needle); }); }
        if ($request->filled('from')) { $query->whereDate('test_date', '>=', $request->date('from')); }
        if ($request->filled('to')) { $query->whereDate('test_date', '<=', $request->date('to')); }
        $rows = $query->orderBy('id','desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tests_export.csv"'
        ];
        $callback = function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','Student','Family','Observer','Role','Date','Status']);
            foreach ($rows as $t) {
                fputcsv($out, [
                    $t->id,
                    optional($t->student)->name,
                    optional(optional($t->student)->family)->name,
                    optional($t->observer)->name,
                    optional($t->observer)->role,
                    $t->test_date,
                    $t->status,
                ]);
            }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    // Profile (Admin user)
    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile.settings', compact('user'));
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profile_path' => 'nullable|string|max:255',
        ]);
        $user->update($validated);
        return redirect()->route('admin.profile');
    }

    // Help
    public function help()
    {
        return view('admin.help');
    }

    // Domains & Questions
    public function domains()
    {
        $domains = Domain::with(['questions' => function($q) {
            $q->orderBy('id');
        }])->orderBy('name')->get();

        return view('admin.domains', compact('domains'));
    }

    // Test result (read-only) for Admin
    public function testResult($testId)
    {
        $test = Test::with(['student','observer','responses.question.domain','scores.domain','student.assessmentPeriods'])->findOrFail($testId);
        if (!in_array($test->status, ['finalized','completed'])) {
            return redirect()->route('admin.reports')->withErrors(['Result is available only for completed tests.']);
        }
        $domains = Domain::with('questions')->orderBy('id')->get();
        // Ensure domain_scores exist; if missing, derive without mutating state
        $sumScaled = $test->scores->sum('scaled_score');
        if ($sumScaled <= 0) {
            // Derive transient scores from responses
            $sumScaled = 0.0;
            foreach ($domains as $d) {
                $qIds = $d->questions->pluck('id')->all();
                $responses = $test->responses->whereIn('question_id', $qIds);
                $app = $responses->filter(fn($r) => $r->score !== null);
                $yes = $app->filter(fn($r) => (float)$r->score === 1.0)->count();
                $tot = max(1, $app->count());
                $pct = ($yes / $tot) * 100.0;
                $sumScaled += (float) EccdScoring::percentageToScaled($pct);
            }
        }
        $standardScore = EccdScoring::deriveStandardScore((float)$sumScaled, $domains->count());

        // Window aggregates within same period
        $windowTests = $test->student->tests()->with(['scores','observer'])
            ->where('assessment_period_id', $test->assessment_period_id)
            ->finalized()->get();
        $aggregates = EccdScoring::aggregateByRole($windowTests, $domains);
        $discrepancies = EccdScoring::analyzeDiscrepancies($aggregates['teacher'], $aggregates['family'], $domains);
        $familyOnly = $windowTests->filter(fn($t) => $t->observer?->role === 'family')->isNotEmpty()
            && $windowTests->filter(fn($t) => $t->observer?->role === 'teacher')->isEmpty();
        $allNA = method_exists($test, 'isAllNA') ? $test->isAllNA() : false;

        return view('admin.test_result', compact('test','domains','sumScaled','standardScore','aggregates','discrepancies','familyOnly','allNA'));
    }
}
