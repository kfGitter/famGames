<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('game_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('family_id')->constrained()->onDelete('cascade');
            $table->foreignId('family_member_id')->constrained()->onDelete('cascade');
            $table->integer('score');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('game_scores');
    }
};
