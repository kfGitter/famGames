<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    protected $fillable = ['name'];

    public function members(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function streaks()
{
    return $this->morphMany(\App\Models\Streak::class, 'streakable');
}


// Family.php (Model)

public function achievements()
{
    // using your existing pivot table name
    return $this->belongsToMany(\App\Models\Achievement::class, 'family_achievements')
                ->withPivot('awarded_at')
                ->withTimestamps();
}

/**
 * Awards achievement to family if not already present.
 */
public function awardAchievementToFamily(string $code): void
{
    $achievement = \App\Models\Achievement::where('code', $code)->first();
    if (! $achievement) {
        return;
    }

    // attach only if not exists
    if (! $this->achievements()->where('achievements.id', $achievement->id)->exists()) {
        $this->achievements()->attach($achievement->id, ['awarded_at' => now()]);
    }
}


}
