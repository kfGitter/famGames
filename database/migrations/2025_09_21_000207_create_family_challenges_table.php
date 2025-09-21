<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('family_challenges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('family_id')->index();
            $table->unsignedBigInteger('challenge_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('type')->default('custom'); // daily, weekly, hidden, custom...
            $table->text('description')->nullable();
            $table->unsignedInteger('goal')->default(1); // goal number (e.g. 5 games)
            $table->unsignedInteger('progress')->default(0); // current progress
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->foreign('family_id')->references('id')->on('families')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_challenges');
    }
};
