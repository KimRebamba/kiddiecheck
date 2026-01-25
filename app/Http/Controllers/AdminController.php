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

        \App\Models\Student::create($validated + ['section_id' => $section->id]);
        return redirect()->route('admin.sections.students', $section->id);
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
