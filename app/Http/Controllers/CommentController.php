<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Traits\ChecksCensoredWords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use ChecksCensoredWords;

    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|min:1|max:1000',
            'is_anonymous' => 'boolean'
        ]);
 
        $censorCheck = $this->containsCensoredWord($request->input('content'));

        if ($censorCheck['found']) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'content' => 'Your comment contains inappropriate content. Please remove it and try again.'
                ]);
        }

        Comment::create([
            'post_id' => $request->input('post_id'),
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'is_anonymous' => $request->input('is_anonymous', false),
        ]);

        return redirect()->back()->with('success', 'Comment posted successfully!');
    }

    public function destroy(Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) {
            return redirect()->back()->with('error', 'You are not authorized to delete this comment.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully!');
    }
}
