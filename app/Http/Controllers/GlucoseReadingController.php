<?php

namespace App\Http\Controllers;

use App\Models\glucose_reading;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GlucoseReadingController extends Controller
{

    
    public function submit(Request $request)
    {
        $data = $request->validate([
            'Datetime' => 'required|date',
            'GlucoseLevel' => 'required|integer',
            'Notes' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $userId = (string) $user->id;

        // Create a new instance of GlucoseReading model and fill it with validated data
        $glucose = new glucose_reading();
        $glucose->fill($data);
        $glucose->PatientID = $userId; // Assign the authenticated user's ID to the model

        // Save the model to the database
        $glucose->save();

        // Optionally, return a JSON response
        return response()->json(['success' => 'Form submitted successfully!', 'glucose' => $glucose]);
    }

    public function getData()
{
    $user = Auth::user();
    $userId = $user->id;

    // Select all fields ('*') instead of just 'GlucoseLevel' and 'Datetime'
    $readings = glucose_reading::where('PatientID', $userId)
        ->orderBy('Datetime', 'asc')
        ->get();

    $formattedData = $readings->map(function($reading) {
        return [
            'GlucoseLevel' => $reading->GlucoseLevel,
            'Datetime' => Carbon::parse($reading->Datetime)->format('m-d H:i'),
            'Notes' => $reading->Notes, // Include 'Notes' field in the response
        ];
    });

    // Return the formatted data as a JSON response
    return response()->json($formattedData);
}

}