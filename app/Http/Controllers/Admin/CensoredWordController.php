<?php

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller; 
use App\Models\CensoredWord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CensoredWordController extends Controller
{
    public function index()
    {
        $words = CensoredWord::orderBy('created_at', 'desc')->get();
            
        return response()->json($words);
    }

    public function store(Request $request)
    {
        $validation = $request->validate([
            'word' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('censored_words', 'word')->where(fn ($query) => $query->where('word', strtolower($request->word)))
            ],
        ]);
        
        try {
            $word = CensoredWord::create([
                'word' => strtolower($request->word),
            ]);

            return response()->json([
                'message' => "Word '{$request->word}' added successfully.", 
                'word' => $word
            ], 201);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Censored Word Storage Failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Internal Server Error during database operation. Check the details below.',
                'exception_message' => $e->getMessage(), 
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function destroy(CensoredWord $censored_word)
    {
        $censored_word->delete();

        return response()->json(['message' => 'Censored word deleted successfully.'], 200);
    }   
}