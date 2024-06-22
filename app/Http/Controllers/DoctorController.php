<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Doctor; // Ensure this import is present

class DoctorController extends Controller
{
    public function index()
    {
        return Inertia::render('Doctor');
    }

    public function store(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'specialization' => 'nullable|string|max:255',
            'licenseNumber' => 'nullable|string|max:50',
            'institution' => 'nullable|string|max:255',
            'graduationYear' => 'nullable|integer',
            'idDocument' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
            'passportPhoto' => 'nullable|file|mimes:jpeg,png|max:2048',
            'medicalLicenseCard' => 'nullable|file|mimes:jpeg,png|max:2048',
        ]);

        // Handle file uploads
        if ($request->hasFile('idDocument')) {
            $validatedData['idDocument'] = $request->file('idDocument')->store('uploads');
        }

        if ($request->hasFile('passportPhoto')) {
            $validatedData['passportPhoto'] = $request->file('passportPhoto')->store('uploads');
        }

        if ($request->hasFile('medicalLicenseCard')) {
            $validatedData['medicalLicenseCard'] = $request->file('medicalLicenseCard')->store('uploads');
        }

        // Create a new doctor record
        $doctor = Doctor::create($validatedData);

        return response()->json(['message' => 'Doctor registered successfully', 'doctor' => $doctor], 201);
    }
}
