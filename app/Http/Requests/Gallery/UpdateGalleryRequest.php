<?php

namespace App\Http\Requests\Gallery;

use App\Enums\GalleryCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('gallery'));
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::enum(GalleryCategory::class)],
            'description' => ['nullable', 'string', 'max:2000'],
            'images' => ['nullable', 'array', 'max:20'],
            'images.*' => ['image', 'max:4096'],
        ];
    }
}
