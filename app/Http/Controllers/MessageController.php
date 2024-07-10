<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    public function getMessageHistory($userId)
    {
        $messages = Message::where('from_id', $userId)
            ->orWhere('to_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        $recipient = User::where('email', $request->email)->first();

        if (!$recipient) {
            return response()->json(['success' => false], 404);
        }

        $message = new Message();
        $message->from_id = auth()->user()->id;
        $message->to_id = $recipient->id;
        $message->content = $request->message;
        $message->save();

        return response()->json(['success' => true]);
    }
}
