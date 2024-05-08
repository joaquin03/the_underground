<?php
//// OPEN FIRST SITEMAP
////////////////////////////////////////////// WRITE STATIC PAGES NO CHANGE CONTENT
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-main.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = 'https://www.theundergroundsexclub.com/
https://www.theundergroundsexclub.com/?mod=members
https://www.theundergroundsexclub.com/?sex=Any&from=From+Age&sex_pref=Any&to=To+Age&sex_relstatus=Any&country=&keywords=&file=search&mod=members&button=Search
https://www.theundergroundsexclub.com/?mod=groups
https://www.theundergroundsexclub.com/?mod=galleries
https://www.theundergroundsexclub.com/?mod=personals
https://www.theundergroundsexclub.com/?mod=forum
https://www.theundergroundsexclub.com/?mod=stories
https://www.theundergroundsexclub.com/?mod=feed
https://www.theundergroundsexclub.com/?mod=groups&cat=27&tag=
https://www.theundergroundsexclub.com/?mod=groups&cat=28&tag=
https://www.theundergroundsexclub.com/?mod=groups&cat=29&tag=';
$stringData = "".$text."\n";
fwrite($fh, $stringData);
///// ENTRY FOR MAIN SITEMAP PAGE
$pagelist = '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-main.txt</loc>
<lastmod>'.date('Y-m-d', $time).'</lastmod>
</sitemap>';











////////////////////////////////////////////   WRITE GROUPS
$pagenum = 1;
/// OPEN FIRST PAGE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-groups-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);

//// GET DATA
$query = $db->query("SELECT id FROM groups ORDER BY id ASC",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$text = 'https://www.theundergroundsexclub.com/?g='.$data['id'].'';
$stringData = "".$text."\n";
fwrite($fh, $stringData);

///////  PAGE IS FULL
if($x > 48999)
{
////// CLOSE PAGE
fclose($fh);
//////   OPEN NEW PAGE
$pagenum++;
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-groups-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
$x=0;
}
}

///// ENTRY FOR MAIN SITEMAP PAGE AFTER ALL IS DONE
for ($x = 1; $x <= $pagenum; $x++) {
	$pagelist .= '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-groups-'.$x.'.txt</loc>
<lastmod>'.date('Y-m-d', $time).'</lastmod>
</sitemap>';
}










////////////////////////////////////////////   WRITE STORIES
$pagenum = 1;
/// OPEN FIRST PAGE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-stories-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);

//// GET DATA
$query = $db->query("SELECT id FROM stories ORDER BY id ASC",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$text = 'https://www.theundergroundsexclub.com/?s='.$data['id'].'';
$stringData = "".$text."\n";
fwrite($fh, $stringData);

///////  PAGE IS FULL
if($x > 48999)
{
////// CLOSE PAGE
fclose($fh);
//////   OPEN NEW PAGE
$pagenum++;
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-stories-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
$x=0;
}
}

///// ENTRY FOR MAIN SITEMAP PAGE AFTER ALL IS DONE
for ($x = 1; $x <= $pagenum; $x++) {
	$pagelist .= '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-stories-'.$x.'.txt</loc>
<lastmod>'.date('Y-m-d', $time).'</lastmod>
</sitemap>';
}











////////////////////////////////////////////   WRITE STORY CATEGORIES
$pagenum = 1;
/// OPEN FIRST PAGE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-storycategories-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);

//// GET DATA
$query = $db->query("SELECT id FROM storycategories ORDER BY id ASC",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$text = 'https://www.theundergroundsexclub.com/?mod=stories&file=category&id='.$data['id'].'';
$stringData = "".$text."\n";
fwrite($fh, $stringData);

///////  PAGE IS FULL
if($x > 48999)
{
////// CLOSE PAGE
fclose($fh);
//////   OPEN NEW PAGE
$pagenum++;
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-storycategories-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
$x=0;
}
}

///// ENTRY FOR MAIN SITEMAP PAGE AFTER ALL IS DONE
for ($x = 1; $x <= $pagenum; $x++) {
	$pagelist .= '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-storycategories-'.$x.'.txt</loc>
<lastmod>'.date('Y-m-d', $time).'</lastmod>
</sitemap>';
}















////////////////////////////////////////////   WRITE GALLERIES
$pagenum = 1;
/// OPEN FIRST PAGE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-galleries-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);

//// GET USERS
$query = $db->query("SELECT id FROM galleries WHERE completed = 'y' ORDER BY id ASC",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$text = 'https://www.theundergroundsexclub.com/?p='.$data['id'].'';
$stringData = "".$text."\n";
fwrite($fh, $stringData);

///////  PAGE IS FULL
if($x > 48999)
{
////// CLOSE PAGE
fclose($fh);
//////   OPEN NEW PAGE
$pagenum++;
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-galleries-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
$x=0;
}
}

