<?php

namespace App\Models;

use App\Enums\FeedbackStatus;
use App\Enums\FeedbackType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedbackTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'subject',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'type' => FeedbackType::class,
            'status' => FeedbackStatus::class,
            'closed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(FeedbackReply::class)->orderBy('created_at');
    }

    public function isOpen(): bool
    {
        return $this->status === FeedbackStatus::Open;
    }
}
