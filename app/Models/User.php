<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\CustomUserGame;
use Illuminate\Database\Eloquent\Model;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'family_name',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

//     public function games()
// {
//     return $this->belongsToMany(Game::class, 'game_user')->withTimestamps();
// }

public function games()
{
    return $this->belongsToMany(Game::class)
                ->withPivot('is_favorite')  // <- add this
                ->withTimestamps();
}


public function family()
{
    return $this->belongsTo(\App\Models\Family::class);
}

public function gameSessions()
{
    return $this->belongsToMany(GameSession::class, 'game_session_user')->withTimestamps();
}

public function customUserGames()
{
    return $this->hasMany(CustomUserGame::class);
}


}
