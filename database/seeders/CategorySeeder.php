<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    const jeezTag = 'jeez';
    const defaultCategories = [
        ['name' => 'Defiler Classic Tournaments', 'tag' => 'defiler'],
        ['name' => 'Gaz Tournaments', 'tag' => 'gaz'],
        ['name' => 'Fast Mini Tournaments', 'tag' => 'mini'],
        ['name' => 'Jeez', 'tag' => self::jeezTag],
        ['name' => '2x2 tournaments', 'tag' => '2x2'],
        ['name' => 'Unusual Starcraft', 'tag' => 'perversion'],
        ['name' => 'Russian League', 'tag' => 'russian'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('categories')->truncate();
        Category::insert(self::defaultCategories);
    }
}
