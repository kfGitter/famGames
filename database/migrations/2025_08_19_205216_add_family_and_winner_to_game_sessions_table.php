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
    Schema::table('game_sessions', function (Blueprint $table) {
        $table->foreignId('family_id')->after('game_id')->constrained()->onDelete('cascade');
        $table->foreignId('winner_family_member_id')->nullable()->after('family_id')
              ->constrained('family_members')->nullOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('game_sessions', function (Blueprint $table) {
        $table->dropForeign(['winner_family_member_id']);
        $table->dropColumn('winner_family_member_id');

        $table->dropForeign(['family_id']);
        $table->dropColumn('family_id');
    });
}

};
