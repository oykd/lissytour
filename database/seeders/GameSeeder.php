<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('games')->truncate();
        Game::insert(['name' => 'StarCraft:BroodWar', 'short' => 'SC:BW']);
    }
}
