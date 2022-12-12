<?php


require 'vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Goutte\Client;

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "news";


function scrapePage($url, $client, $conn)
{

    $sql = "SELECT 'title' FROM `news`";
    $Existingresult = (array) $conn->query($sql);

    $crawler = $client->request('GET', $url);

    $crawler->filter('.card-item')->each(function ($node) use ($conn, $Existingresult) {
        $title = $node->filter('.card-item__title')->text();
        $dateCreated = $node->filter('.symbol-text > time')->text();
        $imageUrl = $node->filter('img')->attr('src');
        $comments = $node->filter('.symbol-text > span')->text();

        if (in_array($title, $Existingresult, TRUE)) {
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
$url = "https://www.gamespot.com/news/";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

while ($url) {
    echo $url.PHP_EOL;
    $url = scrapePage($url, $client, $conn);
}
$conn->close();