<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NotEmptyContent;

class StoreAnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
{
    return [
        'content' => ['required', new NotEmptyContent],
    ];
}

    public function messages(): array
    {
        return [
            'content.required' => 'The answer content cannot be empty.',
        ];
    }
}