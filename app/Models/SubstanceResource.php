<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubstanceResource extends Model
{
    protected $fillable = [
        'substance_id',
        'title',
        'description',
        'type',
        'url',
        'authors',
        'publication',
        'publication_date',
        'doi',
        'abstract',
        'tags',
        'quality_score',
        'key_findings',
        'is_verified',
        'added_by_user_id',
        'is_public',
    ];

    protected $casts = [
        'publication_date' => 'date',
        'tags' => 'array',
        'is_verified' => 'boolean',
        'is_public' => 'boolean',
        'quality_score' => 'integer',
    ];

    public function substance(): BelongsTo
    {
        return $this->belongsTo(Substance::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'study' => 'success',
            'article' => 'info',
            'review' => 'warning',
            'book' => 'primary',
            'video' => 'danger',
            'website' => 'gray',
            default => 'gray',
        };
    }

    public function getQualityBadgeAttribute(): string
    {
        if (!$this->quality_score) return 'gray';

        return match (true) {
            $this->quality_score >= 8 => 'success',
            $this->quality_score >= 6 => 'warning',
            default => 'danger',
        };
    }
}
