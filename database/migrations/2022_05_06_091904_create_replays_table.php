<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReplaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replays', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('hash')->unique();
            $table->foreignId('match_id')->nullable()->constrained('matches')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('map_id')->nullable()->constrained('maps')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedTinyInteger('winner');

            $table->index('match_id');
            $table->index('hash');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replays');
    }
}
