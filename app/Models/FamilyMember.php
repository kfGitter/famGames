<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMember extends Model
{
    protected $fillable = ['family_id', 'name', 'age', 'avatar'];

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
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
