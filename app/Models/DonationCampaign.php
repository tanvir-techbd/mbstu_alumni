<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'goal_amount',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'goal_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => CampaignStatus::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', CampaignStatus::Active);
    }

    public function totalRaised(): string
    {
        return (string) $this->donations()->sum('amount');
    }

    public function donorCount(): int
    {
        return $this->donations()->distinct('user_id')->count('user_id');
    }

    public function progressPercentage(): ?int
    {
        if (! $this->goal_amount || (float) $this->goal_amount <= 0) {
            return null;
        }

        return (int) min(100, round(((float) $this->totalRaised() / (float) $this->goal_amount) * 100));
    }
}
