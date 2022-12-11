<?php

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "news";



require 'vendor/autoload.php';


use Goutte\Client;

function scrapePage($url, $client, $file, $conn)
{

    $sql = "SELECT 'title' FROM `news`";
    $dataRes = array();
    $Existingresult = $conn->mysql_query($sql);

    print($Existingresult);
    
    $crawler = $client->request('GET', $url);

    $crawler->filter('.card-item')->each(function ($node) use ($conn, $dataRes) {
        $title = $node->filter('.card-item__title')->text();
        $dateCreated = $node->filter('.symbol-text > time')->text();
        $imageUrl = $node->filter('img')->attr('src');
        $comments = $node->filter('.symbol-text > span')->text();

        if (in_array($title, $dataRes, TRUE)) {
            $updatesql = "UPDATE `news` SET `date_added`='$dateCreated' ,`comment_count`= '$comments' WHERE `title`= '$title'";
              $conn->query($updatesql);
        } else {
             $instertSql = "INSERT INTO `news`(`id`,`title`, `image`, `date_added`, `comment_count`) VALUES (NULL,'$title', '$imageUrl', '$dateCreated' ,'$comments')";
              $conn->query($instertSql);
        }

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
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

while ($url) {
    echo "<h2>" . $url . "</h2>" . PHP_EOL;
    $url = scrapePage($url, $client, $file, $conn);
}
$conn->close();
fclose($file);