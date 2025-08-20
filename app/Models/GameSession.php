<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    protected $fillable = ['game_id', 'user_id', 'family_id', 'status', 'winner_family_member_id'];

    public function game()  { return $this->belongsTo(Game::class); }
    public function family(){ return $this->belongsTo(Family::class); }

    public function players()
    {
        return $this->belongsToMany(FamilyMember::class, 'game_session_family_member', 'game_session_id', 'family_member_id');
    }

    public function scores()
    {
        return $this->hasMany(GameScore::class);
    }

    public function winner()
    {
        return $this->belongsTo(FamilyMember::class, 'winner_family_member_id');
    }
}
