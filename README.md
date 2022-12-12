<h2>Notes:</h2>

<p> I used https://www.gamespot.com/news/ because https://highload.today/ used js to load more articles which which proved rather difficult to scrap like SPA</p>
<p> To run the scrapper set a cronjob by running the following commands:</p>
  <ol>
  <li> EDITOR=nano crontab -e</li>
  <li>0 1 * * * php /opt/scrapper/NewsScrapper.php </li>
  <li>ctrl + x</li>
  </ol>
