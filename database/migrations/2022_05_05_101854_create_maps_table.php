<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('name');
            $table->string('version')->default('1.0');
            $table->string('picture')->nullable();
            $table->string('icon')->nullable();
            $table->text('description')->default('');

            $table->index('name');
            $table->unique(['name', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maps');
    }
}
