<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\TeacherController;
use App\Models\User;

Route::get('/', [UserController::class, 'index'])->name('index');

Route::get('/login', [UserController::class,'showLoginForm'])->name('login');
Route::post('/login',[UserController::class, 'login']);
Route::post('/logout',[UserController::class, 'logout'])->name('logout');

// Admin routes
Route::prefix('admin')->middleware('auth')->group(function () {
	Route::get('/', [AdminController::class, 'index'])->name('admin.index');
	Route::get('/eccd', [AdminController::class, 'eccd'])->name('admin.eccd');
	Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
	Route::get('/users/create', [AdminController::class, 'usersCreate'])->name('admin.users.create');
	Route::post('/users', [AdminController::class, 'usersStore'])->name('admin.users.store');
	Route::get('/users/{user}', [AdminController::class, 'usersShow'])->name('admin.users.show');
	Route::get('/users/{user}/edit', [AdminController::class, 'usersEdit'])->name('admin.users.edit');
	Route::put('/users/{user}', [AdminController::class, 'usersUpdate'])->name('admin.users.update');
	Route::post('/users/{user}/status', [AdminController::class, 'usersUpdateStatus'])->name('admin.users.status');
	Route::post('/users/{user}/force-reset', [AdminController::class, 'usersForcePasswordReset'])->name('admin.users.force_reset');
	Route::post('/users/{user}/reset-password', [AdminController::class, 'usersResetPassword'])->name('admin.users.reset_password');
	Route::post('/users/{user}/resend-notification', [AdminController::class, 'usersResendNotification'])->name('admin.users.resend_notification');
	Route::delete('/users/{user}', [AdminController::class, 'usersDestroy'])->name('admin.users.destroy');
	Route::get('/students', [AdminController::class, 'students'])->name('admin.students');
	Route::get('/students/create', [AdminController::class, 'studentsCreate'])->name('admin.students.create');
	Route::post('/students', [AdminController::class, 'studentsStore'])->name('admin.students.store');
	Route::get('/students/{student}', [AdminController::class, 'studentsShow'])->name('admin.students.show');
	Route::get('/students/{student}/edit', [AdminController::class, 'studentsEdit'])->name('admin.students.edit');
	Route::put('/students/{student}', [AdminController::class, 'studentsUpdate'])->name('admin.students.update');
	Route::post('/students/{student}/assign-teacher', [AdminController::class, 'studentsAssignTeacher'])->name('admin.students.assign_teacher');
	Route::delete('/students/{student}/teachers/{teacher}', [AdminController::class, 'studentsRemoveTeacher'])->name('admin.students.remove_teacher');
	Route::post('/students/{student}/transfer-family', [AdminController::class, 'studentsTransferFamily'])->name('admin.students.transfer_family');
	Route::post('/students/bulk-assign-teacher', [AdminController::class, 'studentsBulkAssignTeacher'])->name('admin.students.bulk_assign_teacher');
	Route::get('/students/export', [AdminController::class, 'studentsExport'])->name('admin.students.export');
	Route::post('/tests/{test}/cancel', [AdminController::class, 'testsCancel'])->name('admin.tests.cancel');
	Route::get('/assessments', [AdminController::class, 'assessments'])->name('admin.assessments');
	Route::get('/assessments/{period}', [AdminController::class, 'assessmentsShow'])->name('admin.assessments.show');
	Route::post('/assessments/{period}/extend', [AdminController::class, 'assessmentsExtend'])->name('admin.assessments.extend');
	Route::post('/assessments/{period}/close', [AdminController::class, 'assessmentsClose'])->name('admin.assessments.close');
	Route::post('/assessments/{period}/recompute', [AdminController::class, 'assessmentsRecompute'])->name('admin.assessments.recompute');
	Route::get('/assessments/{period}/export', [AdminController::class, 'assessmentsExport'])->name('admin.assessments.export');
	Route::post('/assessments/{period}/notify', [AdminController::class, 'assessmentsNotify'])->name('admin.assessments.notify');
	Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
	Route::get('/reports/export/{format}', [AdminController::class, 'reportsExport'])->name('admin.reports.export');
	Route::get('/scales', [AdminController::class, 'scales'])->name('admin.scales');
	Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
	Route::post('/profile', [AdminController::class, 'profileUpdate'])->name('admin.profile.update');
	Route::post('/profile/password', [AdminController::class, 'profilePassword'])->name('admin.profile.password');
});

// Teacher routes
Route::prefix('teacher')->middleware('auth')->group(function () {
	Route::get('/', [TeacherController::class, 'index'])->name('teacher.index');
	Route::get('/eccd', [TeacherController::class, 'eccd'])->name('teacher.eccd');
});

// Family routes
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
	Route::get('/family', [\App\Http\Controllers\TeacherController::class, 'family'])->name('teacher.family');
	Route::get('/family/{family}', [\App\Http\Controllers\TeacherController::class, 'familyShow'])->name('teacher.family.show');
	Route::get('/sections', [\App\Http\Controllers\TeacherController::class, 'sections'])->name('teacher.sections');
	Route::get('/sections/{section}', [\App\Http\Controllers\TeacherController::class, 'sectionsShow'])->name('teacher.sections.show');
	Route::get('/reports', [\App\Http\Controllers\TeacherController::class, 'reports'])->name('teacher.reports');
	Route::get('/reports/{student}/{period}', [\App\Http\Controllers\TeacherController::class, 'reportShow'])->name('teacher.reports.show');
	Route::get('/reports/{student}/{period}/{test}', [\App\Http\Controllers\TeacherController::class, 'reportDetail'])->name('teacher.reports.detail');
	Route::get('/help', [\App\Http\Controllers\TeacherController::class, 'help'])->name('teacher.help');
	Route::get('/profile', [\App\Http\Controllers\TeacherController::class, 'profile'])->name('teacher.profile');
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
