<?php

require 'vendor/autoload.php';

use Goutte\Client;

$client = new Client;

$file = fopen("news.csv", "a");

$crawler = $client->request("GET", "https://highload.today/");

$crawler->filter('.lenta-item')->each(function ($node) {

    $title = $node->filter('.cat-label > a')->text();
    $dateCreated = $node->filter('.meta-datetime')->text();
    $imageUrl = $node->filter('.wp-post-image')->attr('data-lazy-src');
    $desc = $node->filter('p')->text();

    // fputcsv($file, [$dateCreated, $title, $imageUrl, $desc ]);

});
fclose($file);

