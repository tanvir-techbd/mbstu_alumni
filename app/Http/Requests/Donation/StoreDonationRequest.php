<?php

namespace App\Http\Requests\Donation;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:10'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
        ];
    }
}
