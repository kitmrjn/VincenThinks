<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Reply;
use App\Models\AnalyticsEvent;
use App\Services\ContentFilter;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewActivity; // Make sure this is imported!

class CheckContentSafety implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function handle(): void
    {
        $text = '';
        $imagePaths = [];

        // Prepare text and images based on model type
        if ($this->model instanceof Question) {
            $text = $this->model->title . "\n" . $this->model->content;
            $this->model->load('images');
            foreach ($this->model->images as $img) {
                if (Storage::disk('public')->exists($img->image_path)) {
                     $imagePaths[] = Storage::disk('public')->path($img->image_path);
                }
            }
        } else {
            // Answers and Replies
            $text = $this->model->content;
        }

        // Run AI Check
        $isUnsafe = ContentFilter::check($text, $imagePaths);

        if ($isUnsafe) {
            // Keep it hidden
            $this->model->update(['status' => 'pending_review']);
            
            // Remove the original "New Content" event
            if ($this->model instanceof Question) {
                AnalyticsEvent::where('type', 'new_question')
                    ->whereJsonContains('meta_data->question_id', $this->model->id)
                    ->delete();
            } elseif ($this->model instanceof Answer) {
                AnalyticsEvent::where('type', 'new_answer')
                    ->whereJsonContains('meta_data->answer_id', $this->model->id)
                    ->delete();
            } elseif ($this->model instanceof Reply) {
                 AnalyticsEvent::where('type', 'new_reply')
                    ->whereJsonContains('meta_data->reply_id', $this->model->id)
                    ->delete();
            }

            // Log AI Flag for Admin Dashboard
            AnalyticsEvent::create([
                'type' => 'ai_flagged',
                'message' => 'AI flagged content as UNSAFE.',
                'meta_data' => [
                    'model' => class_basename($this->model),
                    'id' => $this->model->id
                ]
            ]);

            // [NEW] PROACTIVE USER NOTIFICATION
            // Alert the user that their post was flagged.
            $contentType = strtolower(class_basename($this->model));
            
            // Figure out the correct URL to send them to
            $url = '';
            if ($this->model instanceof Question) {
                $url = route('question.show', $this->model->id);
            } elseif ($this->model instanceof Answer) {
                $url = route('question.show', $this->model->question_id);
            } elseif ($this->model instanceof Reply) {
                $url = route('question.show', $this->model->answer->question_id);
            }

            // Send the notification to the author
            $this->model->user->notify(new NewActivity(
                "Your recent {$contentType} was flagged by our automated safety system and is hidden pending manual admin review.",
                $url,
                'system_alert' // Gives it a distinct type in your notifications table
            ));

        } else {
            // Publish the content
            $this->model->update(['status' => 'published']);

            // Send notification if it's an Answer
            if ($this->model instanceof Answer) {
                $question = $this->model->question;
                if ($question->user_id !== $this->model->user_id) {
                    $question->user->notify(new NewActivity(
                        $this->model->user->name . " answered your question.",
                        route('question.show', $question->id),
                        'answer'
                    ));
                }
            }

            // Send notification if it's a Reply
            if ($this->model instanceof Reply) {
                $answer = $this->model->answer;
                $userToNotify = $this->model->parent_id ? Reply::find($this->model->parent_id)->user : $answer->user;

                if ($userToNotify && $userToNotify->id !== $this->model->user_id) {
                    $userToNotify->notify(new NewActivity(
                        $this->model->user->name . " replied to your comment.",
                        route('question.show', $answer->question_id),
                        'reply'
                    ));
                }
            }
        }
    }
}