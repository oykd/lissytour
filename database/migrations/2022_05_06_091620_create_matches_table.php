<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->nullable()->constrained('tournaments')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('line_id');
            $table->integer('round_id');
            $table->string('round_name');
            $table->foreignId('player1_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('player2_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->unsignedTinyInteger('score_win');
            $table->foreignId('winner_goto')->nullable()->constrained('matches')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('looser_goto')->nullable()->constrained('matches')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedSmallInteger('winner_top')->nullable();
            $table->unsignedSmallInteger('looser_top')->nullable();

            $table->index('tournament_id');
            $table->index('player1_id');
            $table->index('player2_id');
            $table->unique(['tournament_id', 'line_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matches');
    }
}
