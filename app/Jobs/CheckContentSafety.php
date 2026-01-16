<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Question;
use App\Services\ContentFilter;
use Illuminate\Support\Facades\Storage; //

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
        // 1. Prepare text
        $text = '';
        $imagePaths = []; // Initialize empty array

        if ($this->model instanceof Question) {
            $text = $this->model->title . "\n" . $this->model->content;
            
            // 2. Load images if they exist
            // We need the full server path for file_get_contents later
            $this->model->load('images');
            foreach ($this->model->images as $img) {
                // Ensure file exists before trying to check it
                if (Storage::disk('public')->exists($img->image_path)) {
                     $imagePaths[] = Storage::disk('public')->path($img->image_path);
                }
            }
        } else {
            $text = $this->model->content;
        }

        // 3. Perform the AI Check (Passing both text and images)
        $isUnsafe = ContentFilter::check($text, $imagePaths);

        // 4. Update the Model
        if ($isUnsafe) {
            $this->model->update(['status' => 'pending_review']);
        } else {
            $this->model->update(['status' => 'published']);
        }
    }
}