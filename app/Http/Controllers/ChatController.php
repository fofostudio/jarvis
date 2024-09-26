<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function getUsers()
    {
        $currentUser = auth()->user();
        return User::where('id', '!=', $currentUser->id)
            ->select('id', 'name')
            ->get();
    }



    public function getOrCreateConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $currentUser = auth()->user();
        $otherUser = User::findOrFail($request->user_id);

        $conversation = Conversation::whereHas('participants', function ($query) use ($currentUser) {
            $query->where('user_id', $currentUser->id);
        })->whereHas('participants', function ($query) use ($otherUser) {
            $query->where('user_id', $otherUser->id);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create(['name' => 'Chat']);
            $conversation->participants()->attach([$currentUser->id, $otherUser->id]);
        }

        return $conversation->load('participants');
    }


    public function getMessages($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        return $conversation->messages()->with('user')->orderBy('created_at', 'asc')->get();
    }

    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $conversation = Conversation::findOrFail($conversationId);
        $message = $conversation->messages()->create([
            'content' => $request->content,
            'user_id' => auth()->id(),
        ]);

        // Aquí podrías emitir un evento para notificaciones en tiempo real

        return $message->load('user');
    }
}
