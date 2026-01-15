<?php

namespace App\Policies;

use App\Models\Answer;
use App\Models\User;

class AnswerPolicy
{
    public function update(User $user, Answer $answer): bool
    {
        return $user->id === $answer->user_id || $user->is_admin;
    }

    public function delete(User $user, Answer $answer): bool
    {
        return $user->id === $answer->user_id || $user->is_admin;
    }
}