<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FamilyMember extends Model
{
    protected $fillable = ['family_id', 'name', 'age', 'avatar'];

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * Game sessions this member participated in.
     */
    public function gameSessions(): BelongsToMany
    {
        return $this->belongsToMany(
            GameSession::class,
            'game_session_family_member',
            'family_member_id',
            'game_session_id'
        )->withTimestamps();
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'family_member_achievements')
            ->withPivot('awarded_at')
            ->select('achievements.id', 'achievements.name', 'achievements.icon', 'achievements.description', 'achievements.code');
    }

    public function scores()
    {
        return $this->hasMany(GameScore::class);
    }

    public function streaks()
    {
        return $this->morphMany(\App\Models\Streak::class, 'streakable');
    }


    //helpers

    public function hasAchievement(string $code): bool
    {
        // NOTE the table name prefix to filter by code on the achievements table
        return $this->achievements()->where('achievements.code', $code)->exists();
    }

    public function awardAchievement(string $code): void
    {
        $achId = \App\Models\Achievement::where('code', $code)->value('id');
        if (!$achId) return; // safeguard if seeder missing
        // Prevent duplicates, record first time it was earned
        $this->achievements()->syncWithoutDetaching([$achId => ['awarded_at' => now()]]);
    }
}
