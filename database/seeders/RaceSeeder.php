<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Race;

class RaceSeeder extends Seeder
{
    const defaultRaces = ['terran', 'zerg', 'protoss', 'random', 'none'];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('races')->truncate();

        foreach (self::defaultRaces as $race)
            Race::insert(['game_id' => 1, 'name' => $race]);
    }
}
