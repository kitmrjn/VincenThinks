<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NotEmptyContent;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // We will keep the permission checks in the controller for safety
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            // Note: The limit is 5MB (5120) here vs 3MB in store, keeping your original logic
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', 
            'content' => ['required', new NotEmptyContent],
        ];
    }
}