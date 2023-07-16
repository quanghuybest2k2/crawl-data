<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;

class ScrapePost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $crawler = GoutteFacade::request('GET', 'https://dantri.com.vn/lao-dong-viec-lam/can-tho-f0-f1-bot-lo-lang-vi-khong-phai-tra-vien-phi-20210829181940605.htm');
        // $title = $crawler->filter('h1.title-page')->each(function ($node) {
        //     return $node->text();
        // })[0];
        // // print($title);

        // $description = $crawler->filter('.singular-sapo')->each(function ($node) {
        //     return $node->text();
        // })[0];
        // $description = str_replace('Dân trí', '', $description);
        // // print($description);

        // $content = $crawler->filter('div.singular-content')->each(function ($node) {
        //     return $node->text();
        // })[0];
        // print($content);

        $crawler = GoutteFacade::request('GET', 'https://dantri.com.vn/lao-dong-viec-lam.htm');
        $linkPost = $crawler->filter('h3.article-title a')->each(function ($node) {
            return $node->attr("href");
        });
        foreach ($linkPost as $link) {
            // print($link);
            $this->scrapeData($link);
        }

        $this->info('Quá trình thu thập dữ liệu đã hoàn tất!');
    }
    public function scrapeData($url)
    {
        $crawler = GoutteFacade::request('GET', $url);

        $title = $this->crawlData('h1.title-page', $crawler);

        $description = $this->crawlData('.singular-sapo', $crawler);

        $description = str_replace('Dân trí', '', $description);

        $content = $this->crawlData('div.singular-content', $crawler);

        $dataPost = [
            'title' => $title,
            'description' => $description,
            'content' => $content
        ];

        Post::create($dataPost);
    }

    protected function crawlData(string $type, $crawler)
    {
        $result = $crawler->filter($type)->each(function ($node) {
            return $node->text();
        });

        if (!empty($result)) {
            return $result[0];
        }

        return '';
    }
}
