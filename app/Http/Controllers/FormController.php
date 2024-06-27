<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class FormController extends Controller
{
        public function submit(Request $request)
    {
        // Validate the form data
        $data = $request->validate([
            'Gender' => 'required|string|max:255',
            'PhoneNo' => 'required|string|max:255',
            'Address' => 'required|string|max:255',
            'EmergencyContactName' => 'required|string|max:255',
            'EmergencyContactPhone' => 'required|string|max:255',
            'DoctorID' => 'required|string|max:255',
        ]);

        // Create a new contact record in the database
        $patient = Patient::create($data);

        // Return a JSON response
        return response()->json(['success' => 'Form submitted successfully!', 'patient' => $patient]);
    }
}
