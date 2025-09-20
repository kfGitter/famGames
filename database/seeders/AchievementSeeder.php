<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [

            // 👤 MEMBER ACHIEVEMENTS
            [
                'code' => 'active_player',
                'name' => 'Active Player ⚡',
                'description' => 'Joined at least 10 sessions.',
            ],
            [
                'code' => 'triple_winner',
                'name' => 'Triple Winner 🔥🔥🔥',
                'description' => 'Won 3 games in a row.',
            ],
            [
                'code' => 'overall_champ',
                'name' => 'Overall Champ 👑',
                'description' => 'Scored 500+ cumulative points.',
            ],
            [
                'code' => 'versatility_badge',
                'name' => 'Versatility Expert 🎲',
                'description' => 'Tried every game in the library at least once.',
            ],
            [
                'code' => 'newbie_slayer',
                'name' => 'Newbie Slayer 🥹😂',
                'description' => 'Beat a newcomer in their very first game.',
            ],
            [
                'code' => 'comeback_kid',
                'name' => 'Comeback Champ 🪃',
                'description' => 'Won after being in last place before.',
            ],
            [
                'code' => 'early_bird',
                'name' => 'Early Bird 🌞',
                'description' => 'Joined a session before 10am.',
            ],
            [
                'code' => 'night_owl',
                'name' => 'Night Owl 🌙',
                'description' => 'Played 5 sessions after 10pm.',
            ],

            // 🔥 MEMBER STREAKS
            [
                'code' => 'streak_boss',
                'name' => ' Streak Boss 🥉',
                'description' => 'Played every day for at least 3 consecutive days.',
            ],
            [
                'code' => 'daily_streak_master',
                'name' => 'Daily Streak Master 🌞',
                'description' => 'Played every day for 3 consecutive days.',
            ],
            [
                'code' => 'weekly_streak_master',
                'name' => 'Weekly Warrior 📅🔥',
                'description' => 'Played every week for a month.',
            ],

            // 🏆 Per-Game Win Milestones
            [
                'code' => 'game_boss_5',
                'name' => 'Game Boss 🎮',
                'description' => 'Won the same game 5 times.',
            ],
            [
                'code' => 'game_master_10',
                'name' => 'Game Master 👑',
                'description' => 'Won the same game 10 times.',
            ],
            [
                'code' => 'game_expert_20',
                'name' => 'Game Expert 🧙‍♂️',
                'description' => 'Won the same game 20 times.',
            ],

            // 🏆 Overall Wins (any games)
            [
                'code' => 'winner_boss_5',
                'name' => 'Winner Boss 🏆',
                'description' => 'Won 5 games in total.',
            ],
            [
                'code' => 'winner_master_10',
                'name' => 'Winner Master 👑',
                'description' => 'Won 10 games in total.',
            ],
            [
                'code' => 'winner_expert_20',
                'name' => 'Winner Expert 🧙‍♂️',
                'description' => 'Won 20 games in total.',
            ],

            // 👤 Member engagement
            [
                'code' => 'active_champ',
                'name' => 'Participation Champ 🏅',
                'description' => 'Most active member across the last 3 sessions.',
            ],

            // 👨‍👩‍👧‍👦 FAMILY ACHIEVEMENTS
            [
                'code' => 'family_favorite',
                'name' => 'Family Favorite 🎯',
                'description' => 'Played the family’s most popular game 5+ times.',
            ],
            [
                'code' => 'family_champ',
                'name' => 'Family Champ 👨‍👩‍👧‍👦👑',
                'description' => 'Earned 1000+ points as a family.',
            ],
            [
                'code' => 'family_milestone_10',
                'name' => 'Milestone 10 🎉',
                'description' => 'Completed 10 family sessions.',
            ],
            [
                'code' => 'family_streak_boss',
                'name' => 'Streak Bosses 🔥👨‍👩‍👧‍👦',
                'description' => 'The family played together everyday in a week.',
            ],
            [
                'code' => 'all_together',
                'name' => 'All Together! 🤝',
                'description' => 'Every family member joined a single game session.',
            ],
            [
                'code' => 'party_animals',
                'name' => 'Party Experts 🎊',
                'description' => 'Played more than 5 games in one evening.',
            ],
            [
                'code' => 'marathon_family',
                'name' => 'Marathon Family 🏃‍♂️🏃‍♀️',
                'description' => 'Played 10 sessions in a single weekend.',
            ],

            // Other Family milestones
            [
                'code' => 'family_of_winners',
                'name' => 'Family of Winners 🥇',
                'description' => 'Every family member has at least 1 win.',
            ],
            [
                'code' => 'family_of_experts',
                'name' => 'Family of Experts 😎',
                'description' => 'Every family member has at least 5 wins.',
            ],
            [
                'code' => 'are_you_humans',
                'name' => 'Are You Even Humans?? 🤯',
                'description' => 'Every family member has at least 10 wins.',
            ],
            [
                'code' => 'active_family',
                'name' => 'Active Family 👨‍👩‍👧‍👦⚡',
                'description' => 'Every family member played at least 3 times.',
            ],
            [
                'code' => 'super_active_family',
                'name' => 'Super Active Family 🤩',
                'description' => 'Every family member played at least 10 times.',
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
