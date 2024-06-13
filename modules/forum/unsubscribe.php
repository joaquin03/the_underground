<?php
/////////////   IF USER IS LOGGED IN
if(isset($_SESSION['userid']))
{
$user = $db->row("SELECT * FROM members WHERE id = :u AND validated = 'y'",array("u"=>$_SESSION['userid']));
}
////////////  IF USER IS NOT LOGGED IN BUT HAS VALid CODE
else if($_GET['uc'] != '')
{
$user = $db->row("SELECT * FROM members WHERE usercode = :u AND validated = 'y'",array("u"=>$_GET['uc']));
}

if($user['id'] <= 0)
{
header("Location:".$array['rooturl']."/?mod=login");
exit;
}


///
$page->page .= $page->get_temp('templates/forum/unsubscribe.htm');
$array['pagetitle'] = 'Forum Unsubscribe';



///////////  SINGLE TOPIC
$topic = $db->row("SELECT * FROM forumtopics WHERE id = :id",array("id"=>$_GET['topic']));
$check = $db->query("SELECT id FROM forumbookmarks WHERE owner = :o AND topic = :t",
array("o"=>$user['id'],"t"=>$topic['id']),PDO::FETCH_NUM,'y');

$array['topsection'] = ($check > 0) ? '<h2>Forum Topic</h2>
You are currently subscribed to the forum topic: '.$short->forumtopic($topic['id'],'text','y').'

<div class="space20"></div>
<a href="'.$array['ogurl'].'&deltopic=y"><span class="button">Unsubscribe from Topic</span></a><div class="space30"></div>
': '<h2>Forum Topic</h2>You are NOT subscribed to the topic: '.$short->forumtopic($topic['id'],'text','y').'<div class="space30"></div>';




////// ALL TOPICS
$settings = $db->row("SELECT email_forum FROM members WHERE id = :u",array("u"=>$user['id']));

$array['allsection'] = ($settings['email_forum'] == 'y') ? '<h2>Forum Notifications</h2>
If you like, instead of removing the topic from your subscriptions, you can just turn off email notifications.
 If you do, you will remain subscribed, so you can quickly access the topics, but you wont get an email notification when a post is made in the topics you subscribe to.
<div class="space20"></div>
<a href="'.$array['ogurl'].'&stopnotify=y"><span class="button">Stop Email Notifications</span></a>': '';



if($array['allsection'] == '' && $array['topsection'] == '')
{
$array['topsection'] = '<h2>No Subscription Detected</h2>
The topic does not exist, and you are no longer set to receive email notifications for forum posts.';
}






//////////////   DELETE SINGLE SUBSCRIPTION
if(isset($_GET['deltopic']))
{
$db->query("DELETE FROM forumbookmarks WHERE owner = :o AND topic = :t", array("o"=>$user['id'],"t"=>$topic['id']),PDO::FETCH_ASSOC,"n");
$_SESSION['gmessage'] = 'Unsubscribed Successfully';
$url = str_replace('&deltopic=y','',$array['ogurl']);
header("Location:".$url);
exit;
}
