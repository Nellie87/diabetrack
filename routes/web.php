<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordVerificationController;
use App\Http\Controllers\Auth\CustomAuthenticatedSessionController;
use App\Http\Controllers\DoctorController;


use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/verify-password', [PasswordVerificationController::class, 'verify'])->name('verify-password');
});

Route::post('/logout', [CustomAuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
    Route::middleware(['auth:sanctum', 'verified'])->get('/doctor', [DoctorController::class, 'index'])->name('doctor');


    