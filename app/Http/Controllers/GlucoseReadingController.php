<?php

namespace App\Http\Controllers;

use App\Models\glucose_reading;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GlucoseReadingController extends Controller
{

    
    public function submit(Request $request)
    {
        $data = $request->validate([
            'Datetime' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $timezone = 'MSK';  
                    $inputDateTime = Carbon::parse($value, $timezone);
                    $now = Carbon::now($timezone);
        
                    if ($inputDateTime->gt($now)) {
                        $fail('The ' . $attribute . ' must be a date and time before or equal to now.');
                    }

                    if (glucose_reading::where('Datetime', $inputDateTime)->exists()) {
                        $fail('The ' . $attribute . ' has already been taken.');
                    }
                },
            ],
            'GlucoseLevel' => 'required|integer',
            'Notes' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $userId = (string) $user->id;

        $glucose = new glucose_reading();
        $glucose->fill($data);
        $glucose->PatientID = $userId; 

        $glucose->save();

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