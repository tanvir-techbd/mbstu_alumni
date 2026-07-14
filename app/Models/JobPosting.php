<?php

namespace App\Models;

use App\Enums\EmploymentType;
use App\Enums\JobStatus;
use App\Enums\RoleName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company',
        'company_logo_path',
        'position',
        'category',
        'employment_type',
        'salary',
        'experience',
        'location',
        'deadline',
        'description',
        'apply_url',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'employment_type' => EmploymentType::class,
            'status' => JobStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'job_bookmarks')->withTimestamps();
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('status', JobStatus::Published);
    }

    public function scopeVisibleTo(Builder $query, User $user): void
    {
        $query->where(function (Builder $q) use ($user) {
            $q->where('status', JobStatus::Published);

            if ($user->hasRole(RoleName::SuperAdmin->value)) {
                $q->orWhereIn('status', [JobStatus::Pending, JobStatus::Rejected]);
            } else {
                $q->orWhere(fn (Builder $qq) => $qq->whereIn('status', [JobStatus::Pending, JobStatus::Rejected])->where('posted_by', $user->id));
            }
        });
    }

    public function isBookmarkedBy(User $user): bool
    {
        return $this->bookmarkedBy()->where('user_id', $user->id)->exists();
    }

    public function isExpired(): bool
    {
        return $this->deadline->isPast();
    }
}
