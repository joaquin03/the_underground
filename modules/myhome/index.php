<?php
if(!isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']."/?mod=login");
exit;
}
$page->page .= $page->get_temp('templates/myhome/index.htm');
$array['pagetitle'] = 'Dashboard';

//
$id = $member['id'];



$array['moderator'] = '';
if($_SESSION['userid'] == '100')
{
/////////  POST DELETE MEMBER
if(isset($_POST['delmember']))
{
if($_POST['emailonly'] == 'y')
{
$user = $db->row("SELECT * FROM members WHERE email = :e OR username = :e2",array("e"=>$_POST['field'],"e2"=>$_POST['field']));
if($user['id'] > 0)
{
$db->query("UPDATE members SET
notify_follow = 'n',
notify_commentsme = 'n',
notify_commentsgallery = 'n',
notify_commentsphoto = 'n',
notify_commentsstory = 'n',
notify_commentsfeed = 'n',
notify_votesmember = 'n',
notify_votesgallery = 'n',
notify_votesphoto = 'n',
notify_votesstory = 'n',
notify_votesfeed = 'n',
notify_joinmygroup = 'n',
notify_groupcomments = 'n',
email_pm = 'n',
email_newsletter = 'n',
email_forum = 'n'
WHERE id = :id", array("id"=>$user['id']),PDO::FETCH_ASSOC,"n");
$_SESSION['gmessage'] = 'User Unsubscribed Successfully';
}
else
{
$_SESSION['rmessage'] = 'No User Found';
}
}
else
{
$user = $db->row("SELECT * FROM members WHERE email = :e OR username = :e2",array("e"=>$_POST['field'],"e2"=>$_POST['field']));
if($user['id'] > 0)
{
$short->deletemember($user['id'],'n');
$_SESSION['gmessage'] = 'User Removed Successfully';
}
else
{
$_SESSION['rmessage'] = 'No User Found';
}
}
/// REDIRECT
header("Location: ".$array['rooturl']."");
exit;
}




///////  GET BOT COUNT
$google = $db->query("SELECT id FROM s_visitors WHERE bot LIKE :b",array("b"=>'%Googlebot%'),PDO::FETCH_NUM,'y');
$bing = $db->query("SELECT id FROM s_visitors WHERE bot LIKE :b",array("b"=>'%bingbot%'),PDO::FETCH_NUM,'y');
$yahoo = $db->query("SELECT id FROM s_visitors WHERE bot LIKE :b",array("b"=>'%Yahoo%'),PDO::FETCH_NUM,'y');
$array['moderator'] .= '<h2>Moderation</h2>
<strong>Current Bots</strong>
<div class="space5"></div>
Google: '.number_format($google).'
<div class="space1"></div>Bing: '.number_format($bing).'
<div class="space1"></div>Yahoo: '.number_format($yahoo).'
<div class="space1"></div><a href="../?mod=myhome&file=moderatevisitors">Moderate Visitors</a>
<div class="space20"></div>';

//// SEARCH ITEM FORM
$array['moderator'] .= '<strong>Show Items</strong>
<div class="space5"></div>
<form id="form" name="form" method="get" action="">
<input type="hidden" name="mod" value="myhome">
<input type="hidden" name="file" value="moderate">
<select name="what" class="formfield width100">
  <option value="messages" {messages-select}>Messages</option>
  <option value="comments" {comments-select}>Comments</option>
  <option value="feed" {feed-select}>Feed Posts</option>
</select>
<input name="button" type="submit" class="button" id="button" value="Show" />
<div class="clear"></div>
</form>
<div class="space20"></div>';


//// DELETE MEMBER FORM
$array['moderator'] .= '<strong>Delete Member</strong>
<div class="space5"></div>
<form id="form" name="form" method="post" action="">
<input name="field" type="text" class="formfield width100" id="field" value="" placeholder="Username or Email Address" autocorrect="off"  autocomplete="off"   />
<input name="emailonly" type="checkbox" value="y" > Email Unsubscribe Only
<div class="space10"></div>
<input name="delmember" type="submit" class="button" id="delmember" value="Delete" />
<div class="clear"></div>
</form>
<div class="space20"></div>';

/// LAST EXTRA SPACE
$array['moderator'] .= '<div class="space10"></div>';



}// END IF MODERATOR









/// FOLLOWERS
$tot = $db->query("SELECT id FROM friends WHERE who = :o",array("o"=>$member['id']),PDO::FETCH_NUM,'y');
$fplur = ($tot == 1) ? '' : 's';
/// VIEWS
$vplur = ($member['views'] == 1) ? '': 's';

$array['menu'] = '';
$array['menu-mobile'] = '';
$array['menu'.$mobilemod.''] = '<h2>Your Profile</h2>
<span class="is minieye"></span>'.number_format($member['views']).' View'.$vplur.'
<div class="space5"></div>
<span class="is miniuser"></span><a href="../?u='.$member['id'].'&view=followers">'.number_format($tot).' Follower'.$fplur.'</a>
<div class="space20"></div>
<a href="../?u='.$member['id'].'">View Profile</a> &middot; <a href="../?mod=myhome&file=info">Edit Profile Info</a> &middot; <a href="../?mod=myhome&file=image">Edit Profile Photo</a>
<div class="space10"></div>
<div class="space20"></div>';

////////////////  MY CONTENT
/// GALL
$tot = $db->query("SELECT id FROM galleries WHERE owner = :o AND completed = 'y' ",array("o"=>$id),PDO::FETCH_NUM,'y');
$plur = ($tot == 1) ? 'y' : 'ies';
$galleries = ($tot == 0) ? 'No Galleries':'<a href="../?u='.$id.'&view=galleries">'.number_format($tot).' Photo Galler'.$plur.'</a>';
/// GROUPS
$tot = $db->query("SELECT id FROM groupfollows WHERE owner = :o",array("o"=>$id),PDO::FETCH_NUM,'y');
$plur = ($tot == 1) ? '' : 's';
$groups = ($tot == 0) ? 'No Groups':'<a href="../?u='.$id.'&view=groups">'.number_format($tot).' Group'.$plur.'</a>';
//  STORIES
$tot = $db->query("SELECT id FROM stories WHERE owner = :o",array("o"=>$id),PDO::FETCH_NUM,'y');
$plur = ($tot == 1) ? 'y' : 'ies';
$stories = ($tot == 0) ? 'No Stories':'<a href="../?mod=stories&file=category&uid='.$id.'">'.number_format($tot).' Sex Stor'.$plur.'</a>';
//  FORUM TOPICS
$tot = $db->query("SELECT id FROM forumtopics WHERE addedby = :o",array("o"=>$id),PDO::FETCH_NUM,'y');
$plur = ($tot == 1) ? '' : 's';
$forum = ($tot == 0) ? 'No Forum Topics':'<a href="../?mod=forum&file=category&uid='.$id.'">'.number_format($tot).' Forum Topic'.$plur.'</a>';
//  PERSONALS
$tot = $db->query("SELECT id FROM classifieds WHERE owner = :o AND title != '' AND delstamp = 0 ",array("o"=>$id),PDO::FETCH_NUM,'y');
$plur = ($tot == 1) ? '' : 's';
$personals = ($tot == 0) ? 'No Personal Ads':'<a href="../?u='.$id.'&view=personals">'.number_format($tot).' Personal Ad'.$plur.'</a>';



$array['mycontent'] = '<span class="is miniphoto"></span>'.$galleries.' <span class="lightgrey">&middot;</span> <a href="../?mod=galleries&file=new">Add a Gallery</a>
<div class="space5"></div>
<span class="is minigroup"></span>'.$groups.' <span class="lightgrey">&middot;</span> <a href="../?mod=groups&file=new">Create a Group</a> <span class="lightgrey">&middot;</span> <a href="../?mod=groups">Join Groups</a>
<div class="space5"></div>
<span class="is ministory"></span>'.$stories.' <span class="lightgrey">&middot;</span> <a href="../?mod=stories&file=new">Write a Sex Story</a>
<div class="space5"></div>
<span class="is miniforum"></span>'.$forum.' <span class="lightgrey">&middot;</span> <a href="../?mod=forum&file=add">Create a Topic</a>
<div class="space5"></div>
<span class="is miniad"></span>'.$personals.' <span class="lightgrey">&middot;</span> <a href="../?mod=personals&file=new">Post an Ad</a>
<div class="space5"></div>';
























/////////////  FEED
if($_GET['feed'] == 'all')
{
$string = '1';
$array['feedtitle'] = 'Everyone<span class="lightgrey"> &middot; </span><a href="../"><span class="blue">Show Friends Only</span></a>';
}
else
{
$array['feedtitle'] = 'Friends Feed<span class="lightgrey"> &middot; </span><a href="../?feed=all"><span class="blue">Show Everyone</span></a>';
/// FRIEND FEEDS
$query = $db->query("SELECT who FROM friends WHERE owner = :id",array("id"=>$member['id']),PDO::FETCH_ASSOC,"n");
$string = 'owner = 100';
foreach($query as $data)
{
$string .= ' OR owner = '.$data['who'].'';
}
}

include($serverpath.'/addons/news.php');


/// PAGINATION
$resultcount = $db->query("SELECT id FROM news WHERE $string ORDER BY stamp DESC", null,PDO::FETCH_NUM,'y');
$perpage = '20';
$spage = ($_GET['pagenum'] > 1) ? $_GET['pagenum']: 1;
$startnum = ($perpage*($spage-1));
///  REDIRECT FAKE PAGES
if($_GET['page'] > ceil($resultcount/$perpage))
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$short->removepagenum($pageurl));
exit;
}
$array['pagination'] = $short->pagination($resultcount,$perpage,$spage,$pageurl,$paginate_adj);


/// GET RESULTS
$x=0;
$query = $db->query("SELECT id FROM news WHERE $string ORDER BY stamp DESC LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$array['newsfeed'] .= $spacer.$news->item($data['id']);
}

////  NO RESULTS
if($array['newsfeed'] == '')
{
$array['newsfeed'] = 'Sorry, your search returned no results.';
}
