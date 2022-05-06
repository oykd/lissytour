<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapStackEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('map_stack_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stack_id')->constrained('map_stacks')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('map_id')->constrained('maps')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('map_stack_entries');
    }
}
