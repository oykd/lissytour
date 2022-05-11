<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MapStackSeeder extends Seeder
{
    const jeezMapStackName = 'Jeez Old School 2021';

    /**
     * Search maps by parts of their names
     *
     * @param array $names
     * @return array
     */
    public static function getMapsByNames(array $names): array
    {
        $R = [];
        foreach ($names as $name) {
            // dismember
            preg_match('#^(?<name>[0-9]*[_|\s]?[a-z]+([_|\s|\-][a-z]+)*)[_|\s]?(?<version>[0-9]+(\.[0-9]+)*)?#i', strtolower(trim($name)), $matches);
            $name = $matches['name'] ?? null;
            $name && $name[0] = strtoupper($name[0]);
            $version = $matches['version'] ?? null;
            preg_match('#\b([v|i|x]+)\b#i', $name, $matches);
            $altVersion = $matches[0] ?? null;
            if ($altVersion) {
                $name = substr($name, 0, strlen($name) - strlen($altVersion) - 1);
                $altVersion = strtoupper($altVersion);
            }
            // search
            $map = \DB::table('maps')->where(['name' => $name, 'version' => $version, 'alt_version' => $altVersion])->first();
            $R[] = $map->id ?? null;
        }
        return $R;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('map_stacks')->truncate();
        \DB::table('map_stack_entries')->truncate();

        $jeez = []; // single-map tournament type
        \DB::connection('mysql_seed')
            ->table('lis_tourney')
            ->chunkById(100, function ($data) use (&$jeez) {
                foreach ($data as $tour) {
                    if (!trim($tour->maps)) continue;

                    // get Category
                    $category = null;
                    foreach (CategorySeeder::defaultCategories as list('name' => $name, 'tag' => $tag)) {
                        if (stripos($tour->name, $tag) !== false) {
                            $category = Category::where('name', $name)->first()->id;
                            break;
                        }
                    }

                    // save stack
                    $msid = null;
                    if (!Str::startsWith($tour->name, 'Jeez')) {
                        \DB::table('map_stacks')->insert([
                            'game_id' => 1,
                            'category_id' => $category,
                            'name' => $tour->name,
                        ]);
                        $msid = \DB::getPdo()->lastInsertId();
                    }

                    // get maps
                    $maps = self::getMapsByNames(explode(',', $tour->maps));
                    foreach ($maps as $map) {
                        if (!isset($map)) {
                            $this->command->warn("Map not found for <$tour->name>");
                            $map = 1; // default map (unknown)
                        }
                        if (Str::startsWith($tour->name, 'Jeez')) {
                            $jeez[] = $map;
                        } else {
                            // save map for stack
                            \DB::table('map_stack_entries')->insert([
                                'stack_id' => $msid,
                                'map_id' => $map,
                            ]);
                        }
                    }
                }
            });

        // save maps for Jeez separately
        \DB::table('map_stacks')->insert([
            'game_id' => 1,
            'category_id' => Category::where('tag', CategorySeeder::jeezTag)->first()->id,
            'name' => self::jeezMapStackName,
        ]);
        $msid = \DB::getPdo()->lastInsertId();
        $jeez = array_map(function ($item) use ($msid) {return ['stack_id' => $msid, 'map_id' => $item]; }, $jeez);
        \DB::table('map_stack_entries')->insert($jeez);
    }
}
