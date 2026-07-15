<?php

namespace App\Http\Requests\Feedback;

use App\Enums\FeedbackType;
use App\Models\FeedbackTicket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeedbackTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', FeedbackTicket::class);
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(FeedbackType::class)],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }
}
