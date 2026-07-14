<?php

namespace App\Http\Requests\Mentorship;

use App\Models\MentorshipRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreMentorshipRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('request', [MentorshipRequest::class, $this->route('mentor')]);
    }

    public function rules(): array
    {
        return [
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
