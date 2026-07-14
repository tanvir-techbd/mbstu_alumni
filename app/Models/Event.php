<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Enums\RoleName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'banner_path',
        'description',
        'venue',
        'event_date',
        'event_time',
        'registration_deadline',
        'capacity',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'registration_deadline' => 'datetime',
            'status' => EventStatus::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function registrants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_registrations')
            ->withPivot('attended')
            ->withTimestamps();
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('status', EventStatus::Published);
    }

    public function scopeVisibleTo(Builder $query, User $user): void
    {
        $query->where(function (Builder $q) use ($user) {
            $q->whereIn('status', [EventStatus::Published, EventStatus::Archived]);

            if ($user->hasRole(RoleName::SuperAdmin->value)) {
                $q->orWhere('status', EventStatus::Draft);
            } else {
                $q->orWhere(fn (Builder $qq) => $qq->where('status', EventStatus::Draft)->where('created_by', $user->id));
            }
        });
    }

    public function isRegistrationOpen(): bool
    {
        return $this->status === EventStatus::Published
            && now()->lt($this->registration_deadline)
            && ! $this->isFull();
    }

    public function isFull(): bool
    {
        return $this->capacity !== null && $this->registrations()->count() >= $this->capacity;
    }

    public function isRegisteredBy(User $user): bool
    {
        return $this->registrations()->where('user_id', $user->id)->exists();
    }
}
