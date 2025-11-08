<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Report;
use App\Models\Comment;
use App\Traits\ChecksCensoredWords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    use ChecksCensoredWords;

    public function index()
    {
        $posts = Post::with(['user', 'comments.user'])
            ->where('is_hidden', false)
            ->latest()->get();
        return view('posts.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'is_anonymous' => 'boolean',
        ]);

        $censorCheck = $this->containsCensoredWord($validated['content']);

        if ($censorCheck['found']) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'content' => 'Your post contains inappropriate content. Please remove it and try again.'
                ]);
        }

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
            ->where('is_hidden', false)
            ->latest()
            ->get();

        return view('posts.history', compact('posts'));
    }

    public function show($id)
    {
        $post = Post::with(['comments' => function ($query) {
            $query->latest();
        }, 'comments.user', 'user'])->findOrFail($id);

        return view('posts.show', compact('post'));
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        Report::where('post_id', $post->id)->delete();
        Comment::where('post_id', $post->id)->delete();

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Your post has been deleted successfully.');
    }
}
