<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FamilyController;
use App\Models\User;

Route::get('/', function(){
	
	$user = Auth::user();
	if (!$user) { return redirect()->route('login'); }
	if ($user->role === 'admin') { return redirect()->route('admin.index'); }
	if ($user->role === 'teacher') { return redirect()->route('teacher.index'); }
	if ($user->role === 'family') { return redirect()->route('family.index'); }
	return redirect()->route('login');
})->name('index');

Route::get('/login', [UserController::class,'showLoginForm'])->name('login');
Route::post('/login',[UserController::class, 'login']);
Route::post('/logout',[UserController::class, 'logout'])->name('logout');

// Admin routes
Route::prefix('admin')->middleware('auth')->group(function () {
	Route::get('/', [AdminController::class, 'index'])->name('admin.index');
	Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
	Route::get('/users/export', [AdminController::class, 'usersExport'])->name('admin.users.export');
	Route::post('/users', [AdminController::class, 'usersStore'])->name('admin.users.store');
	Route::post('/users/{user}/update', [AdminController::class, 'usersUpdate'])->name('admin.users.update');
	Route::post('/users/{user}/delete', [AdminController::class, 'usersDestroy'])->name('admin.users.delete');

	Route::get('/families', [AdminController::class, 'families'])->name('admin.families');
	Route::get('/families/export', [AdminController::class, 'familiesExport'])->name('admin.families.export');
	Route::post('/families', [AdminController::class, 'familiesStore'])->name('admin.families.store');
	Route::post('/families/{family}/assign-student', [AdminController::class, 'familyAssignStudent'])->name('admin.families.assign');
	Route::post('/families/{family}/update', [AdminController::class, 'familiesUpdate'])->name('admin.families.update');
	Route::post('/families/{family}/delete', [AdminController::class, 'familiesDestroy'])->name('admin.families.delete');

	Route::get('/teachers', [AdminController::class, 'teachers'])->name('admin.teachers');
	Route::get('/teachers/export', [AdminController::class, 'teachersExport'])->name('admin.teachers.export');
	Route::post('/teachers', [AdminController::class, 'teachersStore'])->name('admin.teachers.store');
	Route::post('/teachers/{teacher}/assign-student', [AdminController::class, 'teacherAssignStudent'])->name('admin.teachers.assign');
	Route::post('/teachers/{teacher}/update', [AdminController::class, 'teachersUpdate'])->name('admin.teachers.update');
	Route::post('/teachers/{teacher}/delete', [AdminController::class, 'teachersDestroy'])->name('admin.teachers.delete');

	Route::get('/sections', [AdminController::class, 'sections'])->name('admin.sections');
	Route::get('/sections/export', [AdminController::class, 'sectionsExport'])->name('admin.sections.export');
	Route::post('/sections', [AdminController::class, 'sectionsStore'])->name('admin.sections.store');
	Route::get('/sections/{section}/students', [AdminController::class, 'sectionStudents'])->name('admin.sections.students');
	Route::post('/sections/{section}/students', [AdminController::class, 'studentsStore'])->name('admin.sections.students.store');
	Route::post('/sections/{section}/update', [AdminController::class, 'sectionsUpdate'])->name('admin.sections.update');
	Route::post('/sections/{section}/delete', [AdminController::class, 'sectionsDestroy'])->name('admin.sections.delete');

	Route::post('/students/{student}/delete', [AdminController::class, 'studentsDestroy'])->name('admin.students.delete');

	Route::get('/students/{student}', function($studentId){
		$student = App\Models\Student::with(['family','teachers.user','section','tags','assessmentPeriods','tests.responses','tests.scores'])->findOrFail($studentId);
		
		return view('admin.students.show', compact('student','longitudinal'));
	})->name('admin.students.show');

	Route::get('/students/{student}/record', [\App\Http\Controllers\ChecklistController::class, 'record'])
		->name('admin.students.record');

	Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
	Route::get('/reports/export', [AdminController::class, 'reportsExport'])->name('admin.reports.export');
	// Admin read-only result view
	Route::get('/tests/{test}/result', [AdminController::class, 'testResult'])->name('admin.tests.result');
	// Admin delete a test (hard delete)
	Route::post('/tests/{test}/delete', function($testId){
		$test = \App\Models\Test::findOrFail($testId);
		$test->delete();
		return redirect()->route('admin.reports');
	})->name('admin.tests.delete');

	// Admin archive a test (completed or cancelled only)
	Route::post('/tests/{test}/archive', function($testId){
		$test = \App\Models\Test::with('observer')->findOrFail($testId);
		if (!in_array($test->status, ['finalized','completed','cancelled'])) {
			return redirect()->route('admin.reports');
		}
		$test->status = 'archived';
		$test->save();
		return redirect()->route('admin.reports');
	})->name('admin.tests.archive');

	Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
	Route::post('/profile', [AdminController::class, 'profileUpdate'])->name('admin.profile.update');

	Route::get('/help', [AdminController::class, 'help'])->name('admin.help');

	Route::get('/domains', [AdminController::class, 'domains'])->name('admin.domains');
	});