///// ENTRY FOR MAIN SITEMAP PAGE AFTER ALL IS DONE
for ($x = 1; $x <= $pagenum; $x++) {
	$pagelist .= '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-galleries-'.$x.'.txt</loc>
<lastmod>'.date('Y-m-d', $time).'</lastmod>
</sitemap>';
}













////////////////////////////////////////////   WRITE FEED ITEMS
$pagenum = 1;
/// OPEN FIRST PAGE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-feeditems-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);

//// GET DATA
$query = $db->query("SELECT id FROM feed ORDER BY id ASC",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$text = 'https://www.theundergroundsexclub.com/?item='.$data['id'].'';
$stringData = "".$text."\n";
fwrite($fh, $stringData);

///////  PAGE IS FULL
if($x > 48999)
{
////// CLOSE PAGE
fclose($fh);
//////   OPEN NEW PAGE
$pagenum++;
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-feeditems-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
$x=0;
}
}

///// ENTRY FOR MAIN SITEMAP PAGE AFTER ALL IS DONE
for ($x = 1; $x <= $pagenum; $x++) {
	$pagelist .= '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-feeditems-'.$x.'.txt</loc>
<lastmod>'.date('Y-m-d', $time).'</lastmod>
</sitemap>';
}











////////////////////////////////////////////   WRITE PERSONALS
$pagenum = 1;
/// OPEN FIRST PAGE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-personals-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);

//// GET DATA
$query = $db->query("SELECT id FROM classifieds WHERE delstamp = 0 ORDER BY id ASC",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$text = 'https://www.theundergroundsexclub.com/?a='.$data['id'].'';
$stringData = "".$text."\n";
fwrite($fh, $stringData);

///////  PAGE IS FULL
if($x > 48999)
{
////// CLOSE PAGE
fclose($fh);
//////   OPEN NEW PAGE
$pagenum++;
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-personals-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
$x=0;
}
}

///// ENTRY FOR MAIN SITEMAP PAGE AFTER ALL IS DONE
for ($x = 1; $x <= $pagenum; $x++) {
	$pagelist .= '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-personals-'.$x.'.txt</loc>
<lastmod>'.date('Y-m-d', $time).'</lastmod>
</sitemap>';
}









/*

////////////////////////////////////////////   WRITE PERSONAL COUNTRIES
$pagenum = 1;
/// OPEN FIRST PAGE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-personals-countries-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);

//// GET DATA
$query = $db->query("SELECT id FROM loc_countries ORDER BY id ASC",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$text = 'https://www.theundergroundsexclub.com/?mod=personals&co='.$data['id'].'';
$stringData = "".$text."\n";
fwrite($fh, $stringData);

///////  PAGE IS FULL
if($x > 48999)
{
////// CLOSE PAGE
fclose($fh);
//////   OPEN NEW PAGE
$pagenum++;
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-personals-countries-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
$x=0;
}
}

///// ENTRY FOR MAIN SITEMAP PAGE AFTER ALL IS DONE
for ($x = 1; $x <= $pagenum; $x++) {
	$pagelist .= '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-personals-countries-'.$x.'.txt</loc>
<lastmod>'.date('Y-m-d', $time).'</lastmod>
</sitemap>';
}











////////////////////////////////////////////   WRITE PERSONAL STATES
$pagenum = 1;
/// OPEN FIRST PAGE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-personals-states-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);

//// GET DATA
$query = $db->query("SELECT id,country FROM loc_states ORDER BY id ASC",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$text = 'https://www.theundergroundsexclub.com/?mod=personals&co='.$data['country'].'&st='.$data['id'].'';
$stringData = "".$text."\n";
fwrite($fh, $stringData);

///////  PAGE IS FULL
if($x > 48999)
{
////// CLOSE PAGE
fclose($fh);
//////   OPEN NEW PAGE
$pagenum++;
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-personals-states-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
$x=0;
}
}

///// ENTRY FOR MAIN SITEMAP PAGE AFTER ALL IS DONE
for ($x = 1; $x <= $pagenum; $x++) {
	$pagelist .= '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-personals-states-'.$x.'.txt</loc>
<lastmod>'.date('Y-m-d', $time).'</lastmod>
</sitemap>';
}








////////////////////////////////////////////   WRITE PERSONAL AREAS
$pagenum = 1;
/// OPEN FIRST PAGE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-personals-areas-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);

//// GET DATA
$query = $db->query("SELECT id,country,state FROM loc_areas ORDER BY id ASC",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$text = 'https://www.theundergroundsexclub.com/?mod=personals&co='.$data['country'].'&st='.$data['state'].'&ar='.$data['id'].'';
$stringData = "".$text."\n";
fwrite($fh, $stringData);

///////  PAGE IS FULL
if($x > 48999)
{
////// CLOSE PAGE
fclose($fh);
//////   OPEN NEW PAGE
$pagenum++;
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-personals-areas-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
$x=0;
}
}

///// ENTRY FOR MAIN SITEMAP PAGE AFTER ALL IS DONE
for ($x = 1; $x <= $pagenum; $x++) {
	$pagelist .= '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-personals-areas-'.$x.'.txt</loc>
<lastmod>'.date('Y-m-d', $time).'</lastmod>
</sitemap>';
}


*/













