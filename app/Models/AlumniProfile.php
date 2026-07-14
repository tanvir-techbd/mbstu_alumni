<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlumniProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'gender',
        'date_of_birth',
        'student_id',
        'department',
        'program',
        'batch',
        'session',
        'graduation_year',
        'cgpa',
        'company',
        'designation',
        'industry',
        'years_of_experience',
        'country',
        'district',
        'office_address',
        'linkedin_url',
        'github_url',
        'facebook_url',
        'portfolio_url',
        'skills',
        'biography',
        'interests',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'cgpa' => 'decimal:2',
            'verification_status' => VerificationStatus::class,
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

    public function skillList(): array
    {
        return $this->skills
            ? array_map('trim', explode(',', $this->skills))
            : [];
    }

    public function completionPercentage(): int
    {
        $fields = [
            'gender', 'date_of_birth', 'student_id', 'department', 'program',
            'batch', 'session', 'graduation_year', 'company', 'designation',
            'country', 'biography', 'skills',
        ];

        $filled = collect($fields)->filter(fn ($field) => filled($this->{$field}))->count();

        return (int) round(($filled / count($fields)) * 100);
    }
}
