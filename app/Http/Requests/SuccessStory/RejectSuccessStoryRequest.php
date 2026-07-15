<?php

namespace App\Http\Requests\SuccessStory;

use App\Models\SuccessStory;
use Illuminate\Foundation\Http\FormRequest;

class RejectSuccessStoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('review', SuccessStory::class);
    }

    public function rules(): array
    {
        return [
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ];
    }
}
