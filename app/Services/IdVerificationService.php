<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IdVerificationService
{
    public static function verifyDocument(string $imagePath, string $inputName, string $inputIdNumber): array
    {
        $apiKey = env('GEMINI_API_KEY');
        
        if (!$apiKey) {
            Log::error('IdVerificationService: GEMINI_API_KEY is missing.');
            return ['success' => false, 'message' => 'System configuration error.'];
        }

        try {
            $imageData = base64_encode(file_get_contents($imagePath));
            $mimeType = mime_content_type($imagePath);

            $currentYear = date('Y');
            $previousYear = $currentYear - 1;
            $nextYear = $currentYear + 1;

            $prompt = <<<EOT
You are an strict identity verification system for an academic institution.
Task: Check if the provided Name and ID Number appear in the attached image of a school ID or registration form.

Input Name: "{$inputName}"
Input ID Number: "{$inputIdNumber}"
Current Year: {$currentYear}

Rules:
1. Name Match: Account for minor typos or formatting differences (e.g., "John Doe" vs "Doe, John").
2. ID Match: The ID number must be an exact or very close match.
3. Year Validity Check: Look for a printed "S.Y.", "School Year", "Semester", or Expiration Date on the document. It must indicate the document is valid for the current academic timeframe (containing the years {$previousYear}, {$currentYear}, or {$nextYear}. Example: "S.Y. {$previousYear}-{$currentYear}"). If the document is explicitly expired or only shows years older than {$previousYear}, it is invalid. (Note: If it is a permanent ID card with NO printed school year or expiration date anywhere on it, ignore this rule).
4. Return ONLY a valid JSON object. Do not include markdown formatting.

Output Format:
{
    "verified": true or false,
    "confidence": "high" or "low",
    "reason": "short explanation"
}
EOT;

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout(20)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [[
                        'parts' => [
                            ['text' => $prompt],
                            ['inline_data' => ['mime_type' => $mimeType, 'data' => $imageData]]
                        ]
                    ]],
                    'safetySettings' => [
                        ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.0,
                        'responseMimeType' => 'application/json',
                    ]
                ]);

            if ($response->failed()) {
                Log::error('ID Verification AI HTTP Error: ' . $response->body());
                return ['success' => false, 'message' => 'AI Service connection failed.'];
            }

            $aiText = $response->json('candidates.0.content.parts.0.text');
            
            if (!$aiText) {
                Log::error('ID Verification AI Error: No text returned.');
                return ['success' => false, 'message' => 'Image was flagged by safety filters.'];
            }

            $cleanJson = preg_replace('/```json/i', '', $aiText);
            $cleanJson = preg_replace('/```/', '', $cleanJson);
            $cleanJson = trim($cleanJson);

            $result = json_decode($cleanJson, true);

            Log::info('ID Verification AI Output: ' . $cleanJson);

            return [
                'success' => $result['verified'] ?? false,
                'message' => $result['reason'] ?? 'Could not read the document. Please provide a clearer photo.'
            ];

        } catch (\Exception $e) {
            Log::error("IdVerificationService Exception: " . $e->getMessage());
            return ['success' => false, 'message' => 'Internal server error during verification.'];
        }
    }
}