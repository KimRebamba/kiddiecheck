<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AdminController;

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

	Route::get('/teachers', [AdminController::class, 'teachers'])->name('admin.teachers');
	Route::post('/teachers', [AdminController::class, 'teachersStore'])->name('admin.teachers.store');

	Route::get('/sections', [AdminController::class, 'sections'])->name('admin.sections');
	Route::post('/sections', [AdminController::class, 'sectionsStore'])->name('admin.sections.store');
	Route::get('/sections/{section}/students', [AdminController::class, 'sectionStudents'])->name('admin.sections.students');
	Route::post('/sections/{section}/students', [AdminController::class, 'studentsStore'])->name('admin.sections.students.store');

	Route::get('/students/{student}', function($studentId){
		$student = App\Models\Student::with(['family','teachers.user','section','tags','tests.responses','tests.scores'])->findOrFail($studentId);
		return view('admin.students.show', compact('student'));
	})->name('admin.students.show');

	Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');

	Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
	Route::post('/profile', [AdminController::class, 'profileUpdate'])->name('admin.profile.update');

	Route::get('/help', [AdminController::class, 'help'])->name('admin.help');
});
