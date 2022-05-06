<?php

namespace Database\Seeders;

use App\Models\Timezone;
use Illuminate\Database\Seeder;

class TimezoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('timezones')->delete();
        Timezone::insert(['name' => 'Central European Time', 'symbol' => 'CET']);
        Timezone::insert(['name' => 'Russia, Moscow', 'symbol' => 'MSK']);
        Timezone::insert(['name' => 'Korea, Seoul', 'symbol' => 'KOR']);
    }
}
