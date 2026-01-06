<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Department; // <--- ADD THIS
use Illuminate\Support\Str;

class UserProfileController extends Controller
{
    public function show($id)
    {
        // Eager load relationships
        $user = User::with(['course', 'answers.question.answers.ratings'])->findOrFail($id);

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
        $questions_list = $user->questions()
                               ->with(['category', 'images'])
                               ->latest()
                               ->paginate(5, ['*'], 'questions_page');

        $answers_list = $user->answers()
                             ->with(['question.answers.ratings', 'ratings'])
                             ->latest()
                             ->paginate(5, ['*'], 'answers_page');

        // --- NEW: Fetch Departments for the Settings Tab ---
        $departments = Department::orderBy('name')->get();

        return view('profile.show', compact('user', 'solvedCount', 'topRatedCount', 'questions_list', 'answers_list', 'departments'));
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