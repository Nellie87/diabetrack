<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PatientController extends Controller
{
    public function index()
    {
        return Inertia::render('Patient');
    }

    public function index2()
    {
        return Inertia::render('Patient2');
    }
}
