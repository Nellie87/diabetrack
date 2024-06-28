<?php

namespace App\Http\Controllers;

use App\Models\glucose_reading;
use Illuminate\Http\Request;

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
        $data = glucose_reading::pluck('GlucoseReading');
        return response()->json($data);
    }
}