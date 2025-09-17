<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Family Streaks
    |--------------------------------------------------------------------------
    | Rules for streaks that apply to the whole family.
    | Example: keep playing at least once per week.
    */
    'family' => [
        'weekly' => [
            'key' => 'family_weekly_streak',
            'label' => 'Family Weekly Streak',
            'interval' => 'week',      // reset window
            'threshold' => 1,          // min sessions needed per interval
            'start_after' => 1,        // start counting streaks after 1 full week
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Member Streaks
    |--------------------------------------------------------------------------
    | Rules for streaks that apply to individual family members.
    | These can be used to trigger personal achievements.
    */
    'member' => [
        'daily' => [
            'key' => 'member_daily_streak',
            'label' => 'Daily Streak',
            'interval' => 'day',
            'threshold' => 1,          // must play every day
            'start_after' => 2,        // "Streak Master" starts after day 2
        ],
        'weekly' => [
            'key' => 'member_weekly_streak',
            'label' => 'Weekly Streak',
            'interval' => 'week',
            'threshold' => 1,
            'start_after' => 1,        // "Streak Boss" starts after week 1
        ],
    ],

];
