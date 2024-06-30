<?php

namespace App\Http\Controllers;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Medication;

use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function submit(Request $request)
    {
        $data = $request->validate([
            'MedicationName' => 'required|string|max:255',
            'Type' => 'required|string|max:255',
            'Dosage' => 'required|integer',
            'Frequency' => 'required|integer',
            'StartDate' => 'required|date|date_format:Y-m-d',
        ]);

        $user = Auth::user();
        $userId = (string) $user->id;

        // Create a new instance of GlucoseReading model and fill it with validated data
        $diet = new Medication();
        $diet->StartDate = $data['StartDate'];
        $diet->fill($data);
        $diet->PatientID = $userId; // Assign the authenticated user's ID to the model

        // Save the model to the database
        $diet->save();

        // Optionally, return a JSON response
        return response()->json(['success' => 'Form submitted successfully!', 'diet' => $diet]);
    }
}
