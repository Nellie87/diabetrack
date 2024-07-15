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
        $items = $request['items'];

        $sum = 0;
        foreach ($items as $item) {
            $itemName = $item['food_name'];
            $itemQuantity = $item['quantity'];
        
            $processedData = $this->convert($itemName, $itemQuantity);
            foreach ($processedData as $value) {
                if (is_numeric($value)) { 
                 $sum += $value;
                } else {
                    }
                }
          }

        $validatedData = $request->validate([
            'meal_type' => 'required|string|max:255',
            'description' => 'required|string',
            'items' => 'required|array',
            'Date' => 'required|date', 
        ]);

        $user = Auth::user();
        $userId = (string) $user->id;


        $diet = new Meal();
        $diet -> fill($validatedData);
        $diet->PatientID = $userId; 
        $diet->Carbohydrates = $sum;

        
        $diet->save();

        
        return response()->json(['success' => 'Form submitted successfully!', 'diet' => $diet]);
    }

    public function getData()
    {
        $user = Auth::user();
        $userId = $user->id; 
    
        $readings = Meal::where('PatientID', $userId)
            ->where('meal_type','Breakfast')
            ->select('Carbohydrates','Date')
            ->get();


        $transformedReadings = $readings->map(function ($item) {
            $carbName = $item->Carbohydrates;
            $item->Date = Carbon::parse($item->Date)->format('Y-m-d H:i:s');
            return $item;
        });
    
        return response()->json($transformedReadings);
    }

    public function getData1()
    {
        $user = Auth::user();
        $userId = $user->id; 
    
        $readings = Meal::where('PatientID', $userId)
            ->where('meal_type','Lunch')
            ->select('Carbohydrates','Date')
            ->get();


        $transformedReadings = $readings->map(function ($item) {
            $carbName = $item->Carbohydrates;
            $item->Date = Carbon::parse($item->Date)->format('Y-m-d H:i:s');
            return $item;
        });
    
        return response()->json($transformedReadings);
    }

    public function getData2()
    {
        $user = Auth::user();
        $userId = $user->id; 
    
        $readings = Meal::where('PatientID', $userId)
            ->where('meal_type','Dinner')
            ->select('Carbohydrates','Date')
            ->get();


        $transformedReadings = $readings->map(function ($item) {
            $carbName = $item->Carbohydrates;
            $item->Date = Carbon::parse($item->Date)->format('Y-m-d H:i:s');
            return $item;
        });
    
        return response()->json($transformedReadings);
    }

    public function convert($itemName,$itemQuantity)
    {
        $foodName=$itemName;
        $foodQuant=$itemQuantity;
        
        $carbohydrates = $this->fetchCarbohydrateData($foodName);

                if ($carbohydrates !== null) {
                    $results = $foodQuant * $carbohydrates; 
                  } else {
                    $results = $foodName; 
                  }
                  
                return response()->json($results);

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
