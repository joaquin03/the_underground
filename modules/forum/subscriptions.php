<?php
if(!isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']."/?mod=login");
exit;
}


$page->page .= $page->get_temp('templates/forum/subscriptions.htm');



$sql = '';
$query = $db->query("SELECT topic FROM forumbookmarks WHERE owner = :o ORDER BY id DESC",array("o"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$sql .= " OR id = ".$data['topic']."";
}





$array['pagetitle'] = 'Forum Subscriptions &middot; Sex Forum';
$array['pagedescription'] = 'Sex forum topics you have bookmarked.';

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=forum','Sex Forum',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Bookmarked Topics',3);

$array['title'] = 'Bookmarked Topics';
$array['usermenu'] = '';










/// PAGINATION
$resultcount = $db->query("SELECT id FROM forumtopics WHERE id = 1 $sql ORDER BY lastpost desc", null,PDO::FETCH_NUM,'y');
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
$query = $db->query("SELECT id FROM forumtopics WHERE id = 1 $sql ORDER BY lastpost desc LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
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




$rplur = ($resultcount == 1) ? '': 's';
$array['subtitle'] = ''.number_format($resultcount).' Topic'.$rplur.'';






//////   OTHER CATEGORIES
$osql = (isset($_GET['id'])) ? "WHERE id != {$_GET['id']}" : '';
$x=0;
$query = $db->query("SELECT * FROM forumcategories $osql ORDER BY topics DESC LIMIT 5",null,PDO::FETCH_ASSOC,"n");
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
