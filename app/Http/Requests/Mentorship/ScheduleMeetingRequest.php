<?php

namespace App\Http\Requests\Mentorship;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('respond', $this->route('mentorshipRequest'));
    }

    public function rules(): array
    {
        return [
            'meeting_scheduled_at' => ['required', 'date', 'after:now'],
            'meeting_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
