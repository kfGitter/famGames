<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStreaksTable extends Migration
{
    public function up()
    {
        Schema::create('streaks', function (Blueprint $table) {
            $table->id();
            $table->morphs('streakable'); // streakable_type, streakable_id
            $table->enum('cadence', ['daily', 'weekly']); // daily or weekly
            $table->unsignedInteger('count')->default(0);  // current consecutive count
            $table->unsignedInteger('best')->default(0);   // best ever
            $table->date('last_date')->nullable();         // last date we updated (date only)
            $table->timestamp('started_at')->nullable();   // when this streak started
            $table->timestamps();

            $table->unique(['streakable_type','streakable_id','cadence']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('streaks');
    }
}
