<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    
    public function up()
{
    Schema::table('game_scores', function (Blueprint $table) {
        $table->unique(['game_session_id', 'family_member_id']);
    });
}

    /**
     * Reverse the migrations.
     */

    public function down()
{
    Schema::table('game_scores', function (Blueprint $table) {
        $table->dropUnique(['game_session_id', 'family_member_id']);
    });
}

};
