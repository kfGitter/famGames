<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public function users()
{
    return $this->belongsToMany(User::class)->withTimestamps();
}
    //

    public function sessions()
{
    return $this->hasMany(GameSession::class);
}

}
