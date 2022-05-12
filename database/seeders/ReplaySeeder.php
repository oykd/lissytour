<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Tournament, Match};

class ReplaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!$this->command->confirm('Replay seeding is a long process. Do you need it this time?', true)) {
            $this->command->info("Replay seeding cancelled by user");
            return;
        }

        // checks
        if (!env('TRANSFER_REPLAY_FOLDER')) {
            $this->command->error("TRANSFER_REPLAY_FOLDER not configured in .env");
            return;
        }

        $transferFolder = null;
        foreach ([env('TRANSFER_REPLAY_FOLDER'), base_path(env('TRANSFER_REPLAY_FOLDER'))] as $folder)
            if (\File::exists($folder)) {
                $transferFolder = $folder;
                break;
            }
        if (!$transferFolder) {
            $this->command->error("TRANSFER_REPLAY_FOLDER not found");
            return;
        }

        if (\Storage::disk('public')->exists('replays')) {
            $this->command->alert('Replay folder already exist. Files will be overwritten!');
            if (!$this->command->confirm('Do you wish to continue?', true)) {
                $this->command->info("Replay seeding cancelled by user");
                return;
            }
        } else
            \Storage::disk('public')->makeDirectory('replays');

        // unzip transferred replays?
        if ($this->command->confirm('Unzip archives in replay transfer folder?', false)) {
            foreach (glob("$transferFolder/*.zip") as $file) {
                $zip = new \ZipArchive();
                if ($zip->open($file) === true) {
                    preg_match('/^[^\d]*(?<tour>\d+)/', basename($file), $matches);
                    if (!$matches['tour']) {
                        $this->command->warn(basename($file) . " has not tournament ID in his name");
                        continue;
                    }
                    $tournament_id = $matches['tour'];
                    $zip->extractTo("$transferFolder/t$tournament_id");
                    $zip->close();
                } else {
                    $this->command->error("Cant open archive for unzip: <$file>");
                    return;
                }
            }
        }

        // import
        \DB::table('replays')->truncate();

        $scbw = GameSeeder::scbw;
        \Storage::disk('public')->makeDirectory("replays/$scbw");

        $loss = [];
        $progressbar = $this->command->getOutput()->createProgressBar(Match::all()->count());
        $progressbar->start();
        \DB::connection('mysql_seed')
            ->table('lis_tourney_match')
            ->chunkById(100, function ($data) use ($transferFolder, $scbw, &$progressbar, &$loss) {
                $replays = [];

                foreach ($data as $match) {
                    // get Tournament
                    $tournament = Tournament::where('id', $match->id_tourney)->first();
                    // get Imported Match ID
                    $importedMatch = Match::where(['tournament_id' => $match->id_tourney, 'line_id' => $match->id_match])->first();
                    if (!$importedMatch || !$tournament) {
                        $loss[] = [$match->id, $match->id_tourney];
                        continue;
                    }
                    $mid = $importedMatch->id;

                    // destroy incorrect symbols in the name and create Tournament Directory
                    $tname = preg_replace('/[^a-zA-Z0-9#\s\-_!&$()\[\].,]/', '', $tournament->name);
                    $tname = trim($tname);
                    $folder = "$scbw/$match->id_tourney.$tname";
                    \Storage::disk('public')->makeDirectory("replays/$folder");

                    // import replays
                    for ($i = 1; $i <= 7; $i++) {
                        $field = "rep$i";
                        if (!$match->$field || $match->$field == '#WO#') continue;
                        $name = '#lost#';
                        $hash = null;
                        foreach (["$transferFolder/t{$match->id_tourney}/{$match->$field}", "$transferFolder/{$match->$field}"] as $file) {
                            if (file_exists($file)) {
                                $hash = md5_file($file);
                                $name = "$folder/t{$match->id_tourney}m{$mid}_$i.rep";
                                \File::copy(
                                    $file,
                                    \Storage::disk('public')->path("replays/$name")
                                );
                                break;
                            }
                        }
                        $replays[] = [
                            'filename' => $name,
                            'hash' => $hash,
                            'match_id' => $mid,
                            'map_id' => null,
                            // ! it's winner of entire match, but should be winner of concrete game...
                            'winner' => $match->score_player1 > $match->score_player2 ? 1 : 2,
                        ];
                    }
                }
                \DB::table('replays')->insert($replays);
                $progressbar->advance(100);
            });

        $progressbar->finish();
        $this->command->info('');
        foreach ($loss as list($id, $tournament)) {
            $this->command->warn("Tournament#$tournament / Match#$id not found");
        }
    }
}
