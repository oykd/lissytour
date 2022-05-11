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

        /**
         *  Seed categories
         *      !! Table <categories> will be cleared
         */
        $this->call(CategorySeeder::class);

        /**
         *  Transfer pages from old base
         *  html => BBCodes
         *      !! Table <pages> will be cleared
         */
        $this->call(PageSeeder::class);

        /**
         *  Generate <maps> from files
         *  .env => TRANSFER_MAP_FOLDER - path to the old map folder
         *      !! Table <maps> will be cleared
         *      !! Directory "/storage/app/public/maps/" will be overwritten
         */
        $this->call(MapSeeder::class);

        /**
         *  Generate <map_stacks> and <map_stack_entries> from old <tourney> table
         *      !! Table <map_stacks> will be cleared
         *      !! Table <map_stack_entries> will be cleared
         */
        $this->call(MapStackSeeder::class);

        \Schema::enableForeignKeyConstraints();
    }
}
