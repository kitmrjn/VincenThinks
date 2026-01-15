<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Category;
use App\Models\Reply;
use App\Models\Report;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewActivity;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\StoreAnswerRequest;
use App\Http\Requests\StoreReplyRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Http\Requests\UpdateAnswerRequest;
use App\Http\Requests\UpdateReplyRequest;

// Services
use App\Services\ForumService;
use App\Services\ImageService;

class ForumController extends Controller
{
    protected $forumService;
    protected $imageService;

    public function __construct(ForumService $forumService, ImageService $imageService)
    {
        $this->forumService = $forumService;
        $this->imageService = $imageService;
    }

    private function getEditLimit() {
        return (int) (Setting::where('key', 'edit_time_limit')->value('value') ?? 150);
    }

    public function index(Request $request) {
        $questions = $this->forumService->getFilteredFeed($request->all());
        $categories = Category::all();

        if ($request->ajax()) {
            return view('partials.question-list', compact('questions'))->render();
        }
        
        return view('feed', compact('questions', 'categories'));
    }

    public function storeQuestion(StoreQuestionRequest $request) {
        
        $question = Question::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id
        ]);

        if ($request->hasFile('images')) {
            $this->imageService->attachQuestionImages($question, $request->file('images'));
        }

        // [UPDATED] Removed the immediate "pending_review" error check.
        // We now treat the submission as successful regardless of the initial status.
        // The AI job runs in the background.

        if ($request->wantsJson()) {
            $question->load('user.course', 'user.departmentInfo', 'category', 'images');
            $html = view('partials.question-card', ['q' => $question])->render();
            
            return response()->json([
                'success' => true,
                'message' => 'Question submitted! It will appear shortly.',
                'html' => $html
            ]);
        }

        return redirect()->back()->with('success', 'Question submitted! It will appear shortly.');
    }

    public function show($id) {
        // [FIX] Use 'withoutGlobalScope' so we can find the question even if it is Pending
        $question = Question::withoutGlobalScope('published')
            ->with([
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

        // [SECURITY] If it is hidden, ONLY allow the Owner or Admin to see it
        if ($question->status !== 'published') {
            if (!Auth::check() || (Auth::id() !== $question->user_id && !Auth::user()->is_admin)) {
                abort(404); // Fake a 404 for everyone else
            }
        }

        // [Logic] View counting logic
        $sessionKey = 'viewed_question_' . $id;
        if (!session()->has($sessionKey)) {
            $question->timestamps = false;
            $question->increment('views');
            $question->timestamps = true;
            session()->put($sessionKey, true);
        }

        $sortedAnswers = $this->forumService->getSortedAnswers($question);
        $topRatedAnswerId = $this->forumService->getTopRatedAnswerId($question);

        $question->setRelation('answers', $sortedAnswers);

        return view('show_question', compact('question', 'topRatedAnswerId'));
    }

    public function storeAnswer(StoreAnswerRequest $request, $id) {
        $question = Question::findOrFail($id);

        if (Auth::id() === $question->user_id) {
            return redirect()->back()->with('error', 'You cannot answer your own question.');
        }

        if ($question->best_answer_id) {
            return redirect()->back()->with('error', 'This question is solved.');
        }

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

    public function storeReply(StoreReplyRequest $request, $answerId) {
        $reply = Reply::create([
            'user_id' => Auth::id(),
            'answer_id' => $answerId,
            'content' => $request->content,
            'parent_id' => $request->parent_id
        ]);

        // [UPDATED] Removed the immediate "pending_review" error check here as well.

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

        $this->forumService->processRating($answer, $request->score, Auth::user());

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
        $question = Question::with('images')->findOrFail($id);
        
        // [Security] Use Policy
        $this->authorize('delete', $question);

        $this->imageService->deleteAllForQuestion($question);
        $question->delete();

        return redirect()->route('home')->with('success', 'Question deleted.');
    }

    public function destroyAnswer($id) {
        $answer = Answer::findOrFail($id);
        
        // [Security] Use Policy
        $this->authorize('delete', $answer);
        
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
        
        // [Security] Use Policy
        $this->authorize('update', $question);

        $limit = $this->getEditLimit();
        if ($question->created_at < now()->subSeconds($limit) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', "Time limit exceeded. You can only edit within $limit seconds.");
        }

        $categories = Category::all(); 
        return view('edit_question', compact('question', 'categories'));
    }

    public function updateQuestion(UpdateQuestionRequest $request, $id) {
        $question = Question::findOrFail($id);
        
        // [Security] Use Policy
        $this->authorize('update', $question);

        $limit = $this->getEditLimit();
        if ($question->created_at < now()->subSeconds($limit) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', "Time limit exceeded. You can only edit within $limit seconds.");
        }

        $question->update([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id
        ]);

        if ($request->has('delete_images')) {
            $this->imageService->deleteImages($request->delete_images, $question->id);
        }

        if ($request->hasFile('images')) {
            $this->imageService->attachQuestionImages($question, $request->file('images'));
        }

        return redirect()->route('question.show', $id)->with('success', 'Question updated.');
    }

    public function editAnswer($id) {
        $answer = Answer::findOrFail($id);
        
        // [Security] Use Policy
        $this->authorize('update', $answer);
        
        $limit = $this->getEditLimit();
        if ($answer->created_at < now()->subSeconds($limit) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', "Time limit exceeded. You can only edit within $limit seconds.");
        }
        return view('edit_answer', compact('answer'));
    }

    public function updateAnswer(UpdateAnswerRequest $request, $id) {
        $answer = Answer::findOrFail($id);
        
        // [Security] Use Policy
        $this->authorize('update', $answer);
        
        $limit = $this->getEditLimit();
        if ($answer->created_at < now()->subSeconds($limit) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', "Time limit exceeded. You can only edit within $limit seconds.");
        }

        $answer->update(['content' => $request->content]);
        
        return redirect()->route('question.show', $answer->question_id)->with('success', 'Answer updated.');
    }

    public function editReply($id) {
        $reply = Reply::findOrFail($id);
        
        // [Security] Use Policy
        $this->authorize('update', $reply);
        
        $limit = $this->getEditLimit();
        if ($reply->created_at < now()->subSeconds($limit) && !Auth::user()->is_admin) {
            return redirect()->back()->with('error', "Time limit exceeded. You can only edit within $limit seconds.");
        }
        return view('edit_reply', compact('reply'));
    }

    public function updateReply(UpdateReplyRequest $request, $id) {
        $reply = Reply::findOrFail($id);
        
        // [Security] Use Policy
        $this->authorize('update', $reply);
        
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

        // "Mark as Best" is unique: Only the Question Owner can do it.
        // We can use a Policy here too if we add a 'markAsBest' method to QuestionPolicy,
        // but for now, checking ownership directly or using Gate is standard.
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
        
        // [Security] Use Policy
        $this->authorize('delete', $reply);
        
        $reply->delete();
        return redirect()->back()->with('success', 'Reply deleted.');
    }
}