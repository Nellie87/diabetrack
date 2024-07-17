<?php

namespace App\Http\Controllers;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Medication;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

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

        $diet = new Medication();
        $diet->StartDate = $data['StartDate'];
        $diet->fill($data);
        $diet->PatientID = $userId; 

        $diet->save();

        return response()->json(['success' => 'Form submitted successfully!', 'diet' => $diet]);
    }

    public function getData()
    {
        $user = Auth::user();
        $userId = $user->id;
    
        $readings = Medication::where('PatientID', $userId)
            ->select('MedicationName', 'Frequency')
            ->get();
    
        $formattedData = $readings->map(function($reading) {
            return [
                'task_name' => $reading->MedicationName,
                'progress' => $reading->Frequency,
            ];
        });
    
        return response()->json($formattedData);
    }

    public function convert(Request $request)
    {
        $foodName = $request->input('Food');

        // Fetch carbohydrate data from Nutritionix
        $carbohydrates = $this->fetchCarbohydrateData($foodName);

        if ($carbohydrates !== null) {
            return response()->json($carbohydrates);
        } else {
            return view('', ['message' => 'Food item not found']);
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
