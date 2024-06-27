<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Method to fetch all users
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }
//     public function index(Request $request)
// {
//     $role = $request->query('role');
//     if ($role) {
//         $users = User::where('role', $role)->get();
//     } else {
//         $users = User::all();
//     }
//     return response()->json($users);
// }


    // Method to update a user's role
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'role' => 'required|string|max:255',
        ]);

        // Find the user by ID
        $user = User::find($id);

        // If user not found, return error response
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update the user's role
        $user->role = $request->input('role');
        $user->save();

        // Return the updated user
        return response()->json($user);
    }
}
