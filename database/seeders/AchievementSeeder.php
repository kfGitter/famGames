<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            [
                'code' => 'active_player',
                'name' => 'Active Player ⚡',
                'description' => 'Joined at least 10 sessions.',
            ],
            [
                'code' => 'streak_master',
                'name' => 'Streak Master 🔥',
                'description' => 'Won 3 games in a row.',
            ],
            [
                'code' => 'overall_champ',
                'name' => 'Overall Champ 👑',
                'description' => '500+ cumulative points.',
            ],
            [
                'code' => 'versatility_badge',
                'name' => 'Versatility 🎲',
                'description' => 'Played every game in the library.',
            ],
            [
                'code' => 'family_favorite',
                'name' => 'Family Favorite 🎯',
                'description' => 'Played the family’s most popular game 5+ times.',
            ],
            [
                'code' => 'newbie_slayer',
                'name' => 'Newbie Slayer 😂',
                'description' => 'Beat someone in their first session.',
            ],
        ];

        foreach ($achievements as $a) {
            Achievement::updateOrCreate(
                ['code' => $a['code']], // ensure uniqueness
                ['name' => $a['name'], 'description' => $a['description']]
            );
        }
    }
}
