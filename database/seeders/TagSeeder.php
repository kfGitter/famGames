<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Quick Play',
            'Strategy',
            'Team Play',
            'All Ages',
            'Parents vs Kids',
            'Card Game',
            'Board Game',
            'Active / Physical',
            'Word Game',
            'Competitive',
            'Trivia & Quiz',
            'Drawing & Creativity',
            'Funny',
            'Chill',
            'Travel Friendly',
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['name' => $tag]);
        }
    }
}
