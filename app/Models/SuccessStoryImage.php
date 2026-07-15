<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuccessStoryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
    ];

    public function successStory(): BelongsTo
    {
        return $this->belongsTo(SuccessStory::class);
    }
}
