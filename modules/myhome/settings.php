<?php
if(!isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']."/?mod=login");
exit;
}
$array['pagetitle'] = 'Account Settings';
$array['pagedescription'] = '';
$page->page .= $page->get_temp('templates/myhome/settings.htm');

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Settings',2);



//////////////// POST
if(isset($_POST['button']))
{
$db->query("UPDATE members SET
notify_follow = '{$_POST['notify_follow']}',
notify_commentsme = '{$_POST['notify_commentsme']}',
notify_commentsgallery = '{$_POST['notify_commentsgallery']}',
notify_commentsphoto = '{$_POST['notify_commentsphoto']}',
notify_commentsstory = '{$_POST['notify_commentsstory']}',
notify_commentsfeed = '{$_POST['notify_commentsfeed']}',
notify_votesmember = '{$_POST['notify_votesmember']}',
notify_votesgallery = '{$_POST['notify_votesgallery']}',
notify_votesphoto = '{$_POST['notify_votesphoto']}',
notify_votesstory = '{$_POST['notify_votesstory']}',
notify_votesfeed = '{$_POST['notify_votesfeed']}',
notify_joinmygroup = '{$_POST['notify_joinmygroup']}',
notify_groupcomments = '{$_POST['notify_groupcomments']}',
email_pm = '{$_POST['email_pm']}',
email_forum = '{$_POST['email_forum']}',
email_newsletter = '{$_POST['email_newsletter']}'
WHERE id = :id", array("id"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");



$_SESSION['gmessage'] = 'Settings Updated Successfully';
header("Location:".$array['rooturl']."/?mod=myhome&file=settings");
exit;
}



/// GET DATA
$settings = $db->row("SELECT notify_follow, notify_commentsme, notify_commentsgallery, notify_commentsphoto, 	notify_commentsstory, notify_commentsfeed, notify_votesmember, notify_votesgallery, notify_votesphoto, notify_votesstory, notify_votesfeed, notify_joinmygroup, notify_groupcomments, email_pm, email_forum, email_newsletter  FROM members WHERE id = :o",array("o"=>$_SESSION['userid']));
foreach($settings as $key => $value)
{
$array[$key] = ($value == 'y') ? 'checked="checked"' : '';
}