Route::prefix('family')->middleware('auth')->group(function () {
	Route::get('/', [FamilyController::class, 'index'])->name('family.index');
	Route::get('/children/{student}', [FamilyController::class, 'child'])->name('family.child');
	Route::post('/tests/start/{student}', [FamilyController::class, 'startTest'])->name('family.tests.start');
	Route::get('/tests/{test}/domain/{domain}/question/{index}', [FamilyController::class, 'showQuestion'])->name('family.tests.question');
	Route::post('/tests/{test}/domain/{domain}/question/{index}', [FamilyController::class, 'submitQuestion'])->name('family.tests.question.submit');
	Route::get('/tests/{test}/result', [FamilyController::class, 'result'])->name('family.tests.result');
	Route::post('/tests/{test}/finalize', [FamilyController::class, 'finalize'])->name('family.tests.finalize');
	Route::post('/tests/{test}/mark-incomplete', [FamilyController::class, 'markIncomplete'])->name('family.tests.incomplete');
	Route::post('/tests/{test}/cancel', [FamilyController::class, 'cancel'])->name('family.tests.cancel');
	Route::post('/tests/{test}/terminate', [FamilyController::class, 'terminate'])->name('family.tests.terminate');
	Route::post('/tests/{test}/pause', [FamilyController::class, 'pause'])->name('family.tests.pause');
});

// Teacher routes (simple, no CSS UI)
Route::prefix('teacher')->middleware('auth')->group(function () {
	Route::get('/', [\App\Http\Controllers\TeacherController::class, 'index'])->name('teacher.index');
	Route::get('/students/{student}', [\App\Http\Controllers\TeacherController::class, 'student'])->name('teacher.student');
	Route::post('/tests/start/{student}', [\App\Http\Controllers\TeacherController::class, 'startTest'])->name('teacher.tests.start');
	Route::get('/tests/{test}/domain/{domain}/question/{index}', [\App\Http\Controllers\TeacherController::class, 'showQuestion'])->name('teacher.tests.question');
	Route::post('/tests/{test}/domain/{domain}/question/{index}', [\App\Http\Controllers\TeacherController::class, 'submitQuestion'])->name('teacher.tests.question.submit');
	Route::get('/tests/{test}/result', [\App\Http\Controllers\TeacherController::class, 'result'])->name('teacher.tests.result');
	Route::post('/tests/{test}/finalize', [\App\Http\Controllers\TeacherController::class, 'finalize'])->name('teacher.tests.finalize');
	Route::post('/tests/{test}/mark-incomplete', [\App\Http\Controllers\TeacherController::class, 'markIncomplete'])->name('teacher.tests.incomplete');
	Route::post('/tests/{test}/cancel', [\App\Http\Controllers\TeacherController::class, 'cancel'])->name('teacher.tests.cancel');
	Route::post('/tests/{test}/terminate', [\App\Http\Controllers\TeacherController::class, 'terminate'])->name('teacher.tests.terminate');
	Route::post('/tests/{test}/pause', [\App\Http\Controllers\TeacherController::class, 'pause'])->name('teacher.tests.pause');
});
