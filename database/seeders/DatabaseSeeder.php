<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \Schema::disableForeignKeyConstraints();

        /**
         *  Transfer users from old base
         *      !! Table <users> will be cleared
         *      !! Table <profiles> will be cleared
         */
        $this->call(UserSeeder::class);

        /**
         *  Seed default game
         *      !! Table <games> will be cleared
         */
        $this->call(GameSeeder::class);

        /**
         *  Seed main languages
         *      !! Table <languages> will be cleared
         */
        $this->call(LanguageSeeder::class);

        /**
         *  Seed default team
         *      !! Table <teams> will be cleared
         *      master group will be created with single user (id = 1)
         */
        $this->call(TeamSeeder::class);

        /**
         *  Seed currencies
         *      !! Table <currencies> will be cleared
         */
        $this->call(CurrencySeeder::class);

        /**
         *  Seed timezones
         *      !! Table <timezones> will be cleared
         */
        $this->call(TimezoneSeeder::class);

        /**
         *  Seed races
         *      !! Table <races> will be cleared
         */
        $this->call(RaceSeeder::class);

        \Schema::enableForeignKeyConstraints();
    }
}
