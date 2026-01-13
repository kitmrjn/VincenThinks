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
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewActivity;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\StoreAnswerRequest;
use App\Http\Requests\StoreReplyRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Http\Requests\UpdateAnswerRequest;
use App\Http\Requests\UpdateReplyRequest;

class ForumController extends Controller
{
    private function getEditLimit() {
        return (int) (Setting::where('key', 'edit_time_limit')->value('value') ?? 150);
    }

    public function index(Request $request) {
        $categories = Category::all();
        
        $query = Question::with(['user.course', 'user.departmentInfo', 'category', 'images'])
                         ->withCount('answers')
                         ->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'solved': $query->whereNotNull('best_answer_id'); break;
                case 'unsolved': $query->whereNull('best_answer_id'); break;
                case 'no_answers': $query->doesntHave('answers'); break;
            }
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        $questions = $query->paginate(10)->onEachSide(1);

        if ($request->ajax()) {
            return view('partials.question-list', compact('questions'))->render();
        }
        
        // Otherwise, load the full page (for first visit)
        return view('feed', compact('questions', 'categories'));
    }

    public function storeQuestion(StoreQuestionRequest $request) {
        
        // 1. Verification Check (Keep this here for now since your authorize() returns true)
        $verificationRequired = Setting::where('key', 'verification_required')->value('value') == '1';
        if ($verificationRequired && !Auth::user()->hasVerifiedEmail()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Email verification required.'], 403);
            }
            return redirect()->back()->with('error', 'Action blocked: You must verify your email address to post.');
        }

        $question = Question::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id
        ]);

        // 4. Handle Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('question_images', 'public');
                QuestionImage::create(['question_id' => $question->id, 'image_path' => $path]);
            }
        }

        // 5. AJAX RESPONSE
        if ($request->wantsJson()) {
            $question->load('user.course', 'user.departmentInfo', 'category', 'images');
            $html = view('partials.question-card', ['q' => $question])->render();
            
            return response()->json([
                'success' => true,
                'message' => 'Question Posted!',
                'html' => $html
            ]);
        }

        return redirect()->back()->with('success', 'Question Posted!');
    }

    public function show($id) {
        $question = Question::with([
            'user.course', 
            'user.departmentInfo',
            'images',
            'answers.user.course', 
            'answers.user.departmentInfo',
            'answers.ratings', 
            'answers.replies.user.course', 
            'answers.replies.user.departmentInfo',
            'answers.replies.children.user.course',
            'answers.replies.children.user.departmentInfo'
        ])->findOrFail($id);

        $sessionKey = 'viewed_question_' . $id;
        if (!session()->has($sessionKey)) {
            $question->timestamps = false;
            $question->increment('views');
            $question->timestamps = true;
            session()->put($sessionKey, true);
        }

        $sortedAnswers = $question->answers->sortByDesc(function($answer) use ($question) {
            $isBest = $answer->id === $question->best_answer_id ? 1000000 : 0;
            $rating = $answer->ratings->avg('score') ?? 0;
            return $isBest + $rating;
        });

        $question->setRelation('answers', $sortedAnswers);

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

    public function storeAnswer(StoreAnswerRequest $request, $id) {
        
        // 1. Verification Check (Kept in controller)
        $verificationRequired = Setting::where('key', 'verification_required')->value('value') == '1';
        if ($verificationRequired && !Auth::user()->hasVerifiedEmail()) {
            return redirect()->back()->with('error', 'Action blocked: You must verify your email address to post.');
        }

        $question = Question::findOrFail($id);

        // 2. Business Logic Checks (Kept in controller)
        if (Auth::id() === $question->user_id) {
            return redirect()->back()->with('error', 'You cannot answer your own question.');
        }

        if ($question->best_answer_id) {
            return redirect()->back()->with('error', 'This question is solved.');
        }

        // 3. Create Answer
        Answer::create([
            'user_id' => Auth::id(),
            'question_id' => $id,
            'content' => $request->content
        ]);

        // 4. Notifications
        if ($question->user_id !== Auth::id()) {
            $question->user->notify(new NewActivity(
                Auth::user()->name . " answered your question.",
                route('question.show', $question->id),
                'answer'
            ));
        }

        return redirect()->back()->with('success', 'Answer posted!');
    }

    public function storeReply(StoreReplyRequest $request, $answerId) {
        
        // 1. Verification Check
        $verificationRequired = Setting::where('key', 'verification_required')->value('value') == '1';
        if ($verificationRequired && !Auth::user()->hasVerifiedEmail()) {
            return redirect()->back()->with('error', 'Action blocked: You must verify your email address to reply.');
        }

        // 2. Create Reply
        $reply = Reply::create([
            'user_id' => Auth::id(),
            'answer_id' => $answerId,
            'content' => $request->content,
            'parent_id' => $request->parent_id
        ]);

        // 3. Notifications
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

    public function destroyQuestion($id) {
        // CLEANUP: We now eager load images to delete them from storage too
        $question = Question::with('images')->findOrFail($id);
        
        if (Auth::id() !== $question->user_id && !Auth::user()->is_admin) { 
            abort(403); 
        }

        // 1. Delete actual files from disk
        foreach ($question->images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        }

        // 2. Delete the record (Database cascades will handle answers/replies)
        $question->delete();

        return redirect()->route('home')->with('success', 'Question deleted.');
    }

    public function destroyAnswer($id) {
        $answer = Answer::findOrFail($id);
        if (Auth::id() !== $answer->user_id && !Auth::user()->is_admin) { abort(403); }
        $answer->delete();
        return redirect()->back()->with('success', 'Answer deleted.');
    }

    public function markNotification(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect($notification->data['url'] ?? route('home'));
    }

    public function editQuestion($id)
    {
        $question = Question::with('images')->findOrFail($id);
        if (Auth::id() !== $question->user_id && !Auth::user()->is_admin) { abort(403); }

        $limit = $this->getEditLimit();
        if ($question->created_at < now()->subSeconds($limit) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', "Time limit exceeded. You can only edit within $limit seconds.");
        }

        $categories = Category::all(); 
        return view('edit_question', compact('question', 'categories'));
    }

    public function updateQuestion(UpdateQuestionRequest $request, $id) {
        $question = Question::findOrFail($id);
        
        // 1. Authorization & Time Limits (Kept in controller)
        if (Auth::id() !== $question->user_id && !Auth::user()->is_admin) { abort(403); }

        $limit = $this->getEditLimit();
        if ($question->created_at < now()->subSeconds($limit) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', "Time limit exceeded. You can only edit within $limit seconds.");
        }

        // 2. Update Question
        $question->update([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id
        ]);

        // 3. Handle Image Deletions
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = QuestionImage::find($imageId);
                if ($image && $image->question_id == $question->id) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        // 4. Handle New Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('question_images', 'public');
                $question->images()->create(['image_path' => $path]);
            }
        }

        return redirect()->route('question.show', $id)->with('success', 'Question updated.');
    }

    public function editAnswer($id) {
        $answer = Answer::findOrFail($id);
        if (Auth::id() !== $answer->user_id && !Auth::user()->is_admin) { abort(403); }
        
        $limit = $this->getEditLimit();
        if ($answer->created_at < now()->subSeconds($limit) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', "Time limit exceeded. You can only edit within $limit seconds.");
        }
        return view('edit_answer', compact('answer'));
    }

    public function updateAnswer(UpdateAnswerRequest $request, $id) {

        $answer = Answer::findOrFail($id);
        
        // 1. Authorization & Time Limits
        if (Auth::id() !== $answer->user_id && !Auth::user()->is_admin) { abort(403); }
        
        $limit = $this->getEditLimit();
        if ($answer->created_at < now()->subSeconds($limit) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', "Time limit exceeded. You can only edit within $limit seconds.");
        }

        // 2. Update
        $answer->update(['content' => $request->content]);
        
        return redirect()->route('question.show', $answer->question_id)->with('success', 'Answer updated.');
    }

    public function editReply($id) {
        $reply = Reply::findOrFail($id);
        if (Auth::id() !== $reply->user_id && !Auth::user()->is_admin) { abort(403); }
        
        $limit = $this->getEditLimit();
        if ($reply->created_at < now()->subSeconds($limit) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', "Time limit exceeded. You can only edit within $limit seconds.");
        }
        return view('edit_reply', compact('reply'));
    }

    public function updateReply(UpdateReplyRequest $request, $id) {
        $reply = Reply::findOrFail($id);
        
        if (Auth::id() !== $reply->user_id && !Auth::user()->is_admin) { abort(403); }
        
        $limit = $this->getEditLimit();
        if ($reply->created_at < now()->subSeconds($limit) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', "Time limit exceeded. You can only edit within $limit seconds.");
        }

        $reply->update(['content' => $request->content]);
        return redirect()->route('question.show', $reply->answer->question_id)->with('success', 'Reply updated.');
    }

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
        
        $question->timestamps = false;
        $question->save();
        
        return redirect()->back()->with('success', 'Best answer updated!');
    }

    public function destroyReply($id) {
        $reply = Reply::findOrFail($id);
        if (Auth::id() !== $reply->user_id && !Auth::user()->is_admin) { abort(403); }
        $reply->delete();
        return redirect()->back()->with('success', 'Reply deleted.');
    }
}