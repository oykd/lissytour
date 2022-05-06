<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('races', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->nullable()->constrained('games')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->string('icon_url');
            $table->string('picture_url');
            $table->string('description');
            $table->index('game_id');
            $table->unique(['game_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('races');
    }
}
