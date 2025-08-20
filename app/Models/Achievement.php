<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = ['code', 'name', 'icon', 'description'];

    public function members()
    {
        return $this->belongsToMany(FamilyMember::class, 'family_member_achievements')
            ->withPivot('awarded_at');
    }
}
