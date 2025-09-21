<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyChallenge extends Model
{
    protected $fillable = [
        'family_id', 'challenge_id', 'title', 'type', 'description',
        'goal', 'progress', 'start_date', 'end_date', 'completed'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function percent(): int
    {
        if ($this->goal <= 0) return 0;
        return min(100, (int) floor(($this->progress / $this->goal) * 100));
    }

    public function challenge()
{
    return $this->belongsTo(Challenge::class);
}

}
