<?php
$page->page .= $page->get_temp('templates/stories/category.htm');






/////////////  STORIES BY A USER
if(isset($_GET['uid']))
{
$user = $db->row("SELECT * FROM members WHERE id = :id",array("id"=>$_GET['uid']));
if($user['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=stories");
exit;
}

$sql = "owner = {$user['id']}";

$array['pagetitle'] = 'Sex Stories &middot; '.$user['username'].'';
$array['pagedescription'] = 'Sex stories by '.$user['username'].' Read and create fun, sexy and erotic sex stories.';
$array['title'] = 'Sex Stories by '.$user['username'].'';

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=members','Members',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?u='.$user['id'],$short->clean($user['username']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Sex Stories',4);


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

//// ELSE A CATEGORY
else
{
/// CHECK EXISTS
$cat = $db->row("SELECT * FROM storycategories WHERE id = :id",array("id"=>$_GET['id']),PDO::FETCH_ASSOC,"n");
if($cat['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=stories");
exit;
}

$sql = "catid = {$cat['id']}";

$array['pagetitle'] = ''.$cat['title'].' Sex Stories';
$array['pagedescription'] = ''.$cat['title'].' sex stories. Read and create fun, sexy and erotic sex stories for the category '.$cat['title'].'.';
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=stories','Sex Stories',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],$short->clean($cat['title']),3);

$array['title'] = ''.$cat['title'].' Sex Stories';
$array['usermenu'] = '';

}







///// CREATE LINK
$array['newstory'] = (isset($_SESSION['userid'])) ? '<a href="'.$rooturl.'/?mod=stories&file=new"><span class="button">Add a Story</span></a><div class="divline"></div>' : '';






/// PAGINATION
$resultcount = $db->query("SELECT id FROM stories WHERE $sql ORDER BY id desc", null,PDO::FETCH_NUM,'y');
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
$query = $db->query("SELECT id FROM stories WHERE $sql ORDER BY id desc LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$array['stories'] .= $spacer.$short->story($data['id'],'result','y');
}

////  NO RESULTS
if($array['stories'] == '')
{
$array['stories'] = 'Sorry, your search returned no results.';
}






$rplur = ($resultcount == 1) ? 'y': 'ies';
$array['subtitle'] = ''.number_format($resultcount).' Stor'.$rplur.'';





//////   OTHER CATEGORIES
$x=0;
$osql = (isset($_GET['id'])) ? "WHERE id != {$_GET['id']}" : '';
$query = $db->query("SELECT * FROM storycategories $osql ORDER BY stories DESC LIMIT 5",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$plur = ($data['stories'] == '1') ? 'y': 'ies';
$array['other'] .= $spacer.'<div class="forumright fright">
'.number_format($data['stories']).' <span class="grey">Stor'.$plur.'</span>
<div class="space5"></div>
<span class="grey">'.$short->timeago($data['laststamp']).'</span>
</div>
<div class="forumleft">
<span class="onelinetext"><a href="../?mod=stories&file=category&id='.$data['id'].'">'.$data['title'].'</a></span>
<div class="space5"></div>
<span class="onelinetext">'.$data['description'].'</span></span>
</div>
<div class="clear"></div>';
}
