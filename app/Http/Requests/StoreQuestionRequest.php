<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NotEmptyContent;

class StoreQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // You can add logic here (e.g., checks for email verification), 
        // or keep it true if middleware handles permissions.
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
        public function rules(): array
    {
        return [
            'title' => 'required',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:3072',
            
            'content' => ['required', new NotEmptyContent], 
        ];
    }
}