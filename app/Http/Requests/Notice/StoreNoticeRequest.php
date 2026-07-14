<?php

namespace App\Http\Requests\Notice;

use App\Enums\NoticeType;
use App\Models\Notice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Notice::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(NoticeType::class)],
            'content' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
