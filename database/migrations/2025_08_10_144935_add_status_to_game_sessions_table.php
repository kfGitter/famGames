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
        $table->string('status')->default('in_progress')->after('user_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('game_sessions', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
};
