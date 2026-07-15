<?php

namespace App\Models;

use App\Enums\RoleName;
use App\Enums\SuccessStoryStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuccessStory extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'story',
        'company',
        'achievement',
    ];

    protected function casts(): array
    {
        return [
            'status' => SuccessStoryStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(SuccessStoryImage::class);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('status', SuccessStoryStatus::Published);
    }

    public function scopeVisibleTo(Builder $query, User $user): void
    {
        $query->where(function (Builder $q) use ($user) {
            $q->where('status', SuccessStoryStatus::Published);

            if ($user->hasRole(RoleName::SuperAdmin->value)) {
                $q->orWhereIn('status', [SuccessStoryStatus::Pending, SuccessStoryStatus::Rejected]);
            } else {
                $q->orWhere(fn (Builder $qq) => $qq->whereIn('status', [SuccessStoryStatus::Pending, SuccessStoryStatus::Rejected])->where('user_id', $user->id));
            }
        });
    }
}
