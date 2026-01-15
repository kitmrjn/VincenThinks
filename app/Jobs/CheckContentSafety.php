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
use App\Services\ContentFilter;

class CheckContentSafety implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;

    /**
     * Create a new job instance.
     * Accepts any model that has 'title', 'content', and 'status'.
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Prepare text to check
        $text = '';
        if ($this->model instanceof Question) {
            $text = $this->model->title . "\n" . $this->model->content;
        } else {
            $text = $this->model->content;
        }

        // 2. Perform the Slow AI Check
        // We assume ContentFilter::check returns TRUE if content is UNSAFE
        $isUnsafe = ContentFilter::check($text);

        // 3. Update the Model
        if ($isUnsafe) {
            // Keep as pending or flag it (logic depends on your preference)
            $this->model->update(['status' => 'pending_review']);
        } else {
            // It's safe, publish it!
            $this->model->update(['status' => 'published']);
        }
    }
}