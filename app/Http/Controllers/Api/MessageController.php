<?php

namespace App\Http\Controllers\Api;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $messages = Message::where('recipient_id', $request->user()->id)
            ->orWhere('sender_id', $request->user()->id)
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return response()->json(['messages' => $messages]);
    }

    public function getConversation(Request $request, $userId)
    {
        $messages = Message::where(function ($query) use ($request, $userId) {
            $query->where('sender_id', $request->user()->id)
                  ->where('recipient_id', $userId);
        })->orWhere(function ($query) use ($request, $userId) {
            $query->where('sender_id', $userId)
                  ->where('recipient_id', $request->user()->id);
        })->with(['sender', 'recipient'])
         ->orderBy('created_at', 'asc')
         ->paginate(50);

        return response()->json(['messages' => $messages]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'recipient_id' => $validated['recipient_id'],
            'message' => $validated['message'],
        ]);

        return response()->json(['message' => 'Message sent', 'data' => $message->load(['sender', 'recipient'])], 201);
    }

    public function markAsRead(Request $request, $id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        if ($message->recipient_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message->update([
            'read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['message' => 'Message marked as read']);
    }
}
