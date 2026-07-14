<?php

namespace App\Http\Requests\Job;

use App\Enums\EmploymentType;
use App\Models\JobPosting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobPostingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', JobPosting::class);
    }

    public function rules(): array
    {
        return [
            'company' => ['required', 'string', 'max:150'],
            'company_logo' => ['nullable', 'image', 'max:2048'],
            'position' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:100'],
            'employment_type' => ['required', Rule::enum(EmploymentType::class)],
            'salary' => ['nullable', 'string', 'max:100'],
            'experience' => ['nullable', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:150'],
            'deadline' => ['required', 'date', 'after:today'],
            'description' => ['required', 'string', 'max:5000'],
            'apply_url' => ['required', 'url', 'max:255'],
        ];
    }
}
