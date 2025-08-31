<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Tag;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        // Make sure tags exist first
        $tagNames = [
            'Quick Play', 'Strategy', 'Team Play', 'All Ages',
            'Parents vs Kids', 'Card Game', 'Board Game', 'Active Game',
            'Word Game', 'Competitive', 'Trivia & Quiz',
            'Drawing & Creativity', 'Funny', 'Chill', 'Travel Friendly'
        ];

        $tags = [];
        foreach ($tagNames as $name) {
            $tags[$name] = Tag::firstOrCreate(['name' => $name]);
        }

        // Define games with associated tags
        $games = [
            [
                'title' => 'Charades',
                'description' => 'Act out the word or phrase without speaking.',
                'rules' => 'No talking or mouthing words. Use only gestures.',
                'min_players' => 4,
                'max_players' => 20,
                'category' => 'Acting',
                'tags' => ['Funny', 'Team Play', 'All Ages', 'Active Game'],
            ],
            [
                'title' => 'Pictionary',
                'description' => 'Draw the word for your team to guess.',
                'rules' => 'No letters, numbers, or verbal hints.',
                'min_players' => 4,
                'max_players' => 12,
                'category' => 'Drawing',
                'tags' => ['Drawing & Creativity', 'Team Play', 'Funny'],
            ],
            [
                'title' => 'Family Quiz',
                'description' => 'Trivia questions for all ages.',
                'rules' => 'Answer correctly to score points.',
                'min_players' => 2,
                'max_players' => 10,
                'category' => 'Trivia',
                'tags' => ['Trivia & Quiz', 'Competitive', 'All Ages'],
            ],
            [
                'title' => 'Guess the Song',
                'description' => 'Listen to a short clip and guess the song title or artist.',
                'rules' => 'No use of music recognition apps. Teams take turns.',
                'min_players' => 2,
                'max_players' => 10,
                'category' => 'Music',
                'tags' => ['Quick Play', 'Funny', 'Parents vs Kids'],
            ],
            [
                'title' => '20 Questions',
                'description' => 'Guess the object, person, or place within 20 yes/no questions.',
                'rules' => 'Only yes/no questions allowed.',
                'min_players' => 2,
                'max_players' => 8,
                'category' => 'Guessing',
                'tags' => ['Strategy', 'Chill', 'Travel Friendly'],
            ],
            [
                'title' => 'Scavenger Hunt',
                'description' => 'Find and bring back listed items as quickly as possible.',
                'rules' => 'Items must match the exact description. No cheating.',
                'min_players' => 2,
                'max_players' => 15,
                'category' => 'Adventure',
                'tags' => ['Active Game', 'Team Play', 'Funny'],
            ],
            [
                'title' => 'Word Chain',
                'description' => 'Say a word that starts with the last letter of the previous word.',
                'rules' => 'No repeating words. Must be a valid dictionary word.',
                'min_players' => 2,
                'max_players' => 10,
                'category' => 'Word',
                'tags' => ['Word Game', 'Quick Play', 'Travel Friendly'],
            ],
        ];

        // Seed games and attach tags
        foreach ($games as $gameData) {
            $game = Game::firstOrCreate(
                ['title' => $gameData['title']],
                $gameData
            );

            if (isset($gameData['tags'])) {
                $tagIds = collect($gameData['tags'])->map(fn($name) => $tags[$name]->id);
                $game->tags()->sync($tagIds); // sync ensures correct tags
            }
        }
    }
}
