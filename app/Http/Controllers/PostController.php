<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user','comments.user'])
            ->latest()->get();
        return view('posts.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:500',
            'is_anonymous' => 'boolean'
        ]);

        Post::create([
            'user_id' => Auth::id(),
            'is_anonymous' => $request->boolean('is_anonymous'),
            'content' => $validated['content']
        ]);

        return back()->with('success', 'Post created successfully!'); 
    }

    public function history() {
        return view('posts.history');
    }

    public function show($id)
    {
        return view('posts.show', ['postId' => $id]);
    }
}
