<?php
$page->page .= $page->get_temp('templates/forum/category.htm');






/////////////  STORIES BY A USER
if(isset($_GET['uid']))
{
$user = $db->row("SELECT * FROM members WHERE id = :id",array("id"=>$_GET['uid']));
if($user['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=forum");
exit;
}

$sql = "addedby = {$user['id']}";

$array['pagetitle'] = 'Forum Topics &middot; '.$short->clean($user['username']).'';
$array['pagedescription'] = 'Forum Topics by '.$short->clean($user['username']).'.  Create your sex forum topics today.';


//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=members','Members',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?u='.$user['id'],$short->clean($user['username']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Forum Topics',4);


$array['title'] = 'Forum Topics by '.$short->clean($user['username']).'';
$array['addnew'] = '';
$array['newpost'] = '';

//////// DECLARE BUTTONS
$array['followlink'] = '?mod=register';
$array['followtext'] = 'Follow';
$array['blocklink'] = '?mod=register';
$array['blocktext'] = 'Block';
$array['messagelink'] = '?mod=register';

if(isset($_SESSION['userid']))
{
$array['messagelink'] = '?mod=myhome&file=conversation&id='.$user['usercode'].'';
//////////////////// INDIVidUAL ITEMS
// FOLLOW
$array['followlink'] = 'phpfiles/actions.php?follow='.$user['id'].'';
$check = $db->query("SELECT id FROM friends WHERE owner = :o and who = :w limit 1",array("o"=>$_SESSION['userid'],"w"=>$user['id']),PDO::FETCH_NUM,'y');
if($check == 1)
{
$array['followtext'] = 'Unfollow';
$array['followlink'] = 'phpfiles/actions.php?unfollow='.$user['id'].'';
}
// BLOCK
$array['blocklink'] = 'phpfiles/actions.php?block='.$user['id'].'';
$check = $db->query("SELECT id FROM blocks WHERE owner = :o and who = :w limit 1",array("o"=>$_SESSION['userid'],"w"=>$user['id']),PDO::FETCH_NUM,'y');
if($check == 1)
{
$array['blocklink'] = 'phpfiles/actions.php?unblock='.$user['id'].'';
$array['blocktext'] = 'Unblock';
}
}


$array['usermenu'] = '<h2>'.$user['username'].'</h2>
<a href="../'.$array['followlink'].'"><span class="button width100">'.$array['followtext'].' &nbsp; &rsaquo;</span></a>
<div class="space10"></div>
<a href="../'.$array['blocklink'].'"><span class="button width100">'.$array['blocktext'].' &nbsp; &rsaquo;</span></a>
<div class="space10"></div>
<a href="../'.$array['messagelink'].'"><span class="button width100">Message &nbsp; &rsaquo;</span></a>
<div class="space10"></div>
<a href="../?u='.$user['id'].'"><span class="button width100">View Full Profile &nbsp; &rsaquo;</span></a>
<div class="space30"></div>';



}//  END IS A USER VIEW


///////  GROUP DISCUSSIONS
else if(isset($_GET['gid']))
{
$group = $db->row("SELECT * FROM groups WHERE id = :id",array("id"=>$_GET['gid']));
if($group['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=forum");
exit;
}
$sql = "`group` = {$group['id']}";

$array['pagetitle'] = 'Forum Topics &middot; '.$short->clean($group['title']).'';
$array['pagedescription'] = 'Forum Topics for the group: '.$short->clean($group['title']).'.  Create your sex forum topics today.';
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=groups','Groups',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?g='.$group['id'],$short->clean($group['title']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Forum Topics',4);

$array['title'] = 'Forum Topics for the group:'.$short->clean($group['title']).'';
$array['addnew'] = ' <span class="lightgrey">&middot;</span> <a href="../?mod=forum&file=add&group='.$group['id'].'">New Topic</a>';
$array['usermenu'] = '<a href="../?g='.$group['id'].'"><span class="button width100">View Group &nbsp; &rsaquo;</span></a>
<div class="space30"></div>';
}






//// ELSE A CATEGORY
else
{
/// CHECK EXISTS
$cat = $db->row("SELECT * FROM forumcategories WHERE id = :id",array("id"=>$_GET['id']));
if($cat['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=forum");
exit;
}

$array['newpost'] = (isset($_SESSION['userid'])) ? '<a href="'.$rooturl.'/?mod=forum&file=add&cat='.$cat['id'].'"><span class="button">Post a New Topic</span></a><div class="divline"></div>': '';

$sql = "category = {$cat['id']}";

$array['pagetitle'] = ''.$cat['title'].' &middot; Sex Forum';
$array['pagedescription'] = 'Sex forum topics for the category '.$cat['title'].'. Browse topics and create topics for the forum category: '.$cat['title'].'.';
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=forum','Sex Forum',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],$short->clean($cat['title']),3);

$array['title'] = ''.$cat['title'].'';
$array['usermenu'] = '';
}











/// PAGINATION
$resultcount = $db->query("SELECT id FROM forumtopics WHERE $sql ORDER BY lastpost desc", null,PDO::FETCH_NUM,'y');
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
$query = $db->query("SELECT id FROM forumtopics WHERE $sql ORDER BY lastpost desc LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$array['forums'] .= $spacer.$short->forumtopic($data['id'],'result','y');
}

////  NO RESULTS
if($array['forums'] == '')
{
$array['forums'] = 'Sorry, your search returned no results.';
}








//////   OTHER CATEGORIES
$osql = (isset($_GET['id'])) ? "WHERE id != {$_GET['id']}" : '';
$query = $db->query("SELECT * FROM forumcategories $osql ORDER BY topics DESC LIMIT 5",null,PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$plur = ($data['topics'] == '1') ? '': 's';
$array['other'] .= $spacer.'<div class="forumright fright">
'.number_format($data['topics']).' <span class="grey">Topic'.$plur.'</span>
<div class="space5"></div>
<span class="grey">'.$short->timeago($data['laststamp']).'</span>
</div>
<div class="forumleft">
<span class="onelinetext"><a href="../?mod=forum&file=category&id='.$data['id'].'">'.$data['title'].'</a></span>
<div class="space5"></div>
<span class="onelinetext">'.$data['description'].'</span></span>
</div>
<div class="clear"></div>';
}
