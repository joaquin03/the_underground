<?php


////////////////////////////////////////////   WRITE CLUBS
$pagenum = 1;
/// OPEN FIRST PAGE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-clubs-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);

//// GET USERS
$query = $db->query("SELECT * FROM towns",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$text = 'https://www.theundergroundsexclub.com/?club='.urlencode($data['suburb']).'&loc='.urlencode($data['state']).'-'.urlencode($data['country']).'';
$stringData = "".$text."\n";
fwrite($fh, $stringData);

///////  PAGE IS FULL
if($x > 48999)
{
////// CLOSE PAGE
fclose($fh);
//////   OPEN NEW PAGE
$pagenum++;
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-clubs-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
$x=0;
}
}

///// ENTRY FOR MAIN SITEMAP PAGE AFTER ALL IS DONE
for ($x = 1; $x <= $pagenum; $x++) {
	$pagelist .= '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-clubs-'.$x.'.txt</loc>
</sitemap>
';
}










///////////////// OPEN  FILE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemap-3.xml";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
///////////////////////////// WRITE HEADER
$text = '<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
$stringData = "".$text."\n";
fwrite($fh, $stringData);
//// BODY
$stringData = "".$pagelist."\n";
fwrite($fh, $stringData);
//////////////// WRITE FOOTER
$text = '</sitemapindex>';
$stringData = "".$text."\n";
fwrite($fh, $stringData);
////////////// CLOSE PAGE
fclose($fh);








///////////////////    SEND SITEMAP
$sitemapUrl = "https://www.theundergroundsexclub.com/sitemap-3.xml";

// cUrl handler to ping the Sitemap submission URLs for Search Engines…
function myCurl($url){
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  return $httpCode;
}

//Google
$url = "http://www.google.com/webmasters/sitemaps/ping?sitemap=".$sitemapUrl;
$returnCode = myCurl($url);

//Bing / MSN
$url = "http://www.bing.com/webmaster/ping.aspx?siteMap=".$sitemapUrl;
$returnCode = myCurl($url);
