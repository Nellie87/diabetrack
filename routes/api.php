<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\UserController;




Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// routes/api.php
Route::middleware('auth:sanctum')->get('/users', [UserController::class, 'index']);

Route::post('/submit-form', [FormController::class, 'submit']);

Route::get('/chart-data', [FormController::class, 'getData']);

