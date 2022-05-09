<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->truncate();
        \DB::table('profiles')->truncate();

        \DB::connection('mysql_seed')
            ->table('user')
            ->leftJoin('user_info', 'user.id', '=', 'user_info.id')
            ->select('user.id', 'user.login', 'user.password', 'user_info.aka', 'user_info.email', 'user_info.race')
            ->chunkById(100, function ($data) {
                $users = [];
                $profiles = [];
                foreach ($data as $user) {
                    $users[] = [
                        'id' => $user->id,
                        'name' => $user->login,
                        'email' => $user->email ?: '-',
                        //'email_verified_at'=> now(),
                        'password' => $user->password,
                    ];
                    $profiles[] = [
                        'id' => $user->id,
                        'nickname' => $user->aka ?: null,
                        'race_id' => (int)array_search($user->race, RaceSeeder::defaultRaces) + 1,
                    ];
                }
                \DB::table('users')->insert($users);
                \DB::table('profiles')->insert($profiles);
            }, 'user.id', 'id');
    }
}
