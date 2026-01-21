<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', [UserController::class, 'index'])->name('index')->middleware('auth');
Route::get('/login', [UserController::class,'showLoginForm'])->name('login');
Route::post('/login',[UserController::class, 'login']);

Route::get('/register',[UserController::class, 'showRegisterForm'])->name('register');
Route::post('/register',[UserController::class, 'store']);
