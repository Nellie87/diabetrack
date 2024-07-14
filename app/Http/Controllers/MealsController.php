<?php

namespace App\Http\Controllers;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Meal;

use Illuminate\Http\Request;

class MealsController extends Controller
{
    public function submit(Request $request)
    {
        $validatedData = $request->validate([
            'meal_type' => 'required|string|max:255',
            'description' => 'required|string',
            'items' => 'required|array',
            'datetime' => 'required|date', 
        ]);

        $user = Auth::user();
        $userId = (string) $user->id;


        $diet = new Meal();
        $diet -> fill($validatedData);
        $diet->PatientID = $userId; 

        
        $diet->save();

        
        return response()->json(['success' => 'Form submitted successfully!', 'diet' => $diet]);
    }

    public function getData()
    {
        $user = Auth::user();
        $userId = $user->id;
    
        $readings = Meal::where('PatientID', $userId)
            ->select('meal_type','Date')
            ->get();
    
        // Map to convert carbohydrate names to specified values
        $carbMapping = [
            'Breakfast' => 233,
            'Lunch' => 533,
        ];
    
        // Transform the readings
        $transformedReadings = $readings->map(function ($item) use ($carbMapping) {
            $carbName = $item->meal_type;
            $item->Date = Carbon::parse($item->Date)->format('Y-m-d H:i:s'); // Format as needed
            $item->GlucoseConsumed = $carbMapping[$carbName] ?? null;
            return $item;
        });
    
        return response()->json($transformedReadings);
    }
    
}
