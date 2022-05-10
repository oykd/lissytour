<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('pages')->truncate();

        \DB::connection("mysql_seed")
            ->table("lis_page")
            ->chunkById(100, function ($data) {
                $pages = [];
                foreach ($data as $page) {
                    $pages[] = [
                        'creator_id' => $page->id_user,
                        'language_id' => (int)array_search($page->language, ['language.english.php', 'language.russian.php']) + 1,
                        'link' => $page->id,
                        'title' => $page->title,
                        'content' => $this->replaceHtmlWithBBCodes($page->text),
                    ];
                }
                \DB::table("pages")->insert($pages);
            });
    }

    /**
     * Replace some html tags with BB codes
     *
     * @param string $html
     * @return string
     */
    protected function replaceHtmlWithBBCodes(string $html): string
    {
        $html = preg_replace('#<style>.*</style>#i', '', $html);
        $html = preg_replace('#<(/?)(b|i|u|DD|big|br|h[0-9])/?>#i', '[$1$2]', $html);
        $html = preg_replace('#<(/?)ul>#i', '[$1list]', $html);
        $html = preg_replace('#<li>#i', '[*]', $html);
        $html = preg_replace('#<\/li>#i', '', $html);
        //$html = preg_replace('#<a href="([^"]+)">([^<]+)</a>#i',"[url=$1]$2[/url]", $html);
        $html = preg_replace('#<a(\s+target\s*=\s*"[^"]+")?\s+href\s*=\s*"([^"]+)">([^<]+)<\/a>#i', '[url=$2]$3[/url]', $html);
        $html = strip_tags($html);
        return $html;
    }
}