////////////////////////////////////////////   WRITE FORUM TOPICS
$pagenum = 1;
/// OPEN FIRST PAGE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-forumtopics-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);

//// GET USERS
$query = $db->query("SELECT id FROM forumtopics ORDER BY id ASC",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$text = 'https://www.theundergroundsexclub.com/?f='.$data['id'].'';
$stringData = "".$text."\n";
fwrite($fh, $stringData);

///////  PAGE IS FULL
if($x > 48999)
{
////// CLOSE PAGE
fclose($fh);
//////   OPEN NEW PAGE
$pagenum++;
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-forumtopics-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
$x=0;
}
}

///// ENTRY FOR MAIN SITEMAP PAGE AFTER ALL IS DONE
for ($x = 1; $x <= $pagenum; $x++) {
	$pagelist .= '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-forumtopics-'.$x.'.txt</loc>
<lastmod>'.date('Y-m-d', $time).'</lastmod>
</sitemap>';
}















////////////////////////////////////////////   WRITE FORUM CATEGORIES
$pagenum = 1;
/// OPEN FIRST PAGE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-forumcategories-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);

//// GET USERS
$query = $db->query("SELECT id FROM forumcategories ORDER BY id ASC",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$text = 'https://www.theundergroundsexclub.com/?mod=forum&file=category&id='.$data['id'].'';
$stringData = "".$text."\n";
fwrite($fh, $stringData);

///////  PAGE IS FULL
if($x > 48999)
{
////// CLOSE PAGE
fclose($fh);
//////   OPEN NEW PAGE
$pagenum++;
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-forumcategories-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
$x=0;
}
}

///// ENTRY FOR MAIN SITEMAP PAGE AFTER ALL IS DONE
for ($x = 1; $x <= $pagenum; $x++) {
	$pagelist .= '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-forumcategories-'.$x.'.txt</loc>
<lastmod>'.date('Y-m-d', $time).'</lastmod>
</sitemap>';
}























////////////////////////////////////////////   WRITE LOCAL WOMEN
$pagenum = 1;
/// OPEN FIRST PAGE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-localwomen-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);

//// GET TOWNS
$query = $db->query("SELECT url FROM towns WHERE local_active = 'y' ORDER BY id ASC",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$text = 'https://www.theundergroundsexclub.com/?local-women='.$data['url'].'';
$stringData = "".$text."\n";
fwrite($fh, $stringData);

///////  PAGE IS FULL
if($x > 48999)
{
////// CLOSE PAGE
fclose($fh);
//////   OPEN NEW PAGE
$pagenum++;
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-localwomen-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
$x=0;
}
}
///// ENTRY FOR MAIN SITEMAP PAGE AFTER ALL IS DONE
for ($x = 1; $x <= $pagenum; $x++) {
	$pagelist .= '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-localwomen-'.$x.'.txt</loc>
<lastmod>'.date('Y-m-d', $time).'</lastmod>
</sitemap>';
}


////////////////////////////////////////////   WRITE LOCAL SLUTS
$pagenum = 1;
/// OPEN FIRST PAGE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-localsluts-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);

//// GET TOWNS
$query = $db->query("SELECT url FROM towns WHERE local_active = 'y' ORDER BY id ASC",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$text = 'https://www.theundergroundsexclub.com/?local-sluts='.$data['url'].'';
$stringData = "".$text."\n";
fwrite($fh, $stringData);

///////  PAGE IS FULL
if($x > 48999)
{
////// CLOSE PAGE
fclose($fh);
//////   OPEN NEW PAGE
$pagenum++;
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-localsluts-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
$x=0;
}
}
///// ENTRY FOR MAIN SITEMAP PAGE AFTER ALL IS DONE
for ($x = 1; $x <= $pagenum; $x++) {
	$pagelist .= '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-localsluts-'.$x.'.txt</loc>
<lastmod>'.date('Y-m-d', $time).'</lastmod>
</sitemap>';
}



























///////////////// OPEN  FILE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemap.xml";
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
$sitemapUrl = "https://www.theundergroundsexclub.com/sitemap.xml";

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
