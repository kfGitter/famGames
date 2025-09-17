<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Streak extends Model
{
    protected $fillable = [
        'streakable_type',
        'streakable_id',
        'cadence',
        'count',
        'best',
        'last_date',
        'started_at',
    ];

    protected $dates = ['last_date','started_at'];

    public function streakable(): MorphTo
    {
        return $this->morphTo();
    }
}
