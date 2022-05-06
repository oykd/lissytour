<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('languages')->delete();
        Language::insert([
            ['name' => 'English', 'symbol' => 'EN', 'icon_url' => ''],
            ['name' => 'Russian', 'symbol' => 'RU', 'icon_url' => ''],
        ]);
    }
}
