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


}
