<?php
// app/Http/Controllers/MessageController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserMessage;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'message' => 'required|string|max:500',
        ]);

        Mail::to($request->email)->send(new UserMessage($request->message));

        return response()->json(['success' => true]);
    }
}
