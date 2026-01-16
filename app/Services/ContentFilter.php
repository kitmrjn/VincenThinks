<?php

namespace App\Services;

use App\Models\BannedWord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentFilter
{
    /**
     * Check content safety.
     * @param string $text
     * @param array $imagePaths Array of full system file paths to images.
     */
    public static function check($text, array $imagePaths = []): bool
    {
        // 1. Text Empty Check (Skip only if NO images either)
        if (empty(trim($text ?? '')) && empty($imagePaths)) {
            return false;
        }

        // 2. FIRST LINE: Local Text Check
        // If text is explicitly bad, we fail immediately (saves API tokens)
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

        // 3. SECOND LINE: Gemini AI (Text + Images)
        $useAi = \App\Models\Setting::where('key', 'use_ai_moderation')->value('value');
        
        if ($useAi === '1') {
            return self::checkWithGemini($text, $imagePaths);
        }

        return false;
    }

    private static function checkWithGemini(string $text, array $imagePaths): bool
    {
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            Log::error('ContentFilter: GEMINI_API_KEY is missing.');
            return true; 
        }

        // --- BUILD THE PAYLOAD ---
        $parts = [];

        // Add Text Part
        if (!empty($text)) {
            $parts[] = ['text' => self::buildPrompt($text ?: '[No text content]')];
        }

        // Add Image Parts
        foreach ($imagePaths as $path) {
            try {
                $imageData = base64_encode(file_get_contents($path));
                $mimeType = mime_content_type($path);
                
                $parts[] = [
                    'inline_data' => [
                        'mime_type' => $mimeType,
                        'data' => $imageData
                    ]
                ];
            } catch (\Exception $e) {
                Log::error("ContentFilter: Failed to process image at $path. Error: " . $e->getMessage());
                // Fail secure: if we can't check the image, assume it might be bad? 
                // Or continue? Let's continue but log it.
            }
        }

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->timeout(10) // Increase timeout for image processing
            ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [[ 'parts' => $parts ]],
                'safetySettings' => [
                    ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                    ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'], // Crucial for images
                    ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
                ],
                'generationConfig' => [
                    'temperature' => 0.0,
                    'maxOutputTokens' => 100, 
                ]
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error: ' . $response->body());
                return true; 
            }

            $json = $response->json();
            
            // Standard parsing logic (Same as before)
            if (isset($json['promptFeedback']['blockReason'])) return true;
            
            $finishReason = $json['candidates'][0]['finishReason'] ?? 'UNKNOWN';
            if ($finishReason === 'SAFETY' || $finishReason === 'OTHER') return true;

            $aiText = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $cleanResponse = strtoupper(trim($aiText));

            if ($cleanResponse === '') return true;

            $bad_signals = ['FLAG', 'UNSAFE', 'HATE', 'HARASSMENT', 'PROFANITY', 'YES', 'BLOCK'];
            foreach ($bad_signals as $signal) {
                if (str_contains($cleanResponse, $signal)) return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Gemini Connection Exception: " . $e->getMessage());
            return true; 
        }
    }

    private static function buildPrompt(string $text): string
    {
        // Updated prompt to explicitly mention images
        return <<<EOT
You are a content moderator. 
Task: Analyze the attached text AND images for hate speech, harassment, nudity, gore, or extreme violence.

Text Content: "{$text}"

If ANY part of the text or images is unsafe, reply "FLAG".
If everything is safe, reply "SAFE".
Answer:
EOT;
    }
}