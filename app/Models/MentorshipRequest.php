<?php

namespace App\Models;

use App\Enums\MentorshipStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorshipRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'mentor_id',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'status' => MentorshipStatus::class,
            'meeting_scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function scopeForStudent(Builder $query, User $user): void
    {
        $query->where('student_id', $user->id);
    }

    public function scopeForMentor(Builder $query, User $user): void
    {
        $query->where('mentor_id', $user->id);
    }
}
