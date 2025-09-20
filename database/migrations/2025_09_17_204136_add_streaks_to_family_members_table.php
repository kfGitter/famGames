<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::table('family_members', function (Blueprint $table) {
    $table->integer('daily_streak')->default(0);
    $table->integer('weekly_streak')->default(0);
    $table->timestamp('last_daily_played_at')->nullable();
    $table->timestamp('last_weekly_played_at')->nullable();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family_members', function (Blueprint $table) {
            //
        });
    }
};
