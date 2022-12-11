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

        // store data in a received array

        fputcsv($file, [$title, $dateCreated, $imageUrl,  $comments ]);

        // SQL command to select title and id   SELECT `id`, `title` FROM `news` WHERE 1
        // check if the result if not null, if null skip to adding to database, if not null store values in an current extisiting array
        // use for loop to see if the data incoming exists in our current existing array if not intsert the value to the db table
        // SQL command to insert  INSERT INTO `news`(`id`, `title`, `image`, `date_added`, `comment_count`) VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]')
        // if it is existing check the update the date_added and the comment count
        // SQL update Database UPDATE `news` SET `date_added`='[value-4]',`comment_count`='[value-5]' WHERE `id`= id

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


