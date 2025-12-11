<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserProfileController extends Controller
{
    public function show($id)
    {
        $user = User::with(['answers.question.answers.ratings'])->findOrFail($id);

        // --- STATS LOGIC ---
        $solvedCount = \App\Models\Question::whereIn('best_answer_id', $user->answers->pluck('id'))->count();
        
        $topRatedCount = 0;
        foreach ($user->answers as $userAnswer) {
            $myScore = $userAnswer->ratings->avg('score') ?? 0;
            if ($myScore > 0) {
                $isHighest = true;
                if ($userAnswer->question) {
                    foreach ($userAnswer->question->answers as $otherAnswer) {
                        if ($otherAnswer->id === $userAnswer->id) continue;
                        if (($otherAnswer->ratings->avg('score') ?? 0) > $myScore) {
                            $isHighest = false; break;
                        }
                    }
                }
                if ($isHighest) $topRatedCount++;
            }
        }

        // --- PAGINATION ---
        
        // 1. Questions: Load 'category' AND 'images' for the full card design
        $questions_list = $user->questions()
                               ->with(['category', 'images']) // Added 'images' here
                               ->latest()
                               ->paginate(5, ['*'], 'questions_page');

        // 2. Answers
        $answers_list = $user->answers()
                             ->with(['question.answers.ratings', 'ratings'])
                             ->latest()
                             ->paginate(5, ['*'], 'answers_page');

        return view('profile.show', compact('user', 'solvedCount', 'topRatedCount', 'questions_list', 'answers_list'));
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return redirect()->route('user.profile', $user->id)->with('success', 'Profile picture updated!');
    }
}