<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(FeedbackTicket::class, 'feedback_ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
