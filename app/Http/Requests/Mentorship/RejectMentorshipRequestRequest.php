<?php

namespace App\Http\Requests\Mentorship;

use Illuminate\Foundation\Http\FormRequest;

class RejectMentorshipRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('respond', $this->route('mentorshipRequest'));
    }

    public function rules(): array
    {
        return [
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
