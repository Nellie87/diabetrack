<?php

namespace App\Http\Controllers;

use App\Models\glucose_reading;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GlucoseReadingController extends Controller
{
    public function submit(Request $request){
        $data = $request->validate([
            'Datetime' => 'required|date',              
            'GlucoseLevel' => 'required|integer',  
            'Notes' => 'nullable|string|max:255',   
        ]);

        $glucose = glucose_reading::create($data);

        return response()->json(['success' => 'Form submitted successfully!', 'patient' => $glucose]);
    }

        public function getData()
    {

    $readings = glucose_reading::select('GlucoseLevel', 'Datetime')->get();

   
    $formattedData = $readings->map(function($reading) {
        return [
            'GlucoseLevel' => $reading->GlucoseLevel,
            'Datetime' => Carbon::parse($reading->Datetime)->format('m-d H:i'),
        ];
    });

    // Return the formatted data as a JSON response
    return response()->json($formattedData);
    }
}