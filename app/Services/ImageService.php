<?php

namespace App\Services;

use App\Models\QuestionImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageService
{
    /**
     * Store multiple images for a specific question.
     */
    public function attachQuestionImages($question, array $images): void
    {
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $path = $image->store('question_images', 'public');
                QuestionImage::create([
                    'question_id' => $question->id, 
                    'image_path' => $path
                ]);
            }
        }
    }

    /**
     * Delete specific images by ID.
     */
    public function deleteImages(array $imageIds, int $questionId): void
    {
        foreach ($imageIds as $imageId) {
            $image = QuestionImage::find($imageId);
            // Ensure the image actually belongs to the question being updated
            if ($image && $image->question_id == $questionId) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $image->delete();
            }
        }
    }

    /**
     * Delete all images associated with a question (for when a question is deleted).
     */
    public function deleteAllForQuestion($question): void
    {
        foreach ($question->images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        }
    }
}