<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameScore extends Model {
    protected $fillable = ['game_session_id', 'family_id', 'family_member_id', 'score'];

    public function player() {
        return $this->belongsTo(FamilyMember::class, 'family_member_id');
    }

    public function family()
{
    return $this->belongsTo(Family::class);
}

}
