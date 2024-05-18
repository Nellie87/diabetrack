<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PasswordVerificationController extends Controller
{
    public function verify(Request $request)
    {
        // Validate the request
        $request->validate([
            'password' => 'required|string',
        ]);

        // Get the currently authenticated user
        $user = Auth::user();

        // Check if the provided password matches the user's password
        if (Hash::check($request->password, $user->password)) {
            return response()->json(['success' => true]);
        }

        // If the password doesn't match, return a failed response
        return response()->json(['success' => false], 401);
    }
}
