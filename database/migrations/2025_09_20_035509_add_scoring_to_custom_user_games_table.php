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
        Schema::table('custom_user_games', function (Blueprint $table) {
            $table->text('scoring')->nullable()->after('rules'); // Add scoring column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_user_games', function (Blueprint $table) {
            $table->dropColumn('scoring');
        });
    }
};
