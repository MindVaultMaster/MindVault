<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class JournalEntry extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'entry_date',
        'entry_time',
        'overall_focus',
        'overall_mood',
        'overall_sleep',
        'overall_energy',
        'general_notes',
        'is_public',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'entry_time' => 'datetime:H:i',
        'overall_focus' => 'integer',
        'overall_mood' => 'integer',
        'overall_sleep' => 'integer',
        'overall_energy' => 'integer',
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function substances(): BelongsToMany
    {
        return $this->belongsToMany(Substance::class, 'substance_entries')
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
}
