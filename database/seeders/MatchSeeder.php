<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Match;

class MatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!$this->command->confirm('Match seeding is a long process. Do you need it this time?', true)) {
            $this->command->info("Match seeding cancelled by user");
            return;
        }

        \DB::table('matches')->truncate();

        $this->command->info('Importing matches...');

        \DB::connection('mysql_seed')
            ->table('lis_tourney_match')
            ->chunkById(100, function ($data) {
                $matches = [];
                foreach ($data as $match) {
                    if ($match->round_id == 0) {
                        $this->command->warn("Match #$match->id for Tournament #$match->id_tourney is not correct!");
                        continue;
                    }
                    $matches[] = [
                        //'id' => $match->id,
                        'tournament_id' => $match->id_tourney,
                        'line_id' => $match->id_match,
                        'round_id' => $match->round_id,
                        'round_name' => $match->round,
                        'player1_id' => $match->id_player1 == -1 ? null : $match->id_player1,
                        'player2_id' => $match->id_player2 == -1 ? null : $match->id_player2,
                        'score_win' => $match->score_win,

                        //its line_id, but later must be match_id
                        'winner_goto' => $match->winner_action == 'TOP' ? null : $match->winner_value,
                        'looser_goto' => $match->looser_action == 'TOP' ? null : $match->looser_value,

                        'winner_top' => $match->winner_action == 'TOP' ? $match->winner_value : null,
                        'looser_top' => $match->looser_action == 'TOP' ? $match->looser_value : null,
                        'walkover' => $match->rep1 == '#WO#' ? ($match->score_player1 > $match->score_player2 ? 1 : 2) : null,
                    ];
                }
                \DB::table('matches')->insert($matches);
            });

        $this->command->info('Connecting matches...');

        // Correct goto values
        $lost = [];
        $progressbar = $this->command->getOutput()->createProgressBar(Match::all()->count());
        $progressbar->start();
        \DB::table('matches')
            ->chunkById(1000, function ($data) use (&$progressbar, &$lost) {
                foreach ($data as $match) {
                    $connections = ['winner' => $match->winner_goto, 'looser' => $match->looser_goto];
                    foreach ($connections as &$goto) {
                        if ($goto === null) continue;
                        $destination = Match::where(['tournament_id' => $match->tournament_id, 'line_id' => $goto])->first();
                        if (!$destination) {
                            $lost[] = [$match->tournament_id, $match->id, $goto];
                            continue 2;
                        }
                        $goto = $destination->id;
                    }
                    Match::where('id', $match->id)->update(['winner_goto' => $connections['winner'], 'looser_goto' => $connections['looser']]);
                }
                $progressbar->advance(1000);
            });
        $progressbar->finish();
        $this->command->info('');
        foreach ($lost as list($tournament_id, $match_id, $goto)) {
            $this->command->warn("Tournament #$tournament_id match #$match_id lost connection <$goto>");
        }
    }
}
