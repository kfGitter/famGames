<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Game;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        $games = [
            [
                'title' => 'Charades',
                'description' => 'Act out the word or phrase without speaking.',
                'rules' => 'No talking or mouthing words. Use only gestures.',
                'min_players' => 4,
                'max_players' => 20,
                'category' => 'Acting',
            ],
            [
                'title' => 'Pictionary',
                'description' => 'Draw the word for your team to guess.',
                'rules' => 'No letters, numbers, or verbal hints.',
                'min_players' => 4,
                'max_players' => 12,
                'category' => 'Drawing',
            ],
            [
                'title' => 'Family Quiz',
                'description' => 'Trivia questions for all ages.',
                'rules' => 'Answer correctly to score points.',
                'min_players' => 2,
                'max_players' => 10,
                'category' => 'Trivia',
            ],
            [
                'title' => 'Guess the Song',
                'description' => 'Listen to a short clip and guess the song title or artist.',
                'rules' => 'No use of music recognition apps. Teams take turns.',
                'min_players' => 2,
                'max_players' => 10,
                'category' => 'Music',
            ],
            [
                'title' => '20 Questions',
                'description' => 'Guess the object, person, or place within 20 yes/no questions.',
                'rules' => 'Only yes/no questions allowed.',
                'min_players' => 2,
                'max_players' => 8,
                'category' => 'Guessing',
            ],
            [
                'title' => 'Scavenger Hunt',
                'description' => 'Find and bring back listed items as quickly as possible.',
                'rules' => 'Items must match the exact description. No cheating.',
                'min_players' => 2,
                'max_players' => 15,
                'category' => 'Adventure',
            ],
            [
                'title' => 'Word Chain',
                'description' => 'Say a word that starts with the last letter of the previous word.',
                'rules' => 'No repeating words. Must be a valid dictionary word.',
                'min_players' => 2,
                'max_players' => 10,
                'category' => 'Word',
            ],
        ];

        foreach ($games as $game) {
            Game::firstOrCreate(['title' => $game['title']], $game);
        }
    }
}
