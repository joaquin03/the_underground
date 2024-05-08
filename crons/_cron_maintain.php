<?php
session_start();
ob_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE);

////////////////////////////////////////////////////////   INCLUDE SITE CONFIG
include('/var/www/vhosts/theundergroundsexclub.com/httpdocs/configfile.php');
include_once(''.$serverpath.'/addons/Db.class.php');
$db = new Db();
include(''.$serverpath.'/addons/short.php');
include(''.$serverpath.'/addons/page.php');



/// BREAK DOWN TIMES TO RUN AT DIFFERENT TIMES
$minute = date('i',$time);
$lastminnum = substr($minute, -1);
$hour = date('H',$time);
$day = date('j',$time);
$daynum = date('N',$time);//1=Mon 7=Sun











/// HT ACCESS CREATE
$d= $db->row("SELECT value FROM s_admin WHERE name = :v AND site = :s",array("v"=>'create_htaccess',"s"=>$domainonly));
if($d['value'] == 'y' || isset($_GET['force']))
{
include('create_htaccess.php');
$db->query("UPDATE s_admin SET value = 'n' WHERE name = :v AND site = :s", array("v"=>'create_htaccess',"s"=>$domainonly),PDO::FETCH_ASSOC,"n");
}





/////  TidY IPs
$query = $db->query("SELECT regip,id FROM members WHERE regip != '' AND zipcode = '' AND town = '' ORDER BY id DESC LIMIT 1",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$details = $short->zipfromip($data['regip']);
list($zip,$town) = explode("|", $details);
$zip = ($zip == '') ? 'na': $zip;
$db->query("UPDATE members SET zipcode = :z,town = :t WHERE id = :id", array("z"=>$zip,"t"=>$town,"id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}



/////  FILL EMPTY USERCODES
$query = $db->query("SELECT id FROM members WHERE usercode = ''",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$usercode = $short->createusercode();
$db->query("UPDATE members SET usercode = :u WHERE id = :id", array("u"=>$usercode,"id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}





//////////////////////////////////////////////////////////  RUN EVERY 10 MINS
if($lastminnum == 2)
{
///////////   CREATE SEARCHES
include('create_new_searches.php');
}

if($lastminnum == 3)
{
///// UPDATE PAGE LIST CACHE
//include('update_page_list.php');
}

if($lastminnum == 4)
{
///// UPDATE MEMBER COUNT CACHE
include('update_member_count.php');
}





//////////////////////////////////////////////////////////  RUN EVERY 1 HOUR
if($minute == 16)
{
///  RUN ADJUSTMENTS
include('adjustments.php');
///// UPDATE CACHE FILES
include('create_cache_files.php');
}


if($minute == 18)
{
///////////   DELETE OLD NOTIFICATIONS
include('delete_old_data.php');
}










///////////////////  REMOVE DATAFROM 1 WEEK AGO
$timeago = $time-(60*60*24*5);
$db->query("DELETE FROM members WHERE validated = 'n' AND regdate < :t", array("t"=>$timeago),PDO::FETCH_ASSOC,"n");
$db->query("DELETE FROM classifieds WHERE title = '' AND stamp < :t", array("t"=>$timeago),PDO::FETCH_ASSOC,"n");







///////////   SCRAPE WEBSITES FOR DETAILS
include('website_scraper.php');
