<?php
session_start();

///   INCLUDE SITE CONFIG
include('/var/www/vhosts/theundergroundsexclub.com/httpdocs/configfile.php');
include_once(''.$serverpath.'/addons/Db.class.php');
$db = new Db();
include(''.$serverpath.'/addons/short.php');
include(''.$serverpath.'/addons/page.php');



if ($_SERVER['HTTP_REFERER'] != "")
{
$_SESSION['history'] = $_SERVER['HTTP_REFERER'];
}











//////////////////////////////////////////////////////////////////////////////////////   MEMBERS PAST HERE
if(!isset($_SESSION['userid']))
{
header("Location: http://www.theundergroundsexclub.com/?mod=nosession");
exit;
}












/////////////////////////  SUBSCRIBE
if(isset($_GET['subscribe']))
{
$id = $_GET['subscribe'];
////  CHECK ENTRY
$check = $db->query("SELECT id FROM forumbookmarks WHERE topic = {$id} and owner = :u",array("u"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');

////  ADD ENTRY
if($check == 0)
{
//// ADD BOOKMARK
$insert = $db->query("INSERT INTO forumbookmarks(owner,topic) VALUES(:u,:t)",
array("u"=>$_SESSION['userid'],"t"=>$id),PDO::FETCH_ASSOC,"n");
}

$_SESSION['gmessage'] = 'Subscribed Successfully';

header("Location: ".$_SESSION['history']."");
exit;
}






/////////////////////////  UNSUBSCRIBE
if(isset($_GET['unsubscribe']))
{
$id = $_GET['unsubscribe'];
////  DELETE JOIN
$db->query("DELETE FROM forumbookmarks WHERE topic = {$id} and owner = :u", array("u"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");

$_SESSION['gmessage'] = 'Unsubscribed Successfully';

header("Location: ".$_SESSION['history']."");
exit;
}



















/////////////////////////  DELETE POST
if(isset($_GET['delpost']))
{
$id = $_GET['delpost'];
$post = $db->row("SELECT * FROM forumposts WHERE id = :id",array("id"=>$id));
$topic = $db->row("SELECT * FROM forumtopics WHERE id = :id",array("id"=>$post['topic']));

//// PROCESS IF OWNER OR ADMIN
if($_SESSION['userid'] == '100' || $_SESSION['userid'] == $post['addedby'])
{
////   SET SQL FOR DELETION BASED ON ORIGINAL OR NOT
$sql = ($post['original'] == 'y') ? "topic = {$post['topic']}": "id = {$post['id']}";

///  RUN QUERY LOOP FOR EITHER 1 POST or ENTIRE TOPIC
$query= $db->query("SELECT * FROM forumposts WHERE $sql",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
/// DELETE ONE NEWS ITEM FOR EACH POST MATCHING TIME
$db->query("DELETE FROM news WHERE itemid = :id and (type = 'forum' OR type = 'newforum') and owner = :o and stamp = :st",
array("id"=>$post['topic'],"o"=>$data['addedby'],"st"=>$data['added']),PDO::FETCH_ASSOC,"n");
//// UNLINK IMAGE FOR EACH POST BEING DELETED
@unlink($serverpath.'/images/forum/'.$data['image'].'-original.jpg');
@unlink($serverpath.'/images/forum/'.$data['image'].'.jpg');
@unlink($serverpath.'/images/forum/'.$data['image'].'-thumb.jpg');
//// DELETE ACTUAL POST
$db->query("DELETE FROM forumposts WHERE id = :id",array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
/////  CALCULATE AND UPDATE USER POSTS COUNT
$count = $db->query("SELECT id FROM forumposts WHERE addedby = :u",array("u"=>$data['addedby']),PDO::FETCH_NUM,'y');
$db->query("UPDATE members SET forumposts = :p WHERE id = :id", array("p"=>$count,"id"=>$data['addedby']),PDO::FETCH_ASSOC,"n");
}

//// ADDITIONAL INSTRUCTIONS FOR ORIGINAL POSTS
if($post['original'] == 'y')
{
////  REMOVE SUBSCRIPTIONS
$db->query("DELETE FROM forumbookmarks WHERE topic = :id", array("id"=>$topic['id']),PDO::FETCH_ASSOC,"n");
//// REMOVE ALL NEWS FOR THE TOPIC
$db->query("DELETE FROM news WHERE itemid = :id and type = :t", array("id"=>$topic['id'],"t"=>'forum'),PDO::FETCH_ASSOC,"n");
//// REMOVE ALL NOTIFICATIONS
$db->query("DELETE FROM notifications WHERE itemid = :id and itemtype = :t", array("id"=>$topic['id'],"t"=>'forum'),PDO::FETCH_ASSOC,"n");
//// DELETE SEARCH ENTRY
$turl = '/?f='.$topic['id'];
$db->query("DELETE FROM search WHERE url = :url",array("url"=>$turl),PDO::FETCH_ASSOC,"n");
//// DELETE ACTUAL TOPIC
$db->query("DELETE FROM forumtopics WHERE id = :id",array("id"=>$topic['id']),PDO::FETCH_ASSOC,"n");
}
/////   IF NOT ORIGINAL
else
{
/// GET LAST POST FOR RE ADJUSTING LAST TIME
$last = $db->row("SELECT * FROM forumposts WHERE topic = :t ORDER BY id DESC",array("t"=>$topic['id']));
$lasttime = $last['added'];
/////   CALCULATE REMAINING TOPIC POSTS COUNT AND TIME AND UPDATE
$count = $db->query("SELECT id FROM forumposts WHERE topic = :t",array("t"=>$topic['id']),PDO::FETCH_NUM,'y');
$db->query("UPDATE forumtopics SET posts = :p, lastpost = :t WHERE id = :id", array("p"=>$count,"t"=>$lasttime,"id"=>$topic['id']),PDO::FETCH_ASSOC,"n");
}


///// CALCULATE AND UPDATE CATEGORY POSTS AND TOPICS TOTALS - GET IN LAST POST ORDER
$query= $db->query("SELECT posts,lastpost FROM forumtopics WHERE category = :c ORDER BY lastpost ASC",array("c"=>$topic['category']),PDO::FETCH_ASSOC,"n");
$x=0;
$p=0;
foreach($query as $data)
{
$x++;
$p = $data['posts']+$p;
$lastpost = $data['lastpost'];
}
$db->query("UPDATE forumcategories SET topics = :t, posts = :p, laststamp = :a WHERE id = :id", array("t"=>$x,"p"=>$p,"a"=>$lastpost,"id"=>$topic['category']),PDO::FETCH_ASSOC,"n");



$_SESSION['gmessage'] = 'Post Removed Successfully';
}
else
{
$_SESSION['rmessage'] = 'Error Removing Post';
}

header("Location: ".$_SESSION['history']."");
exit;
}
