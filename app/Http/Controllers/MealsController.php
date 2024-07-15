<?php

namespace App\Http\Controllers;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Meal;
use Illuminate\Support\Facades\Http;

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
    

        $transformedReadings = $readings->map(function ($item) use ($carbMapping) {
            $carbName = $item->meal_type;
            $item->Date = Carbon::parse($item->Date)->format('Y-m-d H:i:s');
            $item->GlucoseConsumed = $carbMapping[$carbName] ?? null;
            return $item;
        });
    
        return response()->json($transformedReadings);
    }

    public function getData2()
    {
        $user = Auth::user();
        $userId = $user->id;

        $readings = Meal::where('PatientID', $userId)
                ->where('meal_type', 'Breakfast')
                ->select('meal_type', 'Date', 'items')
                ->get();  
        

        $items = $readings->pluck('items');

        $foodNames = [];
        foreach ($items as $subArray) {
            foreach ($subArray as $item) {
                $foodNames[] = $item['food_name'];
            }
        }

        $foodQuantity = [];
        foreach ($items as $subArrays) {
            foreach ($subArrays as $item2) {
                $foodQuantity[] = $item2['quantity'];
            }
        }
        $processedData = $this->convert($foodNames,$foodQuantity);

        return response()->json($processedData);
    }

    public function convert($foodNames,$foodQuantity)
    {
        $foodName = $foodNames[0];
        $foodQuant = $foodQuantity[0];

        $carbohydrates = $this->fetchCarbohydrateData($foodName);

        if ($carbohydrates !== null) {
            return response()->json($foodQuant*$carbohydrates);
        } else {
            return response()->json($foodName);
        }
    }

    private function fetchCarbohydrateData($foodName)
    {
        $appId = env('NUTRITIONIX_APP_ID');
        $apiKey = env('NUTRITIONIX_API_KEY');
        $url = "https://trackapi.nutritionix.com/v2/natural/nutrients";

        $response = Http::withHeaders([
            'x-app-id' => $appId,
            'x-app-key' => $apiKey,
            'Content-Type' => 'application/json'
        ])->post($url, [
            'query' => $foodName
        ]);

        $data = $response->json();

        if (isset($data['foods']) && count($data['foods']) > 0) {
            $carbohydrates = $data['foods'][0]['nf_total_carbohydrate']; 
            return $carbohydrates;
        } else {
            return null;
        }
    }
    
}
