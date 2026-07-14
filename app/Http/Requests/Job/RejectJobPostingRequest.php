<?php

namespace App\Http\Requests\Job;

use App\Models\JobPosting;
use Illuminate\Foundation\Http\FormRequest;

class RejectJobPostingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('review', JobPosting::class);
    }

    public function rules(): array
    {
        return [
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
