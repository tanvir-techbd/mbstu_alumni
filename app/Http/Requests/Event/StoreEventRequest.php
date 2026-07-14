<?php

namespace App\Http\Requests\Event;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Event::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'banner' => ['nullable', 'image', 'max:2048'],
            'description' => ['required', 'string', 'max:5000'],
            'venue' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'event_time' => ['required', 'date_format:H:i'],
            'registration_deadline' => ['required', 'date', 'before:event_date'],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
