<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    /**
     * Determine whether the user can update the question.
     */
    public function update(User $user, Question $question): bool
    {
        // Users can edit their own questions, Admins can edit anything
        return $user->id === $question->user_id || $user->is_admin;
    }

    /**
     * Determine whether the user can delete the question.
     */
    public function delete(User $user, Question $question): bool
    {
        return $user->id === $question->user_id || $user->is_admin;
    }
}