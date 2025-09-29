<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Substance extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'common_dosage',
        'notes',
        'is_predefined',
        'created_by_user_id',
        'is_public',
    ];

    protected $casts = [
        'is_predefined' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function journalEntries(): BelongsToMany
    {
        return $this->belongsToMany(JournalEntry::class, 'substance_entries')
            ->withPivot([
                'dosage',
                'taken_at',
                'duration_minutes',
                'focus_rating',
                'mood_rating',
                'sleep_rating',
                'side_effects',
                'effectiveness_rating',
                'notes'
            ])
            ->withTimestamps();
    }

    public function resources(): HasMany
    {
        return $this->hasMany(SubstanceResource::class);
    }

    public function verifiedResources(): HasMany
    {
        return $this->hasMany(SubstanceResource::class)->where('is_verified', true);
    }

    public function studiesAndReviews(): HasMany
    {
        return $this->hasMany(SubstanceResource::class)->whereIn('type', ['study', 'review']);
    }
}
