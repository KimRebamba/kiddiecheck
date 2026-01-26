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

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index', [
            'userCount' => User::count(),
            'familyCount' => Family::count(),
            'teacherCount' => Teacher::count(),
            'studentCount' => Student::count(),
            'domainCount' => Domain::count(),
            'questionCount' => Question::count(),
            'testCount' => Test::count(),
        ]);
    }

    // Users
    public function users()
    {
        return view('admin.users.index', [
            'users' => User::orderBy('id', 'desc')->get(),
        ]);
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
    public function families()
    {
        return view('admin.families.index', [
            'families' => Family::with(['user', 'students'])->orderBy('id', 'desc')->get(),
            'familyUsers' => User::where('role', 'family')->orderBy('name')->get(),
            'allStudents' => \App\Models\Student::orderBy('name')->get(),
        ]);
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
    public function teachers()
    {
        return view('admin.teachers.index', [
            'teachers' => Teacher::with(['user', 'students'])->get(),
            'teacherUsers' => User::where('role', 'teacher')->orderBy('name')->get(),
            'allStudents' => \App\Models\Student::orderBy('name')->get(),
        ]);
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
    public function sections()
    {
        return view('admin.sections.index', [
            'sections' => \App\Models\Section::withCount('students')->orderBy('id','desc')->get(),
        ]);
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
    public function reports()
    {
        $tests = Test::with(['student.family', 'observer', 'responses.question', 'scores.domain'])
            ->orderBy('id', 'desc')->get();
        return view('admin.reports.index', compact('tests'));
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
}
