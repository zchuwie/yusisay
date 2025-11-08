<?php

namespace App\Traits;

use App\Models\CensoredWord;

trait ChecksCensoredWords
{ 
    private function containsCensoredWord($content)
    {
        $censoredWords = CensoredWord::pluck('word')->toArray();
        $normalizedContent = $this->normalizeText($content);
        
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
 
    private function normalizeText($text)
    {
        $text = strtolower(trim($text));
         
        $substitutions = [
            '0' => 'o',
            '1' => 'i',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '7' => 't',
            '8' => 'b',
            '@' => 'a',
            '$' => 's',
            '!' => 'i',
            '+' => 't',
            '(' => 'c',
            '|' => 'i',
            '¡' => 'i',
        ];
        
        return str_replace(array_keys($substitutions), array_values($substitutions), $text);
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
}