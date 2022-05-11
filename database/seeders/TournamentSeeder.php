<?php

namespace Database\Seeders;

use App\Models\MapStack;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TournamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('tournaments')->truncate();

        \DB::connection('mysql_seed')
            ->table('lis_tourney')
            ->chunkById(100, function ($data) {
                $tournaments = [];
                foreach ($data as $tour) {

                    // get Category
                    $category = null;
                    foreach (CategorySeeder::defaultCategories as list('name' => $name, 'tag' => $tag)) {
                        if (stripos($tour->name, $tag) !== false) {
                            $category = Category::where('name', $name)->first()->id;
                            break;
                        }
                    }

                    // get Prizes
                    $prizes = explode(',', $tour->prize_pool);
                    if (count($prizes) < 3) {
                        $currency = $pool = $rate = null;
                        if (count($prizes) == 2)
                            $this->command->warn("<$tour->name> has incorrect prize structure <$tour->prize_pool>");
                    } else {
                        $currency = $prizes[0] == '$' || $prizes[count($prizes) - 1] == '$' ? 1 : null;
                        $pool = implode(',', array_splice($prizes, 1, count($prizes) - 2));
                        $rate = $currency ? 1.0 : 0;
                    }

                    // get MapStack
                    $msid = MapStack::where(
                        'name',
                        Str::startsWith($tour->name, 'Jeez') ? MapStackSeeder::jeezMapStackName : $tour->name
                    )->first();
                    $msid = $msid->id ?? null;

                    // add to insert list
                    $tournaments[] = [
                        'name' => $tour->name,
                        //'number' => null,
                        'creator_id' => $tour->admin_id ?: 1,
                        'game_id' => 1,
                        'team_id' => null,
                        'place' => $tour->place,
                        'registration_time' => Carbon::createFromTimestamp($tour->time_reg),
                        'checkin_time' => Carbon::createFromTimestamp($tour->time_checkin),
                        'start_time' => Carbon::createFromTimestamp($tour->time_start),
                        'prize_pool' => $pool,
                        'prize_currency' => $currency,
                        'prize_rate' => $rate,
                        'visible' => $tour->visible == 'VISIBLE',
                        'state' => $tour->state,
                        'logo_url' => $tour->logo_link,
                        'rules_url' => $tour->rules_link,
                        'vod_url' => $tour->vod_link,
                        'mapstack_id' => $msid,
                        'map_selection' => $msid ? $tour->map_selecttype : 'NONE',
                        'rated' => $tour->is_ranking == 'YES',
                        'importance' => $tour->importance,
                        'category_id' => $category,
                        'chat_id' => null,
                        'password' => $tour->password,
                    ];
                }

                \DB::table('tournaments')->insert($tournaments);
            });
    }
}
