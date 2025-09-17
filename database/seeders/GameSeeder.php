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
                'description' => 'Draw a word or phrase while others guess what it is.',
                'rules' => 'No letters or numbers allowed in drawings.',
                'min_players' => 4,
                'max_players' => 16,
                'category' => 'Drawing',
                'tags' => ['Funny', 'Team Play', 'All Ages', 'Active Game'],
            ],

            [
                'title' => 'Trivia Quiz',
                'description' => 'Answer questions from various categories.',
                'rules' => 'Players take turns answering questions.',
                'min_players' => 2,
                'max_players' => 10,
                'category' => 'Quiz',
                'tags' => ['Competitive', 'All Ages', 'Chill'],
            ],

            [
                'title' => 'Mafia',
                'description' => 'Deduce who among the players is part of the mafia.',
                'rules' => 'Players discuss and vote to eliminate suspects.',
                'min_players' => 6,
                'max_players' => 20,
                'category' => 'Social Deduction',
                'tags' => ['Team Play', 'All Ages', 'Chill'],
            ],

            [
                'title' => 'Codenames',
                'description' => 'Give one-word clues to help your team guess words.',
                'rules' => 'No gestures or sounds allowed.',
                'min_players' => 4,
                'max_players' => 12,
                'category' => 'Word Game',
                'tags' => ['Team Play', 'All Ages', 'Chill'],
            ],
            [
                'title' => 'Exploding Kittens',
                'description' => 'A card game about kittens that explode.',
                'rules' => 'Players take turns drawing cards until someone draws an exploding kitten.',
                'min_players' => 2,
                'max_players' => 5,
                'category' => 'Card Game',
                'tags' => ['Funny', 'Team Play', 'All Ages'],
            ],
            [
                'title' => 'Sushi Go!',
                'description' => 'A fast-paced card game about sushi dishes.',
                'rules' => 'Players draft cards to create the best sushi meal.',
                'min_players' => 2,
                'max_players' => 5,
                'category' => 'Card Game',
                'tags' => ['Funny', 'Team Play', 'All Ages'],
            ]
        ];



        // Seed games and attach tags
        foreach ($games as $gameData) {
            // Remove tags from insert data
            $tagNames = $gameData['tags'] ?? [];
            unset($gameData['tags']);

            // Create or fetch the game
            $game = Game::firstOrCreate(
                ['title' => $gameData['title']],
                $gameData
            );

            // Attach tags
            if (!empty($tagNames)) {
                $tagIds = collect($tagNames)->map(fn($name) => $tags[$name]->id);
                $game->tags()->sync($tagIds);
            }
        }
    }
}
