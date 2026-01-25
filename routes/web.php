<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FamilyController;

Route::get('/', [WelcomeController::class, 'index'])->name('index');
Route::get('/login', [UserController::class,'showLoginForm'])->name('login');
Route::post('/login',[UserController::class, 'login']);
Route::post('/logout',[UserController::class, 'logout'])->name('logout');

Route::get('/register',[UserController::class, 'showRegisterForm'])->name('register');
Route::post('/register',[UserController::class, 'store']);

// Admin routes
Route::prefix('admin')->group(function () {
	Route::get('/', [AdminController::class, 'index'])->name('admin.index');
	Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
	Route::post('/users', [AdminController::class, 'usersStore'])->name('admin.users.store');

	Route::get('/families', [AdminController::class, 'families'])->name('admin.families');
	Route::post('/families', [AdminController::class, 'familiesStore'])->name('admin.families.store');
	Route::post('/families/{family}/assign-student', [AdminController::class, 'familyAssignStudent'])->name('admin.families.assign');

	Route::get('/teachers', [AdminController::class, 'teachers'])->name('admin.teachers');
	Route::post('/teachers', [AdminController::class, 'teachersStore'])->name('admin.teachers.store');
	Route::post('/teachers/{teacher}/assign-student', [AdminController::class, 'teacherAssignStudent'])->name('admin.teachers.assign');

	Route::get('/sections', [AdminController::class, 'sections'])->name('admin.sections');
	Route::post('/sections', [AdminController::class, 'sectionsStore'])->name('admin.sections.store');
	Route::get('/sections/{section}/students', [AdminController::class, 'sectionStudents'])->name('admin.sections.students');
	Route::post('/sections/{section}/students', [AdminController::class, 'studentsStore'])->name('admin.sections.students.store');

	Route::get('/students/{student}', function($studentId){
		$student = App\Models\Student::with(['family','teachers.user','section','tags','tests.responses','tests.scores'])->findOrFail($studentId);
		return view('admin.students.show', compact('student'));
	})->name('admin.students.show');

	Route::get('/students/{student}/record', [\App\Http\Controllers\ChecklistController::class, 'record'])
		->name('admin.students.record');

	Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');

	Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
	Route::post('/profile', [AdminController::class, 'profileUpdate'])->name('admin.profile.update');

	Route::get('/help', [AdminController::class, 'help'])->name('admin.help');

	Route::get('/domains', [AdminController::class, 'domains'])->name('admin.domains');
	});

Route::prefix('family')->group(function () {
	Route::get('/', [FamilyController::class, 'index'])->name('family.index');
	Route::get('/children/{student}', [FamilyController::class, 'child'])->name('family.child');
	Route::post('/tests/start/{student}', [FamilyController::class, 'startTest'])->name('family.tests.start');
	Route::get('/tests/{test}/domain/{domain}/question/{index}', [FamilyController::class, 'showQuestion'])->name('family.tests.question');
	Route::post('/tests/{test}/domain/{domain}/question/{index}', [FamilyController::class, 'submitQuestion'])->name('family.tests.question.submit');
	Route::get('/tests/{test}/result', [FamilyController::class, 'result'])->name('family.tests.result');
});

// Teacher routes (simple, no CSS UI)
Route::prefix('teacher')->group(function () {
	Route::get('/', [\App\Http\Controllers\TeacherController::class, 'index'])->name('teacher.index');
	Route::get('/students/{student}', [\App\Http\Controllers\TeacherController::class, 'student'])->name('teacher.student');
	Route::post('/tests/start/{student}', [\App\Http\Controllers\TeacherController::class, 'startTest'])->name('teacher.tests.start');
	Route::get('/tests/{test}/domain/{domain}/question/{index}', [\App\Http\Controllers\TeacherController::class, 'showQuestion'])->name('teacher.tests.question');
	Route::post('/tests/{test}/domain/{domain}/question/{index}', [\App\Http\Controllers\TeacherController::class, 'submitQuestion'])->name('teacher.tests.question.submit');
	Route::get('/tests/{test}/result', [\App\Http\Controllers\TeacherController::class, 'result'])->name('teacher.tests.result');
});
