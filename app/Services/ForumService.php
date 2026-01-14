<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Answer;
use App\Models\Category;
use App\Models\Rating;
use App\Notifications\NewActivity;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ForumService
{
    /**
     * Handle complex filtering for the main feed.
     */
    public function getFilteredFeed(array $filters): LengthAwarePaginator
    {
        $query = Question::with(['user.course', 'user.departmentInfo', 'category', 'images'])
                         ->withCount('answers')
                         ->latest();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['filter'])) {
            switch ($filters['filter']) {
                case 'solved': $query->whereNotNull('best_answer_id'); break;
                case 'unsolved': $query->whereNull('best_answer_id'); break;
                case 'no_answers': $query->doesntHave('answers'); break;
            }
        }

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        return $query->paginate(10)->onEachSide(1);
    }

    /**
     * Sort answers: Best Answer first, then by Rating score.
     */
    public function getSortedAnswers(Question $question): Collection
    {
        return $question->answers->sortByDesc(function($answer) use ($question) {
            $isBest = $answer->id === $question->best_answer_id ? 1000000 : 0;
            $rating = $answer->ratings->avg('score') ?? 0;
            return $isBest + $rating;
        });
    }

    /**
     * Identify the ID of the highest-rated answer for highlighting.
     */
    public function getTopRatedAnswerId(Question $question): ?int
    {
        $topRatedAnswerId = null;
        $highestScore = 0;

        foreach ($question->answers as $answer) {
            $avgScore = $answer->ratings->avg('score');
            if ($avgScore > $highestScore) {
                $highestScore = $avgScore;
                $topRatedAnswerId = $answer->id;
            }
        }
        
        return $topRatedAnswerId;
    }

    /**
     * Handle rating logic and notification triggering.
     */
    public function processRating(Answer $answer, int $score, $user): void
    {
        Rating::updateOrCreate(
            ['user_id' => $user->id, 'answer_id' => $answer->id],
            ['score' => $score]
        );

        // Calculate if this is now the top answer
        $answer->load('ratings', 'question.answers.ratings');
        $myScore = $answer->ratings->avg('score');
        $question = $answer->question;
        
        $isHighest = true;
        foreach($question->answers as $other) {
            if($other->id !== $answer->id && $other->ratings->avg('score') >= $myScore) {
                $isHighest = false; break;
            }
        }

        // Notify if it's high quality (4+) and the highest
        if ($isHighest && $myScore >= 4 && $answer->user_id !== $user->id) {
            $answer->user->notify(new NewActivity(
                'Your answer is now the Top Rated solution!',
                route('question.show', $question->id),
                'top_rated'
            ));
        }
    }
}