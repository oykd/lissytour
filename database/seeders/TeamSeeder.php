<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('teams')->truncate();
        Team::insert(['name' => 'master', 'description' => 'Default super-group for initial admin',]);

        \DB::table('team_entries')->truncate();
        \DB::table('team_entries')->insert(['team_id' => 1, 'user_id' => 1, 'level' => 0b1111]);
    }
}
