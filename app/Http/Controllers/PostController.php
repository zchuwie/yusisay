<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Report;
use App\Models\Comment;
use App\Models\CensoredWord;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
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
                    'content' => 'Your post contains inappropriate content: "' . $censorCheck['word'] . '". Please remove it and try again.'
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
 
    private function containsCensoredWord($content)
    { 
        $censoredWords = CensoredWord::pluck('word')->toArray();
 
        $normalizedContent = strtolower(trim($content));

        foreach ($censoredWords as $censoredWord) { 
            $normalizedCensoredWord = strtolower(trim($censoredWord));
 
            if (empty($normalizedCensoredWord)) {
                continue;
            }
 
            $pattern = $this->createFlexiblePattern($normalizedCensoredWord);
 
            if (preg_match($pattern, $normalizedContent)) {
                return [
                    'found' => true,
                    'word' => $censoredWord
                ];
            }
        }

        return ['found' => false, 'word' => null];
    }
 
    private function createFlexiblePattern($word)
    { 
        $chars = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);
 
        $patternParts = [];
        foreach ($chars as $char) { 
            $escapedChar = preg_quote($char, '/'); 
            $patternParts[] = $escapedChar . '+';
        }
 
        $pattern = '/\b' . implode('', $patternParts) . '\b/u';

        return $pattern;
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
