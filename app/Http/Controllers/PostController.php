<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Report; // Added for cleanup
use App\Models\Comment; // Added for cleanup
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'comments.user'])
            ->where('is_hidden', false) // Add this line
            ->latest()->get();
        return view('posts.index', compact('posts'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'is_anonymous' => 'boolean',
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'is_anonymous' => $validated['is_anonymous'] ?? false,
        ]);

        return redirect()->route('posts.index')
            ->with('success', 'Your post has been added successfully!');
    }

    public function history()
    {
        $posts = Post::with(['user.userInfo', 'comments'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('posts.history', compact('posts'));
    }

    public function show($id)
    {
        $post = Post::with(['comments' => function ($query) {
            $query->latest(); // same as ->orderBy('created_at', 'desc')
        }, 'comments.user', 'user'])->findOrFail($id);

        return view('posts.show', compact('post'));
    }


    public function destroy(Post $post)
    {
        // Owner check: Ensure only owner can delete (or an admin via separate route/check)
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        // Cleanup: If the post is deleted, its reports and comments must also be deleted.
        Report::where('post_id', $post->id)->delete();
        Comment::where('post_id', $post->id)->delete();

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Your post has been deleted successfully.');
    }
}       