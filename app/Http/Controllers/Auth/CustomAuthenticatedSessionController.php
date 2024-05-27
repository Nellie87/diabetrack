<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class CustomAuthenticatedSessionController extends Controller
{
    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        // Add your custom logic here
        // For example, you can add a confirmation dialog using JavaScript
        
        Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return Inertia::location(route('welcome'));
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        // return redirect('/')->with('status', 'You have been logged out.');
    }
}
