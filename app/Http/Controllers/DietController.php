<?php

namespace App\Http\Controllers;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Diet;

use Illuminate\Http\Request;

class DietController extends Controller
{
    public function submit(Request $request)
    {
        $data = $request->validate([
            'Date' => 'required|date|date_format:Y-m-d',
            'MealType' => 'required|string|max:255',
            'FoodItems' => 'required|string|max:255',
            'Carbohydrates' => 'required|string|max:255',
            'Notes' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $userId = (string) $user->id;


        $diet = new Diet();
        $diet->Date = $data['Date'];
        $diet->fill($data);
        $diet->PatientID = $userId; 

        
        $diet->save();

        
        return response()->json(['success' => 'Form submitted successfully!', 'diet' => $diet]);
    }
}
