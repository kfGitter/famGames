<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Tag;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        // Making sure tags exist first
        $tagNames = [
            'Quick Play',
            'Strategy',
            'Team Play',
            'All Ages',
            'Parents vs Kids',
            'Card Game',
            'Board Game',
            'Active Game',
            'Word Game',
            'Competitive',
            'Trivia & Quiz',
            'Drawing & Creativity',
            'Funny',
            'Chill',
            'Travel Friendly'
        ];

        $tags = [];
        foreach ($tagNames as $name) {
            $tags[$name] = Tag::firstOrCreate(['name' => $name]);
        }

        // Defining the games (about 25 so far)
        $games = [
            [
                'title' => 'Charades',
                'description' => 'Act out a word or phrase without speaking while others guess. Can be individal or team-based. 
                You can propose lists or go freestyle',
                'rules' => 'No talking or mouthing words. Use only gestures and movement.',
                'min_players' => 4,
                'max_players' => 20,
                'scoring' => '10 points per correct guess. Optional: Penalty -3 points for trespassing the rules.',
                'tags' => ['Funny', 'Team Play', 'All Ages', 'Active Game', 'Parents vs Kids'],
            ],
            [
                'title' => 'Pictionary',
                'description' => 'Draw a word or phrase while the others (or your team) guesses what it is.',
                'rules' => 'No letters, numbers, or symbols in drawings.',
                'min_players' => 4,
                'max_players' => 16,
                'scoring' => '10 points per correct guess. Optional: Penalty -3 points for trespassing the rules.',
                'tags' => ['Drawing & Creativity', 'Funny', 'Team Play', 'All Ages', 'Parents vs Kids'],
            ],
            [
                'title' => 'Trivia Quiz',
                'description' => 'Answer fun questions across different categories.',
                'rules' => 'Take turns answering. The one with most correct answers wins.',
                'min_players' => 2,
                'max_players' => 10,
                'scoring' => '10 points per correct answer.',
                'tags' => ['Trivia & Quiz', 'Competitive', 'All Ages', 'Chill', 'Travel Friendly', 'Parents vs Kids'],
            ],
            [
                'title' => 'Mafia / Werewolf',
                'description' => 'A social deduction game where players try to figure out who is secretly in the mafia while surviving each round.',
                'rules' => "Night phase: Everyone closes their eyes. Mafia secretly choose a town member to eliminate. Optional roles like Detective can investigate.\n
                Day phase: All players open their eyes. Discuss and vote to eliminate a suspected mafia member.\n
                Game continues alternating night and day until either all mafia are eliminated (town wins) or mafia equal or outnumber town (mafia wins).",
                'min_players' => 6,
                'max_players' => 20,
                'scoring' => '20 points per round survived; 30 extra points if your team (mafia or town) wins the game.',
                'tags' => ['Team Play', 'Strategy', 'All Ages', 'Funny'],
            ],
            [
                'title' => 'Codenames (Word Clues)',
                'description' => 'A word association game where teams try to guess their secret words based on one-word clues from their Spymaster.',
                'rules' => "Teams: Red and Blue, each with a Spymaster.\n
                Spymaster gives a one-word clue and a number indicating how many words relate to the clue.\n
                Team guesses the words. Correct guesses are marked. Avoid guessing the opponent's words or the Assassin word.\n
                Teams alternate turns. First team to find all their words wins.",
                'min_players' => 4,
                'max_players' => 12,
                'scoring' => '10 points per correct word guessed; optional 30 points bonus for completing all words first.',
                'tags' => ['Word Game', 'Team Play', 'Strategy', 'All Ages'],
            ],
            [
                'title' => 'Two Truths and a Lie',
                'description' => 'Players share two true facts and one lie about themselves—others must guess the lie.',
                'rules' => 'Each player states 3 things. Others must vote which one is fake.',
                'min_players' => 2,
                'max_players' => 15,
                'scoring' => '10 point for each correct guess. Bonus for fooling others with your lie.',
                'tags' => ['Funny', 'All Ages', 'Chill', 'Travel Friendly', 'Parents vs Kids'],
            ],
            [
                'title' => 'Would You Rather?',
                'description' => 'Players ask each other funny or tricky “Would you rather…” questions.',
                'rules' => 'Each player must pick one of the two options, no skipping.',
                'min_players' => 2,
                'max_players' => 15,
                'scoring' => 'Everybody starts with 100 points; -10 points for each skipped round (not choosing).',
                'tags' => ['Funny', 'Chill', 'All Ages', 'Travel Friendly', 'Parents vs Kids'],
            ],
            [
                'title' => 'Langabouri',
                'description' => 'Traditional Senegalese hide-and-seek with chasing and playful hits.',
                'rules' => 'One hides an object, gives hot/cold clues (structured of free-style hints), and the finder chases others to safe zone.',
                'min_players' => 3,
                'max_players' => 12,
                'scoring' => '10 points for solving hints enigmas, 100 points for finding the object. Rotate roles each round.',
                'tags' => ['Active Game', 'Funny', 'All Ages', 'Quick Play'],
            ],
            [
                'title' => 'Musical Chairs',
                'description' => 'Place (number of players - 1) chairs in a circle. Dance freely around chairs while music plays. When the music stops, 
                quickly find a chair to sit in (this part can get crazy 🤣). One chair is removed each round.',
                'rules' => 'Find a chair to sit in when the music stops. Last person standing is out.',
                'min_players' => 2,
                'max_players' => 20,
                'scoring' => '10 points per round survived. Last sitting gets 30 bonus points.',
                'tags' => ['Active Game', 'Competitive', 'Funny', 'Quick Play', 'All Ages', 'Parents vs Kids'],
            ],

            [
                'title' => 'Freeze Dance',
                'description' => 'Dance freely while music plays. Freeze when it stops!',
                'rules' => 'No moving when the music stops. First to move is out.',
                'min_players' => 2,
                'max_players' => 20,
                'scoring' => '10 points per round survived. Last standing gets 30 bonus points.',
                'tags' => ['Active Game', 'Funny', 'Quick Play', 'All Ages', 'Parents vs Kids'],
            ],
            [
                'title' => 'Simon Says',
                'description' => 'Follow commands only if they start with "Simon says".',
                'rules' => 'Move only when "Simon says." Moving otherwise means you are out.',
                'min_players' => 2,
                'max_players' => 15,
                'scoring' => '10 point per round survived. Last standing gets 3 bonus points.',
                'tags' => ['Active Game', 'All Ages', 'Quick Play', 'Funny'],
            ],

            [
                'title' => 'Duck Duck Goose',
                'description' => 'Sit in a circle. One walks around tapping heads saying "duck" until choosing "goose" who must chase them.',
                'rules' => 'The "goose" must catch the tapper before they sit in the empty spot.',
                'min_players' => 4,
                'max_players' => 20,
                'scoring' => '10 points per round survived. Bonus for fastest tapper or catcher.',
                'tags' => ['Active Game', 'All Ages', 'Quick Play', 'Funny'],
            ],

            [
                'title' => 'No yes / No no',
                'description' => 'Answer questions without saying "yes" or "no".',
                'rules' => 'You cannot say "yes" or "no". If you do, you are out.',
                'min_players' => 2,
                'max_players' => 10,
                'scoring' => '10 points per question answered without slipping. Last standing gets 30 bonus points.',
                'tags' => ['Funny', 'Chill', 'All Ages', 'Quick Play', 'Travel Friendly', 'Parents vs Kids'],
            ],

            [
                'title' => 'Langue de chats',
                'description' => 'Players try to say tongue twisters quickly and clearly.',
                'rules' => 'Take turns saying a tongue twister. If you mess up, you are out.',
                'min_players' => 2,
                'max_players' => 10,
                'scoring' => '10 points per successful tongue twister. Last standing gets 30 bonus points.',
                'tags' => ['Funny', 'Chill', 'All Ages', 'Quick Play', 'Travel Friendly', 'Word Game'],
            ],

            [
                'title' => '20 Questions',
                'description' => 'Guess the object someone is thinking of by asking yes/no questions.',
                'rules' => 'You can only ask yes/no questions. You have 20 questions to guess the object.',
                'min_players' => 2,
                'max_players' => 10,
                'scoring' => '10 points for guessing correctly within 20 questions. Bonus for fewer questions used.',
                'tags' => ['Word Game', 'Chill', 'All Ages', 'Travel Friendly'],
            ],

            [
                'title' => 'Categories',
                'description' => 'Pick a category and take turns naming items in that category until someone can’t think of one.',
                'rules' => 'No repeating items. If you can’t think of one, you are out.',
                'min_players' => 2,
                'max_players' => 10,
                'scoring' => '10 points per item named. Last standing gets 30 bonus points.',
                'tags' => ['Word Game', 'Chill', 'All Ages', 'Travel Friendly'],
            ],
            [
                'title' => 'spelling relay',
                'description' => 'Players take turns spelling words letter by letter in a relay format. The first person gives the first letter,
                the word is unknown and can change as long as it exists. A theme can be proposed (animals, foods, countries, etc).',
                'rules' => 'Each player must say one letter of the word in turn. The person who says the last or wrong letter is out.',
                'min_players' => 2,
                'max_players' => 10,
                'scoring' => '10 points per correctly spelled word. Last standing gets 30 bonus points.',
                'tags' => ['Word Game', 'Chill', 'All Ages', 'Travel Friendly'],
            ],

            [
                'title' => 'Human Knot',
                'description' => 'Grab hands in a circle knot, one player is placed in the middle',
                'rules' => 'The player in the middle must get out without using their hands. The outer players must work together to keep him in',
                'min_players' => 4,
                'max_players' => 12,
                'scoring' => '100 points for each minute the player stays in the knot. vice versa.',
                'tags' => ['Team Play', 'Active Game', 'Strategy', 'All Ages'],
            ],
            [
                'title' => 'Balloon Keep-Up',
                'description' => 'Keep the balloon in the air as long as possible.',
                'rules' => 'Use hands, head, or feet. If balloon hits the ground, you’re out.',
                'min_players' => 2,
                'max_players' => 12,
                'scoring' => '10 points per successful hit. if more than one team / balloon, Last standing gets 30 bonus points.',
                'tags' => ['Active Game', 'Funny', 'Quick Play', 'All Ages'],
            ],

            [
                'title' => 'Rope Tug-of-War',
                'description' => 'Two teams pull on opposite ends of a rope. The team that pulls the other over a line wins.',
                'rules' => 'No wrapping the rope around any body parts. Best of three rounds wins.',
                'min_players' => 4,
                'max_players' => 20,
                'scoring' => '30 points per round won. Best of three gets 10 bonus points.',
                'tags' => ['Team Play', 'Active Game', 'Strategy'],
            ],
            [
                'title' => 'Hot Potato',
                'description' => 'Pass an object quickly. Don’t be holding it when the music stops!',
                'rules' => 'Keep passing. Last to hold when music stops is out.',
                'min_players' => 3,
                'max_players' => 15,
                'scoring' => '10 points per round survived. Last standing gets 10 bonus points.',
                'tags' => ['Active Game', 'Funny', 'Quick Play', 'All Ages'],
            ],
            [
                'title' => 'Red Light, Green Light',
                'description' => 'Move on green, freeze on red. First to the finish wins.',
                'rules' => 'Stop immediately on red. Moving on red sends you back to start.',
                'min_players' => 2,
                'max_players' => 12,
                'scoring' => '10 points per round completed. First to finish gets 30 bonus points.',
                'tags' => ['Active Game', 'All Ages', 'Quick Play'],
            ],
            [
                'title' => 'Scavenger Hunt',
                'description' => 'Find items from a list or theme within a time limit.',
                'rules' => 'Collect all items before time is up. First to finish wins.',
                'min_players' => 2,
                'max_players' => 20,
                'scoring' => '10 points per item collected. 30 Bonus points for finishing fastest.',
                'tags' => ['Active Game', 'Team Play', 'All Ages', 'Funny'],
            ],
            [
                
                'title' => 'Telephone / Whisper Down the Lane',
                'description' => 'Whisper a message down a line and see how it changes!',
                'rules' => 'Do not clarify or repeat. Last player says the final message.',
                'min_players' => 4,
                'max_players' => 15,
                'scoring' => '10 points for accurately passing the message. 10 Bonus for funniest message.',
                'tags' => ['Funny', 'Team Play', 'All Ages', 'Chill'],
            ],
            [
                'title' => 'Scattergories',
                'description' => 'Pick a few (about 5) categories (e.g. "Fruits", "Countries") and choose a random letter. Players must name items in each category starting with that letter.',
                'rules' => 'You need to write; 60s for each round.',
                'min_players' => 2,
                'max_players' => 10,
                'scoring' => '10 points per item named. players get only 5 points if another player has the same item though. Highest cumulative score wins.',
                'tags' => ['Word Game', 'Chill', 'All Ages', 'Travel Friendly'],
            ],
        ];

        // Seeding games and attaching tags
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
