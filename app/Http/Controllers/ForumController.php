<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Category;
use App\Models\Rating;
use App\Models\Reply;
use App\Models\Report;
use App\Models\QuestionImage;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewActivity;

class ForumController extends Controller
{
    // 1. HOME PAGE
    public function index(Request $request) {
        $categories = Category::all();
        $query = Question::with(['user', 'category', 'images', 'answers'])->latest();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $questions = $query->get();
        return view('welcome', compact('questions', 'categories'));
    }

    // 2. STORE QUESTION (FIXED: Checks for empty HTML)
    public function storeQuestion(Request $request) {
        $request->validate([
            'title' => 'required',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:3072',
            // CUSTOM RULE: Strip HTML tags to ensure it's not just empty space
            'content' => ['required', function ($attribute, $value, $fail) {
                if (trim(strip_tags($value)) === '') {
                    $fail('The question content cannot be empty.');
                }
            }],
        ]);

        $question = Question::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('question_images', 'public');
                QuestionImage::create([
                    'question_id' => $question->id,
                    'image_path' => $path
                ]);
            }
        }

        return redirect()->back()->with('success', 'Question Posted!');
    }

    // 3. SHOW QUESTION
    public function show($id) {
        $question = Question::with([
            'user', 
            'images',
            'answers.user', 
            'answers.ratings', 
            'answers.replies.user', 
            'answers.replies.children'
        ])->findOrFail($id);

        $sessionKey = 'viewed_question_' . $id;
        if (!session()->has($sessionKey)) {
            $question->increment('views');
            session()->put($sessionKey, true);
        }

        // Sort Answers
        $sortedAnswers = $question->answers->sortByDesc(function($answer) use ($question) {
            $isBest = $answer->id === $question->best_answer_id ? 1000000 : 0;
            $rating = $answer->ratings->avg('score') ?? 0;
            return $isBest + $rating;
        });

        $question->setRelation('answers', $sortedAnswers);

        // Find Top Rated
        $topRatedAnswerId = null;
        $highestScore = 0;
        foreach ($question->answers as $answer) {
            $avgScore = $answer->ratings->avg('score');
            if ($avgScore > $highestScore) {
                $highestScore = $avgScore;
                $topRatedAnswerId = $answer->id;
            }
        }

        return view('show_question', compact('question', 'topRatedAnswerId'));
    }
    
    // 4. STORE ANSWER (FIXED: Checks for empty HTML)
    public function storeAnswer(Request $request, $id) {
        $question = Question::findOrFail($id);

        if (Auth::id() === $question->user_id) {
            return redirect()->back()->with('error', 'You cannot answer your own question.');
        }

        if ($question->best_answer_id) {
            return redirect()->back()->with('error', 'This question is solved.');
        }

        $request->validate([
            'content' => ['required', function ($attribute, $value, $fail) {
                if (trim(strip_tags($value)) === '') {
                    $fail('The answer content cannot be empty.');
                }
            }]
        ]);
        
        Answer::create([
            'user_id' => Auth::id(),
            'question_id' => $id,
            'content' => $request->content
        ]);

        if ($question->user_id !== Auth::id()) {
            $question->user->notify(new NewActivity(
                Auth::user()->name . " answered your question.",
                route('question.show', $question->id),
                'answer'
            ));
        }

        return redirect()->back()->with('success', 'Answer posted!');
    }

    // 5. STORE REPLY (FIXED: Checks for empty HTML)
    public function storeReply(Request $request, $answerId) {
        $request->validate([
            'parent_id' => 'nullable|exists:replies,id',
            'content' => ['required', function ($attribute, $value, $fail) {
                if (trim(strip_tags($value)) === '') {
                    $fail('The reply content cannot be empty.');
                }
            }]
        ]);

        $reply = Reply::create([
            'user_id' => Auth::id(),
            'answer_id' => $answerId,
            'content' => $request->content,
            'parent_id' => $request->parent_id
        ]);

        $answer = Answer::findOrFail($answerId);
        $userToNotify = $request->parent_id ? Reply::find($request->parent_id)->user : $answer->user;

        if ($userToNotify && $userToNotify->id !== Auth::id()) {
            $userToNotify->notify(new NewActivity(
                Auth::user()->name . " replied to your comment.",
                route('question.show', $answer->question_id),
                'reply'
            ));
        }

        return redirect()->back()->with('success', 'Reply posted.');
    }

    // 6. RATE ANSWER
    public function rateAnswer(Request $request, $id) {
        $request->validate(['score' => 'required|integer|min:1|max:5']);
        $answer = Answer::findOrFail($id); 

        if (Auth::id() === $answer->user_id) {
            return redirect()->back()->with('error', 'You cannot rate your own answer.');
        }

        Rating::updateOrCreate(
            ['user_id' => Auth::id(), 'answer_id' => $id],
            ['score' => $request->score]
        );

        $answer->load('ratings', 'question.answers.ratings');
        $myScore = $answer->ratings->avg('score');
        $question = $answer->question;
        
        $isHighest = true;
        foreach($question->answers as $other) {
            if($other->id !== $answer->id && $other->ratings->avg('score') >= $myScore) {
                $isHighest = false; break;
            }
        }

        if ($isHighest && $myScore >= 4 && $answer->user_id !== Auth::id()) {
            $answer->user->notify(new NewActivity(
                'Your answer is now the Top Rated solution!',
                route('question.show', $question->id),
                'top_rated'
            ));
        }

        return redirect()->back()->with('message', 'Rating saved!');
    }

    // 7. REPORT QUESTION
    public function reportQuestion(Request $request, $id) {
        $request->validate([
            'reason' => 'required',
            'other_reason_details' => 'required_if:reason,Other|nullable|string|max:255',
        ]);

        $existingReport = Report::where('user_id', Auth::id())->where('question_id', $id)->first();

        if ($existingReport) {
            return redirect()->back()->with('error', 'You have already reported this question.');
        }

        $finalReason = $request->reason === 'Other' ? 'Other: ' . $request->other_reason_details : $request->reason;

        Report::create([
            'user_id' => Auth::id(),
            'question_id' => $id,
            'reason' => $finalReason
        ]);

        return redirect()->back()->with('message', 'Reported to admins.');
    }

    // 8. DELETE QUESTION
    public function destroyQuestion($id) {
        $question = Question::findOrFail($id);
        if (Auth::id() !== $question->user_id && !Auth::user()->is_admin) { abort(403); }
        $question->delete();
        return redirect()->route('home')->with('success', 'Question deleted.');
    }

    // 9. DELETE ANSWER
    public function destroyAnswer($id) {
        $answer = Answer::findOrFail($id);
        if (Auth::id() !== $answer->user_id && !Auth::user()->is_admin) { abort(403); }
        $answer->delete();
        return redirect()->back()->with('success', 'Answer deleted.');
    }

    // 10. MARK NOTIFICATION AS READ
    public function markNotification(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect($notification->data['url'] ?? route('home'));
    }

    // 11. EDIT & UPDATE QUESTION (FIXED: Checks for empty HTML)
    public function editQuestion($id) {
        $question = Question::findOrFail($id);
        if (Auth::id() !== $question->user_id && !Auth::user()->is_admin) { abort(403); }
        if ($question->created_at < now()->subSeconds(150) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', 'Time limit exceeded.');
        }
        return view('edit_question', compact('question'));
    }

    public function updateQuestion(Request $request, $id) {
        $request->validate([
            'title' => 'required',
            'content' => ['required', function ($attribute, $value, $fail) {
                if (trim(strip_tags($value)) === '') {
                    $fail('The question content cannot be empty.');
                }
            }]
        ]);
        
        $question = Question::findOrFail($id);
        if (Auth::id() !== $question->user_id && !Auth::user()->is_admin) { abort(403); }
        if ($question->created_at < now()->subSeconds(150) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', 'Time limit exceeded.');
        }
        $question->update(['title' => $request->title, 'content' => $request->content]);
        return redirect()->route('question.show', $id)->with('success', 'Question updated.');
    }

    // 12. EDIT & UPDATE ANSWER (FIXED: Checks for empty HTML)
    public function editAnswer($id) {
        $answer = Answer::findOrFail($id);
        if (Auth::id() !== $answer->user_id && !Auth::user()->is_admin) { abort(403); }
        if ($answer->created_at < now()->subSeconds(150) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', 'Time limit exceeded.');
        }
        return view('edit_answer', compact('answer'));
    }

    public function updateAnswer(Request $request, $id) {
        $request->validate([
            'content' => ['required', function ($attribute, $value, $fail) {
                if (trim(strip_tags($value)) === '') {
                    $fail('The content cannot be empty.');
                }
            }]
        ]);

        $answer = Answer::findOrFail($id);
        if (Auth::id() !== $answer->user_id && !Auth::user()->is_admin) { abort(403); }
        if ($answer->created_at < now()->subSeconds(150) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', 'Time limit exceeded.');
        }
        $answer->update(['content' => $request->content]);
        return redirect()->route('question.show', $answer->question_id)->with('success', 'Answer updated.');
    }

    // 13. EDIT & UPDATE REPLY (FIXED: Checks for empty HTML)
    public function editReply($id) {
        $reply = Reply::findOrFail($id);
        if (Auth::id() !== $reply->user_id && !Auth::user()->is_admin) { abort(403); }
        if ($reply->created_at < now()->subSeconds(150) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', 'Time limit exceeded.');
        }
        return view('edit_reply', compact('reply'));
    }

    public function updateReply(Request $request, $id) {
        $request->validate([
            'content' => ['required', function ($attribute, $value, $fail) {
                if (trim(strip_tags($value)) === '') {
                    $fail('The content cannot be empty.');
                }
            }]
        ]);

        $reply = Reply::findOrFail($id);
        if (Auth::id() !== $reply->user_id && !Auth::user()->is_admin) { abort(403); }
        if ($reply->created_at < now()->subSeconds(150) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', 'Time limit exceeded.');
        }
        $reply->update(['content' => $request->content]);
        return redirect()->route('question.show', $reply->answer->question_id)->with('success', 'Reply updated.');
    }

    // 14. MARK AS BEST ANSWER
    public function markAsBest($id) {
        $answer = Answer::findOrFail($id);
        $question = $answer->question;

        if (Auth::id() !== $question->user_id) { abort(403); }

        if ($question->best_answer_id === $answer->id) {
            $question->best_answer_id = null;
        } else {
            $question->best_answer_id = $answer->id;
            
            if ($answer->user_id !== Auth::id()) {
                $answer->user->notify(new NewActivity(
                    'Your answer to "' . substr($question->title, 0, 20) . '..." was marked as the Accepted Solution!',
                    route('question.show', $question->id),
                    'best_answer'
                ));
            }
        }
        
        $question->save();
        return redirect()->back()->with('success', 'Best answer updated!');
    }
}