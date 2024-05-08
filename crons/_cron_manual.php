<?php
ini_set('memory_limit', '256M');
session_start();
ob_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE);
////
$ltt = microtime();
$ltt = explode(' ', $ltt);
$ltt = $ltt[1] + $ltt[0];
$ltstart = $ltt;




///   INCLUDE SITE CONFIG
include('/var/www/vhosts/theundergroundsexclub.com/httpdocs/configfile.php');
include_once(''.$serverpath.'/addons/Db.class.php');
$db = new Db();
include(''.$serverpath.'/addons/short.php');
include(''.$serverpath.'/addons/page.php');





$query = $db->query("SELECT id,email FROM members WHERE email LIKE '%bombamai%' LIMIT 50", null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
echo ''.$data['email'].' ';
$short->deletemember($data['id'],'n');
}



























//$db->query("UPDATE classifieds SET brett = 'n'", null,PDO::FETCH_ASSOC,"n");








//include('reset_searches.php');



//$db->query("DELETE FROM pages WHERE phrase LIKE '%Fuck Women%'", null,PDO::FETCH_ASSOC,"n");


/*




$query = $db->query("SELECT * FROM pages WHERE phrase LIKE '%Backpage%' LIMIT 10000",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
echo $data['phrase'].' ';
}




/////  UPDATE GROUP IMAGE TO BLANK
$db->query("UPDATE pm SET image = ''", null,PDO::FETCH_ASSOC,"n");




















*/














//// DISPLAY LOAD TIME
$ltt = microtime();
$ltt = explode(' ', $ltt);
$ltt = $ltt[1] + $ltt[0];
$ltfinish = $ltt;
$total_time = round(($ltfinish - $ltstart), 4);
echo 'Loaded in '.$total_time.' seconds';










/*
// SET URL FOR TOWN
$query = $db->query("SELECT * FROM towns WHERE url = '' LIMIT 10000", null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$url = $data['suburb'].'-'.$data['state'].'-'.$data['country'];
$url = strtolower(trim($url));
$url= $short->replace_accents($url);
$find = array(' ', '&', '\r\n', '\n', '+',',');
$url = str_replace ($find, '-', $url);
$find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
$repl = array('', '-', '');
$url = preg_replace ($find, $repl, $url);
$array['test'] .= $url.' ';
$db->query("UPDATE towns SET url = :u WHERE id = :id", array("u"=>$url,"id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}
















$query = $db->query("SELECT id,suburb FROM towns WHERE done = 'n' LIMIT 10000", null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$sexsql = "AND sex = 'Female' ";
$find_town = str_replace("'","",$data['suburb']);
/// CHECK FOR WOMEN IN AREA
$check = $db->query("SELECT id,username,image, MATCH (country, town, sex,username,sex_relstatus, sex_pref) AGAINST ('".$find_town."') FROM members WHERE validated = 'y' AND image != '' $sexsql AND MATCH (country, town, sex,username,sex_relstatus, sex_pref) AGAINST ('".$find_town."') ORDER BY rand() LIMIT 1", null,PDO::FETCH_NUM,'y');

$activate = ($check > 0) ? 'y': 'n';



$db->query("UPDATE towns SET done = :v, local_active = :a WHERE id = :id", array("v"=>'y',"a"=>$activate,"id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}
