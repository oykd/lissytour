<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!env('TRANSFER_MAP_FOLDER')) {
            $this->command->error("TRANSFER_MAP_FOLDER not configured in .env");
            return;
        }

        $transferFolder = null;
        foreach ([env('TRANSFER_MAP_FOLDER'), base_path(env('TRANSFER_MAP_FOLDER'))] as $folder)
            if (\File::exists($folder)) {
                $transferFolder = $folder;
                break;
            }
        if (!$transferFolder) {
            $this->command->error("TRANSFER_MAP_FOLDER not found");
            return;
        }

        if (\Storage::disk('public')->exists('maps')) {
            $this->command->alert('Map folder already exist. Files will be overwritten!');
            if(!$this->command->confirm('Do you wish to continue?',true)) {
                $this->command->info("Map seeding cancelled by user");
                return;
            }
        } else
            \Storage::disk('public')->makeDirectory('maps');

        \DB::table('maps')->truncate();

        $scbw = GameSeeder::scbw;
        \Storage::disk('public')->makeDirectory("maps/$scbw");
        $mapCounter = 0;
        $pics = 0;
        $maps = [];
        $rewritten = 0;
        foreach (glob("$transferFolder/norm/*.sc[x|m]") as $file) {
            try {
                // dismember map-name
                $filename = basename($file);
                preg_match('#^(?<name>[0-9]*[_|\s]?[a-z]+([_|\s|\-][a-z]+)*)[_|\s]?(?<version>[0-9]+(\.[0-9]+)*)?.*\.(?<ext>sc[x|m])$#i', $filename, $matches);
                if (!isset($matches['name'], $matches['ext'])) {
                    $this->command->error("Bad map name: <$filename>");
                    continue;
                }
                $name = $matches['name'];
                $name[0] = strtoupper($name[0]);
                $extension = $matches['ext'];
                $version = $matches['version'] ?? null;
                #$name = preg_replace('#\b([v|i|x]+)\b#ie', 'strtoupper("$0")', $name);
                preg_match('#\b([v|i|x]+)\b#i', $name, $matches);
                $altVersion = $matches[0] ?? null;
                if ($altVersion && $version)
                    $this->command->warn("Map <$filename> has two versions");
                if ($altVersion) {
                    $name = substr($name, 0, strlen($name) - strlen($altVersion) - 1);
                    $altVersion = strtoupper($altVersion);
                }
                // Clear map folder if exists
                if (!in_array($name, $maps) && \Storage::disk('public')->exists("maps/$scbw/$name")) {
                    \Storage::disk('public')->deleteDirectory("maps/$scbw/$name");
                    $rewritten++;
                }
                // copy map file
                $postfix = '';
                array_map(function ($item) use (&$postfix){ $item && $postfix .= ' ' . $item; }, [$altVersion, $version]);
                \Storage::disk('public')->makeDirectory("maps/$scbw/$name");
                \File::copy(
                    $file,
                    \Storage::disk('public')->path("maps/$scbw/$name/$name$postfix.$extension")
                );
                // copy obs version
                $title = pathinfo($file, PATHINFO_FILENAME);
                if ($obs = glob("$transferFolder/obs/${title}_ob.sc[x|m]")) {
                    \File::copy(
                        $obs[0],
                        \Storage::disk('public')->path("maps/$scbw/$name/$name$postfix.obs.$extension")
                    );
                }
                // copy map pictures
                $picture = null;
                if (\File::exists("$transferFolder/jpg1024/$title.jpg")) {
                    \File::copy(
                        "$transferFolder/jpg1024/$title.jpg",
                        \Storage::disk('public')->path("maps/$scbw/$name/$name$postfix.jpg")
                    );
                    $pics++;
                } else
                    $this->command->warn("Picture not found for map <$filename>");
                if (\File::exists("$transferFolder/jpg256/$title.jpg")) {
                    \File::copy(
                        "$transferFolder/jpg256/$title.jpg",
                        \Storage::disk('public')->path("maps/$scbw/$name/$name$postfix.256px.jpg")
                    );
                }
                // save to DB
                \DB::table("maps")->insert([
                    'game_id' => 1,
                    'name' => $name,
                    'version' => $version,
                    'alt_version' => $altVersion,
                    'picture' => "$name$postfix.jpg",
                ]);
                $maps[] = $name;
                $mapCounter++;
            } catch (\Exception $e) {
                $filename = basename($file);
                $this->command->error($e->getMessage());
                $this->command->info("Map <$filename> was skipped");
            }
        }
        $this->command->info("Maps: $mapCounter | Pictures: $pics | Rewritten: $rewritten");
    }
}
