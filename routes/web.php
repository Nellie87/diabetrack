<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordVerificationController;
use App\Http\Controllers\Auth\CustomAuthenticatedSessionController;
use App\Http\Controllers\DietController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\GlucoseReadingController;
use App\Http\Controllers\MedicationController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Models\Patient;
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
    Route::post('/submit-form', [GlucoseReadingController::class, 'submit']);
    Route::post('/submit-form2', [DietController::class, 'submit']);
    Route::post('/submit-form3', [MedicationController::class, 'submit']);
});

Route::post('/logout', [CustomAuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
    Route::middleware(['auth:sanctum', 'verified'])->get('/doctor', [DoctorController::class, 'index'])->name('doctor');


    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/patient', [PatientController::class, 'index'])->name('patient.index');
        Route::get('/sesh', [UserController::class, 'getProfile'])->name('patient.profile');
        Route::get('/chart-data', [GlucoseReadingController::class, 'getData']);
        
    });

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');
    
        Route::get('/patient', [PatientController::class, 'index']);
    
        Route::get('/doctor', function () {
            return Inertia::render('Doctor');
        })->name('doctor');
    });
    Route::get('/patient', function () {
            return Inertia::render('Patient');
        })->name('patient');

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

Route::get('scum', function(){
    return Patient::get();
});

Route::get('/users', [UserController::class, 'index'])->name('users.index');

Route::post('/users', [UserController::class, 'store'])->name('users.store');
