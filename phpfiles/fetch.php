<?php
session_start();
ob_start();
include_once('../addons/Db.class.php');
include('../addons/news.php');
include('../addons/short.php');
$time = time();


$db = new Db();

//sanitize post value
$page_number = filter_var($_POST["group_no"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
$perpage = filter_var($_POST["num"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
$querystring = filter_var($_POST["hash"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$mobilemod = filter_var($_POST["mobilemod"], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);




///// QUERY TYPE CODES
$equation = array(
'AA789OPL' => '=',
'ZVBG6711' => '>'
);


$mobilelink = ($mobilemod == '') ? '': '&m=y';

//////   GET CONTENT ADS
$query = $db->query("SELECT * FROM ads WHERE type = 'content' AND active = 'y' ORDER BY rand() LIMIT 6",null,PDO::FETCH_ASSOC,"n");
$adcount = 0;
foreach($query as $data)
{
$adcount++;
$addata[''.$adcount.''] = '<a href="'.$data['link'].$mobilelink.'">
<img src="https://staticads.theundergroundsexclub.com/images/ads/'.$data['id'].'.jpg" width="100%" style="max-width:'.$data['display_x'].'" border="0"alt=""/></a>';
}








/////////////////////////////////  DECODE THE SENT QUERY
if($queryselect == '')
{
$fullquery = '1';
}
else
{
$sets = explode("#", $queryselect);
foreach($cartitems as $key => $cartlist)
{
list($column,$q,$value) = explode("*", $sets);
}
}


//throw HTTP error if page number is not valid
if(!is_numeric($page_number)){
     header('HTTP/1.1 500 Invalid page number!');
     exit();
}

//get current starting point of records
$position = ($page_number * $perpage);
$queryst = $short->stringdecoder($querystring);

//Limit our results within a specified range.
$query = $db->query("$queryst LIMIT $position, $perpage",null,PDO::FETCH_ASSOC,"n");
$x = 0;
$ad = 0;

//echo $queryst;

//output results from database depending on type
foreach($query as $data)
{
$x++;

//////  DISPLAY AD AND INCREASE COUNT
if($x==4 || $x==12 || $x==20 || $x==27)
{
$abovespacer = ($_POST['display'] == 'stories' || $_POST['display'] == 'forumtopic') ? '<div class="space10"></div>': '';
$belowspacer = ($_POST['display'] == 'stories' || $_POST['display'] == 'forumtopic') ? '<div class="space-10"></div>': '';
$ad++;
echo $abovespacer.''.$addata[''.$ad.''].'<div class="divline"></div>'.$belowspacer;
}




//////// NEWS ITEM
if($_POST['display'] == 'news')
{
echo  $news->item($data['id']).'<div class="divline"></div>';
}



//////// FOLLOWERS LIST
if($_POST['display'] == 'followers')
{
echo  $short->user($data['owner'],'result','n').'<div class="divline"></div>';
}

//////// FOLLOWING LIST
if($_POST['display'] == 'following')
{
echo  $short->user($data['who'],'result','n').'<div class="divline"></div>';
}

//////// MEMBER RESULT
if($_POST['display'] == 'members')
{
echo  $short->user($data['id'],'result','n').'<div class="divline"></div>';
}



//////// GROUP FOLLOWING
if($_POST['display'] == 'groupmember')
{
echo  $short->group($data['groupid'],'result').'<div class="divline"></div>';
}

//////// GROUP FOLLOWING
if($_POST['display'] == 'groups')
{
echo  $short->group($data['id'],'result').'<div class="divline"></div>';
}





//////// STORIES
if($_POST['display'] == 'stories')
{
echo  $short->story($data['id'],'result','y').'';
}



//////// FORUM TOPIC
if($_POST['display'] == 'forumtopic')
{
echo  $short->forumtopic($data['id'],'result','y').'';
}




//////// FORUM POSTS
if($_POST['display'] == 'forumpost')
{
echo  '<div class="space-10"></div>'.$short->forumpost($data['id'],'y').'';
}




//////// PERSONAL
if($_POST['display'] == 'personal')
{
echo  ''.$short->personal($data['id'],'result','y').'<div class="divline"></div>';
}





//////// GALLERIES
if($_POST['display'] == 'galleries')
{
echo  $short->gallery($data['id'],'large','y').'
<div class="divline"></div>';
}








}



?>
