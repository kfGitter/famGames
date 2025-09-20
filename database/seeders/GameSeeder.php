<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Tag;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure tags exist first
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

        // Define offline family games
        $games = [
            [
                'title' => 'Charades',
                'description' => 'Act out a word or phrase without speaking while others guess.',
                'rules' => 'No talking or mouthing words. Use only gestures and movement.',
                'min_players' => 4,
                'max_players' => 20,
                'scoring' => '1 point per correct guess by your team. Optional: bonus for fastest acting.',
                'tags' => ['Funny', 'Team Play', 'All Ages', 'Active Game'],
            ],
            [
                'title' => 'Pictionary',
                'description' => 'Draw a word or phrase while your team guesses what it is.',
                'rules' => 'No letters, numbers, or symbols in drawings.',
                'min_players' => 4,
                'max_players' => 16,
                'scoring' => '1 point per correct guess. Optional: fastest drawer bonus.',
                'tags' => ['Drawing & Creativity', 'Funny', 'Team Play', 'All Ages'],
            ],
            [
                'title' => 'Trivia Quiz',
                'description' => 'Answer fun questions across different categories.',
                'rules' => 'Take turns answering. The one with most correct answers wins.',
                'min_players' => 2,
                'max_players' => 10,
                'scoring' => '1 point per correct answer.',
                'tags' => ['Trivia & Quiz', 'Competitive', 'All Ages', 'Chill'],
            ],
            [
                'title' => 'Mafia / Werewolf',
                'description' => 'Guess who’s secretly in the mafia.',
                'rules' => 'Players close eyes at “night” while mafia acts, then discuss and vote at “day.”',
                'min_players' => 6,
                'max_players' => 20,
                'scoring' => '1 point per survival round; extra points for winning as mafia or town.',
                'tags' => ['Team Play', 'Strategy', 'All Ages'],
            ],
            [
                'title' => 'Codenames (Word Clues)',
                'description' => 'Give clever one-word clues to guide your team.',
                'rules' => 'No gestures or sounds. Stick to a single word.',
                'min_players' => 4,
                'max_players' => 12,
                'scoring' => '1 point per correct guess; optional bonus for solving the board fastest.',
                'tags' => ['Word Game', 'Team Play', 'Strategy'],
            ],
            [
                'title' => 'Two Truths and a Lie',
                'description' => 'Players share two true facts and one lie about themselves—others must guess the lie.',
                'rules' => 'Each player states 3 things. Others must vote which one is fake.',
                'min_players' => 3,
                'max_players' => 15,
                'scoring' => '1 point for each correct guess. Bonus for fooling others with your lie.',
                'tags' => ['Funny', 'All Ages', 'Chill'],
            ],
            [
                'title' => 'Would You Rather?',
                'description' => 'Players ask each other funny or tricky “Would you rather…” questions.',
                'rules' => 'Each player must pick one of the two options—no skipping.',
                'min_players' => 2,
                'max_players' => 15,
                'scoring' => 'Optional: 1 point for each round played. Bonus for most unique choices.',
                'tags' => ['Funny', 'Chill', 'All Ages'],
            ],
            [
                'title' => 'Langabouri',
                'description' => 'Traditional Senegalese hide-and-seek with chasing and playful hits.',
                'rules' => 'One hides an object, gives hot/cold clues, and the finder chases others to safe zone.',
                'min_players' => 3,
                'max_players' => 12,
                'scoring' => '1 point for finding the object, 1 point for reaching safe zone first. Rotate roles each round.',
                'tags' => ['Active Game', 'Funny', 'All Ages', 'Quick Play'],
            ],
            [
                'title' => 'Freeze Dance',
                'description' => 'Dance freely while music plays. Freeze when it stops!',
                'rules' => 'No moving when the music stops. First to move is out.',
                'min_players' => 2,
                'max_players' => 20,
                'scoring' => '1 point per round survived. Last standing gets 3 bonus points.',
                'tags' => ['Active Game', 'Funny', 'Quick Play', 'All Ages'],
            ],
            [
                'title' => 'Simon Says',
                'description' => 'Follow commands only if they start with "Simon says".',
                'rules' => 'Move only when "Simon says." Moving otherwise means you are out.',
                'min_players' => 2,
                'max_players' => 15,
                'scoring' => '1 point per round survived. Last standing gets 3 bonus points.',
                'tags' => ['Active Game', 'All Ages', 'Quick Play', 'Funny'],
            ],
            [
                'title' => 'Human Knot',
                'description' => 'Grab hands in a knot and untangle as a team.',
                'rules' => 'Do not let go of hands. Work together to untangle.',
                'min_players' => 4,
                'max_players' => 12,
                'scoring' => '1 point per team successfully untangled. Optional: fastest group bonus.',
                'tags' => ['Team Play', 'Active Game', 'Strategy', 'All Ages'],
            ],
            [
                'title' => 'Balloon Keep-Up',
                'description' => 'Keep the balloon in the air as long as possible.',
                'rules' => 'Use hands, head, or feet. If balloon hits the ground, you’re out.',
                'min_players' => 2,
                'max_players' => 12,
                'scoring' => '1 point per successful hit. Last standing gets 3 bonus points.',
                'tags' => ['Active Game', 'Funny', 'Quick Play', 'All Ages'],
            ],
            [
                'title' => 'Hot Potato',
                'description' => 'Pass an object quickly. Don’t be holding it when the music stops!',
                'rules' => 'Keep passing. Last to hold when music stops is out.',
                'min_players' => 3,
                'max_players' => 15,
                'scoring' => '1 point per round survived. Last standing gets 3 bonus points.',
                'tags' => ['Active Game', 'Funny', 'Quick Play', 'All Ages'],
            ],
            [
                'title' => 'Red Light, Green Light',
                'description' => 'Move on green, freeze on red. First to the finish wins.',
                'rules' => 'Stop immediately on red. Moving on red sends you back to start.',
                'min_players' => 2,
                'max_players' => 12,
                'scoring' => '1 point per round completed. First to finish gets 3 bonus points.',
                'tags' => ['Active Game', 'All Ages', 'Quick Play'],
            ],
            [
                'title' => 'Scavenger Hunt',
                'description' => 'Find items from a list or theme within a time limit.',
                'rules' => 'Collect all items before time is up. First to finish wins.',
                'min_players' => 2,
                'max_players' => 20,
                'scoring' => '1 point per item collected. Bonus points for finishing fastest.',
                'tags' => ['Active Game', 'Team Play', 'All Ages', 'Funny'],
            ],
            [
                'title' => 'Telephone / Whisper Down the Lane',
                'description' => 'Whisper a message down a line and see how it changes!',
                'rules' => 'Do not clarify or repeat. Last player says the final message.',
                'min_players' => 4,
                'max_players' => 15,
                'scoring' => '1 point for accurately passing the message. Bonus for funniest final message.',
                'tags' => ['Funny', 'Team Play', 'All Ages'],
            ],
            [
                'title' => 'Charades with a Twist',
                'description' => 'Act out words or phrases, but include a dance move.',
                'rules' => 'No letters or numbers. Must include a dance move.',
                'min_players' => 4,
                'max_players' => 20,
                'scoring' => '1 point per correct guess. Bonus if team performs creative moves.',
                'tags' => ['Funny', 'Active Game', 'Team Play', 'All Ages'],
            ],
            [
                'title' => 'Obstacle Course',
                'description' => 'Race through a simple indoor or outdoor course.',
                'rules' => 'Complete the course in the shortest time possible.',
                'min_players' => 2,
                'max_players' => 12,
                'scoring' => '1 point per completed course. Optional: time bonus for fastest completion.',
                'tags' => ['Active Game', 'Quick Play', 'All Ages'],
            ],
        ];

        // Seed games and attach tags
        foreach ($games as $gameData) {
            $tagNames = $gameData['tags'] ?? [];
            unset($gameData['tags']);

            $game = Game::firstOrCreate(
                ['title' => $gameData['title']],
                $gameData
            );

            if (!empty($tagNames)) {
                $tagIds = collect($tagNames)->map(fn($name) => $tags[$name]->id);
                $game->tags()->sync($tagIds);
            }
        }
    }
}
