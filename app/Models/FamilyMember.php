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
}
