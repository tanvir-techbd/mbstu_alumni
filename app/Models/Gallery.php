<?php

namespace App\Models;

use App\Enums\GalleryCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'category' => GalleryCategory::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class);
    }

    public function coverImage(): ?GalleryImage
    {
        return $this->images->first();
    }
}
