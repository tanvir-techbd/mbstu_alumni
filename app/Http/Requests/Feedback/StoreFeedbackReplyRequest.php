<?php

namespace App\Http\Requests\Feedback;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('reply', $this->route('ticket'));
    }

    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:5000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->route('ticket')->isOpen()) {
                $validator->errors()->add('message', 'This ticket is closed and can no longer receive replies.');
            }
        });
    }
}
