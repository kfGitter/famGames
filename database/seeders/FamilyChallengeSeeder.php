<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Challenge;
use App\Models\FamilyChallenge;
use App\Models\Family;

class FamilyChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            //  Daily mini Challenges  
            [
                'title'       => 'Daily Game',
                'type'        => 'daily',
                'description' => 'Play at least 1 game today.',
                'goal'        => 1,
            ],
            // [
            //     'title'       => 'Daily Double',
            //     'type'        => 'daily',
            //     'description' => 'Play 2 different games in one day.',
            //     'goal'        => 2,
            // ],

            //  Weekly play 
            [
                'title'       => 'All Week Champions',
                'type'        => 'weekly',
                'description' => 'Play 7 games this week',
                'goal'        => 7,
            ],
            [
                'title'       => 'Try something New',
                'type'        => 'weekly',
                'description' => 'Play 3 different types of games this week.',
                'goal'        => 3,
            ],

            //--------------------------------------------------------------------------------------------------------------------
            //  Social / bonding challenges (hidden / unlockable)
            //--------------------------------------------------------------------------------------------------------------------
            // [
            //     'title'       => 'Family Bonding',
            //     'type'        => 'hidden',
            //     'description' => 'Play a game with every family member at least once.',
            //     'goal'        => 1,
            // ],

            //--------------------------------------------------------------------------------------------------------------------
            //  Narrative / meaningful
            //--------------------------------------------------------------------------------------------------------------------
            // [
            //     'title'       => 'Mini Quest',
            //     'type'        => 'narrative',
            //     'description' => 'Complete a series of 3 Adventure games over the weekend to unlock a special badge.',
            //     'goal'        => 3,
            // ],

            //--------------------------------------------------------------------------------------------------------------------
            //  Progression / streaks 
            //--------------------------------------------------------------------------------------------------------------------
            // [
            //     'title'       => 'Three-Day Streak',
            //     'type'        => 'streak',
            //     'description' => 'Play a game three days in a row.',
            //     'goal'        => 3,
            // ],
        ];

        foreach ($templates as $t) {
            // Ensure uniqueness by title + type
            Challenge::updateOrCreate(
                [
                    'title' => $t['title'],
                    'type'  => $t['type'],
                ], 
                $t
            );
        }

        $families   = Family::all();
        $challenges = Challenge::all();

        foreach ($families as $family) {
            foreach ($challenges as $challenge) {
                FamilyChallenge::firstOrCreate(
                    [
                        'family_id'    => $family->id,
                        'challenge_id' => $challenge->id,
                    ],
                    [
                        'title'       => $challenge->title,
                        'type'        => $challenge->type,
                        'description' => $challenge->description,
                        'progress'    => 0,
                        'completed'   => false,
                    ]
                );
            }
        }

        $this->command->info('Family challenges seeded successfully!');
    }
}
