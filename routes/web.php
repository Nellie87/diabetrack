<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordVerificationController;
use App\Http\Controllers\Auth\CustomAuthenticatedSessionController;
use App\Http\Controllers\MealsController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\GlucoseReadingController;
use App\Http\Controllers\MedicationController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Models\Patient;
use Inertia\Inertia;
use App\Http\Controllers\MessageController;




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
    Route::post('/submit-form2', [MealsController::class, 'submit']);
    Route::post('/submit-form3', [MedicationController::class, 'submit']);
    Route::post('/submit-form4', [MedicationController::class, 'convert']);
    Route::post('/search', [MedicationController::class, 'search']);
    Route::post('/meals', [MealsController::class, 'submit']);

});

Route::post('/logout', [CustomAuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
    Route::middleware(['auth:sanctum', 'verified'])->get('/doctor', [DoctorController::class, 'index'])->name('doctor');


    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/patient', [PatientController::class, 'index'])->name('patient.index');
        Route::get('/patient2', [PatientController::class, 'index2'])->name('patient.index2');
        Route::get('/patient3', [PatientController::class, 'index3'])->name('patient.index3');
        Route::get('/sesh', [UserController::class, 'getProfile'])->name('patient.profile');
        Route::get('/chart-data', [GlucoseReadingController::class, 'getData']);
        Route::get('/chart-datas', [MealsController::class, 'getData']);
        Route::get('/chart2', [MealsController::class, 'getData1']);
        Route::get('/chart3', [MealsController::class, 'getData2']);
        Route::get('/chart4', [MealsController::class, 'getData3']);
        Route::get('/chart-datas1', [MedicationController::class, 'getData']);
        Route::get('/diet', [PatientController::class, 'index2'])->name('meals.index2');
        
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
// Route for submitting glucose readings
Route::post('/glucose/submit', [GlucoseReadingController::class, 'submit']);

// Route for fetching glucose readings
Route::get('/glucose/readings', [GlucoseReadingController::class, 'getData']);

// Fetch message history for a specific user
Route::get('/messages/{userId}', [MessageController::class, 'getMessageHistory']);

// Send a new message
Route::post('/send-message', [MessageController::class, 'sendMessage']);

// Send a reply to a message
Route::post('/send-reply', [MessageController::class, 'sendReply']);

// Fetch message history (including replies) for a specific user
Route::get('/message-history/{userId}', [MessageController::class, 'fetchMessageHistory'])->name('message-history');
