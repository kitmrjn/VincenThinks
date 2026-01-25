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
            $this->model->update(['status' => 'pending_review']);
            
            // [FIX] Remove the original "New Content" event to prevent duplicates on dashboard
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

            // [NEW] Log AI Flag for Real-Time Dashboard
            AnalyticsEvent::create([
                'type' => 'ai_flagged',
                'message' => 'AI flagged content as UNSAFE.',
                'meta_data' => [
                    'model' => class_basename($this->model),
                    'id' => $this->model->id
                ]
            ]);
        } else {
            $this->model->update(['status' => 'published']);
        }
    }
}