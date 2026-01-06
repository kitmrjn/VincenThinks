<?php

namespace App\Services;

use App\Models\BannedWord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ContentFilter
{
    /**
     * Check if the text contains any banned words from the database.
     *
     * @param string $text
     * @return bool True if flagged, False if safe.
     */
    public static function check($text): bool
    {
        if (empty(trim($text))) {
            return false;
        }

        // Cache the list of banned words for 60 minutes to reduce DB queries
        $bannedWords = Cache::remember('banned_words_list', 3600, function () {
            return BannedWord::pluck('word')->toArray();
        });

        $normalizedText = strtolower($text);

        foreach ($bannedWords as $word) {
            // Check if the banned word is contained in the text (case-insensitive)
            if (str_contains($normalizedText, strtolower($word))) {
                Log::info("ContentFilter: Flagged content containing word '{$word}'");
                return true; // Flagged
            }
        }

        return false; // Safe
    }
}