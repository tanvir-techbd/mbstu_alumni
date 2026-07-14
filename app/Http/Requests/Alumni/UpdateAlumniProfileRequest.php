<?php

namespace App\Http\Requests\Alumni;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlumniProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->user()->alumniProfile);
    }

    public function rules(): array
    {
        return [
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'date_of_birth' => ['nullable', 'date', 'before:today'],

            'student_id' => ['nullable', 'string', 'max:50'],
            'department' => ['nullable', 'string', 'max:100'],
            'program' => ['nullable', 'string', 'max:100'],
            'batch' => ['nullable', 'string', 'max:50'],
            'session' => ['nullable', 'string', 'max:50'],
            'graduation_year' => ['nullable', 'integer', 'min:1950', 'max:'.(date('Y') + 1)],
            'cgpa' => ['nullable', 'numeric', 'min:0', 'max:4'],

            'company' => ['nullable', 'string', 'max:150'],
            'designation' => ['nullable', 'string', 'max:150'],
            'industry' => ['nullable', 'string', 'max:150'],
            'years_of_experience' => ['nullable', 'integer', 'min:0', 'max:60'],
            'country' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'office_address' => ['nullable', 'string', 'max:500'],

            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],

            'skills' => ['nullable', 'string', 'max:500'],
            'biography' => ['nullable', 'string', 'max:2000'],
            'interests' => ['nullable', 'string', 'max:500'],
        ];
    }
}
