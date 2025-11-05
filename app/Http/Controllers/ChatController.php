<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = Conversation::where('user_one_id', Auth::id())
            ->orWhere('user_two_id', Auth::id())
            ->with(['userOne', 'userTwo'])
            ->get();

        return view('chat.index', compact('conversations'));
    }

    public function search(Request $request)
    {
        $search = $request->input('query');

        $users = User::where('name', 'like', "%{$search}%")
            ->where('id', '!=', auth()->id())
            ->with('userInfo') // include profile picture
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'profile_picture' => $user->userInfo->profile_picture ?? null,
                ];
            });

        return response()->json($users);
    }


    public function show(Conversation $conversation)
    {
        $conversation->load('messages.sender');
        return response()->json($conversation);
    }

    public function sendMessage(Request $request)
    {
        $conversation = Conversation::findOrFail($request->conversation_id);

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'body' => $request->body,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    public function startConversation(Request $request)
    {
        $otherUserId = $request->user_id;

        $conversation = Conversation::firstOrCreate(
            [
                'user_one_id' => min(Auth::id(), $otherUserId),
                'user_two_id' => max(Auth::id(), $otherUserId),
            ]
        );

        return response()->json($conversation);
    }
}
