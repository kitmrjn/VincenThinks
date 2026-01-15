<?php

namespace App\Services;

use App\Models\BannedWord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentFilter
{
    /**
     * Check content against local DB first, then AI.
     *
     * @param string $text
     * @return bool True if flagged (unsafe), False if safe.
     */
    public static function check($text): bool
    {
        // 0. Empty check
        if (empty(trim($text ?? ''))) {
            return false;
        }

        // 1. FIRST LINE OF DEFENSE: Local Database (Instant)
        $bannedWords = Cache::remember('banned_words_list', 3600, function () {
            return BannedWord::pluck('word')->toArray();
        });

        $normalizedText = strtolower($text);
        foreach ($bannedWords as $word) {
            if (str_contains($normalizedText, strtolower($word))) {
                Log::info("ContentFilter: Locally flagged word '{$word}'");
                return true; 
            }
        }

        // 2. SECOND LINE OF DEFENSE: Gemini AI
        // [UPDATED] Check the Database Setting instead of .env
        $useAi = \App\Models\Setting::where('key', 'use_ai_moderation')->value('value');
        
        // If strictly '1', we run the AI. 
        if ($useAi === '1') {
            return self::checkWithGemini($text);
        }

        return false;
    }

    private static function checkWithGemini(string $text): bool
    {
        $apiKey = env('GEMINI_API_KEY');

        // Fail Secure: If no API key, assume unsafe or log error (here we default to flagging to be safe)
        if (!$apiKey) {
            Log::error('ContentFilter: GEMINI_API_KEY is missing.');
            return true; 
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->timeout(5) // Wait up to 5 seconds
            ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => self::buildPrompt($text)]
                        ]
                    ]
                ],
                'safetySettings' => [
                    ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
                ],
                'generationConfig' => [
                    'temperature' => 0.0,
                    // CRITICAL FIX: Increased from 15 to 100. 
                    // This gives Gemini space to "think" before writing "FLAG".
                    'maxOutputTokens' => 100, 
                ]
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error: ' . $response->body());
                return true; // Fail Secure
            }

            $json = $response->json();
            
            // Log raw response for debugging
            Log::info("Gemini Raw JSON:", $json);

            // CHECK 1: Input Blocked?
            if (isset($json['promptFeedback']['blockReason'])) {
                Log::info("Gemini Moderation: Input blocked due to " . $json['promptFeedback']['blockReason']);
                return true; 
            }

            // CHECK 2: Output Blocked by Safety Filter?
            $finishReason = $json['candidates'][0]['finishReason'] ?? 'UNKNOWN';
            if ($finishReason === 'SAFETY' || $finishReason === 'OTHER') {
                Log::info("Gemini Moderation: Output blocked. FinishReason: {$finishReason}");
                return true;
            }

            // CHECK 3: Empty Response (The "Silence is Danger" Fix)
            $aiText = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $cleanResponse = strtoupper(trim($aiText));

            if ($cleanResponse === '') {
                Log::warning("Gemini Moderation: AI returned empty string. Treating as UNSAFE (Fail-Safe).");
                return true; 
            }

            Log::info("Gemini Moderation Result: '{$cleanResponse}'");

            // CHECK 4: Keyword Matching
            // We check for "FLAG", "UNSAFE", or "YES" (some models say "YES" to "Is this bad?")
            $bad_signals = ['FLAG', 'UNSAFE', 'HATE', 'HARASSMENT', 'PROFANITY', 'YES', 'BLOCK'];
            
            foreach ($bad_signals as $signal) {
                if (str_contains($cleanResponse, $signal)) {
                    return true;
                }
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Gemini Connection Exception: " . $e->getMessage());
            return true; // Fail Secure: If internet breaks, block the post.
        }
    }

    private static function buildPrompt(string $text): string
    {
        return <<<EOT
You are a content moderator. 
Task: Analyze the following text for hate speech, harassment, extreme profanity, or bullying.

Text: "{$text}"

If the text is unsafe, reply "FLAG".
If the text is safe, reply "SAFE".
Answer:
EOT;
    }
}