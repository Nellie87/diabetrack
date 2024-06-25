<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordVerificationController;
use App\Http\Controllers\Auth\CustomAuthenticatedSessionController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;

use App\Http\Controllers\UserController;


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


    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/patient', [PatientController::class, 'index'])->name('patient.index');
    });

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');
    
        Route::get('/patient', [PatientController::Class, 'index']);
    
        Route::get('/doctor', function () {
            return Inertia::render('Doctor');
        })->name('doctor');
    });

    Route::post('/login', [CustomAuthenticatedSessionController::class, 'store'])->name('login');
Route::post('/logout', [CustomAuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // // Apply middleware to redirect based on role after login
    // Route::middleware(['auth:sanctum', 'verified', 'role.redirect'])->get('/home', function () {
    //     return redirect()->route('dashboard');
    // });
// Define the routes for users
Route::get('/users', [UserController::class, 'index']);
Route::put('/users/{id}', [UserController::class, 'update']);

Route::post('/doctors', [DoctorController::class, 'store']);
Route::get('/doctors', [DoctorController::class, 'index']);
