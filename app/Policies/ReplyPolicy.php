<?php

namespace App\Policies;

use App\Models\Reply;
use App\Models\User;

class ReplyPolicy
{
    public function update(User $user, Reply $reply): bool
    {
        return $user->id === $reply->user_id || $user->is_admin;
    }

    public function delete(User $user, Reply $reply): bool
    {
        return $user->id === $reply->user_id || $user->is_admin;
    }
}