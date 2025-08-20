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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();          // e.g. FIRST_WIN, PLAY_10
            $table->string('name');                    // e.g. "First Win!"
            $table->string('icon')->nullable();        // optional sticker filename
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('family_member_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_member_id')->constrained()->onDelete('cascade');
            $table->foreignId('achievement_id')->constrained()->onDelete('cascade');
            $table->timestamp('awarded_at');
            $table->unique(['family_member_id', 'achievement_id']);
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::dropIfExists('family_member_achievements');
        Schema::dropIfExists('achievements');
    }
};
