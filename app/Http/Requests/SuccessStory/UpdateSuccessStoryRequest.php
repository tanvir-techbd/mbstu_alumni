<?php

namespace App\Http\Requests\SuccessStory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSuccessStoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('successStory'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'story' => ['required', 'string', 'max:5000'],
            'company' => ['nullable', 'string', 'max:150'],
            'achievement' => ['nullable', 'string', 'max:255'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'max:2048'],
        ];
    }
}
