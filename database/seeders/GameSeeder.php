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
        ];
        foreach ($games as $game) {
            Game::firstOrCreate(['title' => $game['title']], $game);
        }
    }
}
