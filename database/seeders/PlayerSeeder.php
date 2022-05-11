<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('players')->truncate();

        \DB::connection('mysql_seed')
            ->table('lis_tourney_player')
            ->leftJoin('user_info', 'lis_tourney_player.id_user', '=', 'user_info.id')
            ->select(
                'lis_tourney_player.id_user',
                'lis_tourney_player.id_tourney',
                'lis_tourney_player.checkin',
                'lis_tourney_player.description',
                'lis_tourney_player.id',
                'user_info.race'
            )
            ->chunkById(500, function ($data) {
                $players = [];
                foreach ($data as $player) {
                    $players[] = [
                        'tournament_id' => $player->id_tourney,
                        'user_id' => $player->id_user,
                        'checkin' => $player->checkin == 'YES',
                        'name' => $player->description,
                        //'banned' => false,
                        'race_id' => (int)array_search($player->race, RaceSeeder::defaultRaces) + 1,
                    ];
                }
                \DB::table('players')->insert($players);
            }, 'lis_tourney_player.id', 'id');
    }
}
