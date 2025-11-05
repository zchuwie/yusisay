<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user.userInfo', 'comments.user'])
            ->where('is_hidden', false)
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
        $post = Post::with(['comments.user.userInfo', 'user.userInfo'])->findOrFail($id);

        return view('posts.show', [
            'post' => $post,
            'postId' => $post->id,
        ]);
    }

    public function destroy(Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403, 'Unauthorized');
        }

        $post->delete();

        return redirect()->back()->with('success', 'Post deleted successfully.');
    }
}
