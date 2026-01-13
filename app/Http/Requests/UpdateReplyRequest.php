<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NotEmptyContent;

class UpdateReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', new NotEmptyContent],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'The reply content cannot be empty.',
        ];
    }
}