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
	Route::get('/eccd', [FamilyController::class, 'eccd'])->name('family.eccd');
});
