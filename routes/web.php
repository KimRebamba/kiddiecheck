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
