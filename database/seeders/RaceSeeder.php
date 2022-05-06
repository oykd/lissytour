<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Race;

class RaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('races')->delete();
        Race::insert(['game_id' => 1, 'name' => 'terran']);
        Race::insert(['game_id' => 1, 'name' => 'zerg']);
        Race::insert(['game_id' => 1, 'name' => 'protoss']);
    }
}
