<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string|max:300',
            'is_anonymous' => 'boolean'
        ]);

        Comment::create([
            'post_id' => $validated['post_id'],
            'user_id' => Auth::id(),
            'is_anonymous' => $request->boolean('is_anonymous'),
            'content' => $validated['content']
        ]);

        return back();
    }
}
