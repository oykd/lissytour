<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('creator_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('game_id')->nullable()->constrained('games')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams')->cascadeOnUpdate()->nullOnDelete();
            $table->string('place');
            //$table->foreignId('timezone_id')->nullable()->constrained('timezones')->cascadeOnUpdate()->nullOnDelete();
            $table->dateTime('registration_time')->useCurrent();
            $table->dateTime('checkin_time')->nullable();
            $table->dateTime('start_time');
            $table->string('prize_pool')->nullable();
            $table->foreignId('prize_currency')->nullable()->constrained('currencies')->cascadeOnUpdate()->nullOnDelete();
            $table->float('prize_rate')->nullable();
            $table->boolean('visible')->default(true);
            $table->enum('state', ['VOID', 'ANNOUNCE', 'REGISTRATION', 'CHECK-IN', 'GENERATION', 'STARTED', 'FINISHED'])->default('ANNOUNCE');
            $table->string('logo_url')->nullable();
            $table->string('rules_url')->nullable();
            $table->string('vod_url')->nullable();
            $table->foreignId('mapstack_id')->constrained('map_stacks')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('map_selection', ['FIRSTBYROUND', 'FIRSTBYREMOVING'])->default('FIRSTBYROUND');
            $table->boolean('rated')->default(false);
            $table->integer('importance')->default(0);
            $table->foreignId('category_id')->constrained('categories')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('chat_id')->nullable()->constrained('chats')->cascadeOnUpdate()->nullOnDelete();
            $table->string('password')->nullable();

            $table->index('creator_id');
            $table->index('game_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournaments');
    }
}
