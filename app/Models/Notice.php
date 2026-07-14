<?php

namespace App\Models;

use App\Enums\NoticeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'content',
        'attachment_path',
    ];

    protected function casts(): array
    {
        return [
            'type' => NoticeType::class,
        ];
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notice_bookmarks')->withTimestamps();
    }

    public function isBookmarkedBy(User $user): bool
    {
        return $this->bookmarkedBy()->where('user_id', $user->id)->exists();
    }
}
