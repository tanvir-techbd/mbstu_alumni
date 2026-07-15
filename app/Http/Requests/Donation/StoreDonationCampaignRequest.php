<?php

namespace App\Http\Requests\Donation;

use App\Models\DonationCampaign;
use Illuminate\Foundation\Http\FormRequest;

class StoreDonationCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', DonationCampaign::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'goal_amount' => ['nullable', 'numeric', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
