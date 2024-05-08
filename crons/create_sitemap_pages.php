<?php


//// GET THE LAST PAGE NUM
$setting = $db->row("SELECT value FROM site_settings WHERE `name` = 'sitemap pages lastid'");
$lastpage = $setting['value'];


/// VARIABLES
$perpage = 49000;
$pagenum = $lastpage+1;
$offset = $lastpage*$perpage;
$stringData = '';

/// OPEN PAGE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemaps/sitemap-content-".$pagenum.".txt";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);

//// GET DATABASE PAGES
$query = $db->query("SELECT phrase FROM pages LIMIT $offset, $perpage",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$text = 'https://www.theundergroundsexclub.com/?page='.urlencode($data['phrase']).'';
$stringData = "".$text."\n";
/// WRITE STRING
fwrite($fh, $stringData);
}
////// CLOSE PAGE
fclose($fh);


//echo $offset.', '.$perpage;


if($stringData != '')
{
/// UPDATE DATABASE
$db->query("UPDATE site_settings SET value = :v WHERE name = 'sitemap pages lastid'", array("v"=>$pagenum),PDO::FETCH_ASSOC,"n");
}
else
{
$pagenum = $lastpage;
}



///// ENTRY FOR MAIN SITEMAP PAGE AFTER ALL IS DONE - FROM 1 to LAST PAGE
for ($x = 1; $x <= $pagenum; $x++) {
	$pagelist .= '<sitemap>
<loc>https://www.theundergroundsexclub.com/sitemaps/sitemap-content-'.$x.'.txt</loc>
</sitemap>
';
}








///////////////// OPEN  FILE
$myFile = "/var/www/vhosts/theundergroundsexclub.com/httpdocs/sitemap-1.xml";
$fh = fopen($myFile, 'w') or die("can't open file");
chmod($myFile,0777);
$text = '';
///////////////////////////// WRITE HEADER
$text = '<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
$stringData = "".$text."\n";
fwrite($fh, $stringData);
//// BODY
$stringData = "".$pagelist."";
fwrite($fh, $stringData);
//////////////// WRITE FOOTER
$text = '</sitemapindex>';
$stringData = "".$text."\n";
fwrite($fh, $stringData);
////////////// CLOSE PAGE
fclose($fh);
