<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomUserGame extends Model
{
    use HasFactory;

    protected $table = 'custom_user_games';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'rules',
        'min_players',
        'max_players',
        'scoring',
        'is_favorite',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'custom_user_game_tag');
    }
}
