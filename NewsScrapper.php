<?php

require 'vendor/autoload.php';

use Goutte\Client;

function scrapePage($url, $client, $file)
{
    $crawler = $client->request('GET', $url);

    $crawler->filter('.card-item')->each(function ($node) use ($file) {
        $title = $node->filter('.card-item__title')->text();
        $dateCreated = $node->filter('.symbol-text > time')->text();
        $imageUrl = $node->filter('img')->attr('src');
        $comments = $node->filter('.symbol-text > span')->text();

        fputcsv($file, [$title, $dateCreated, $imageUrl,  $comments ]);
    });

    try {
        $next_page = $crawler->filter('.next > a')->attr('href');
    } catch (\Throwable $th) {
        return null;
    }

    return "https://www.gamespot.com" . $next_page;
}

$client = new Client();
$file = fopen("news.csv", "w");
$url = "https://www.gamespot.com/news/";

while ($url) {
    echo "<h2>" . $url . "</h2>" . PHP_EOL;
    $url = scrapePage($url, $client, $file);
}
fclose($file);


