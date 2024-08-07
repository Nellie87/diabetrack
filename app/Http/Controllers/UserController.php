<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserRoleChanged;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    // Method to fetch all users
    // public function index()
    // {
    //     $users = User::all();
    //     return response()->json($users);
    // }
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
public function index(Request $request)
{
    $role = $request->query('role');
    
    // Fetch users based on role condition
    if ($role !== null) {
        $users = User::where('role', $role)->get();
    } else {
        $users = User::all();
    }
    
    return response()->json($users);
}



    // Method to update a user's role
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->role = $request->input('role');
            $user->save();
    
            try {
                // Send notification email
                Mail::to($user->email)->send(new UserRoleChanged($user));
                return response()->json(['message' => 'User updated successfully and email sent!']);
            } catch (\Exception $e) {
                return response()->json(['message' => 'User updated but email not sent: ' . $e->getMessage()]);
            }
        }
        return response()->json(['error' => 'User not found'], 404);
    }

    public function getProfile()
    {
        $user = Auth::user();
        $userId = (string) $user->id;
        
        return response() -> json($userId);
        }
        public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:admin,patient,doctor', // Validate role input
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password ?? 'defaultpassword'), // Set default password if none provided
        ]);

        return response()->json(['success' => true, 'user' => $user]);
    }
}
