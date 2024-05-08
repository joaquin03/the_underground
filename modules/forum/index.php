<?php
if(!isset($_GET['mod']))
{
die();
}
$page->page .= $page->get_temp('templates/forum/index.htm');

$array['pagetitle'] = 'Sex Forum';
$array['pagedescription'] = 'The Underground Sex Club Forum. Where you can discuss anything sex. Free to Join, so start creating sex topics today. The Free Sex Forum.';

/// BREADCRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Sex Forum',2);
$array['subscriptions'] = '';
$array['subscriptions-mobile'] = '';

$array['newpost'] = (isset($_SESSION['userid'])) ? '<a href="'.$rooturl.'/?mod=forum&file=add"><span class="button">Post a New Topic</span></a><div class="divline"></div>': '';

//////   CATEGORIES
$query = $db->query("SELECT * FROM forumcategories ORDER BY categoryorder ASC",null,PDO::FETCH_ASSOC,"n");
$cat = '';
$x=0;
$x2 = 0;
foreach($query as $data)
{
$x2++;
////  SEE IF WE NEED A HEADER
if($data['category'] != $cat)
{
$x++;
$x2 = 1;
////  INSERT ADS
if($x==2 || $x==4 || $x==6)
{
$array['forums'] .= '<div class="divline"></div>'.$short->contentad($mobilemod).'
<div class="divline"></div><div class="space-10"></div>';
}

$spacer = ($x==1) ? '': '<div class="space30"></div>';
$cat = $data['category'];
$array['forums'] .= $spacer.'<h2>'.$data['category'].$createtopic.'</h2><div class="space-10"></div>';


}

$spacer2 = ($x2==1) ? '': '<div class="divline"></div>';
$plur = ($data['topics'] == '1') ? '': 's';
$array['forums'] .= $spacer2.'<div class="forumright fright">
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



//////////////////////  MEMBER SUBSCRIPTIONS
if(isset($_SESSION['userid']))
{
$query = $db->query("SELECT topic FROM forumbookmarks WHERE owner = :o ORDER BY id DESC",array("o"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
if($x < 11)
{
$subs .= $spacer.$short->forumtopic($data['topic'],'result','n');
}
}
$more = ($x > 10) ? '<div class="space10"></div><a href="../?mod=forum&file=subscriptions"><span class="button">View All</span></a>': '';
$array['subscriptions'] = ($x==0) ? '': '<h2>My Subscriptions</h2><div class="space-10"></div>'.$subs.$more.'<div class="space30"></div>';


}




//////   LATEST TOPICS
$query = $db->query("SELECT id FROM forumtopics ORDER BY id DESC LIMIT 5",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$forums .= $spacer.$short->forumtopic($data['id'],'result','n');
}
if($forums != '')
{
$array['latest'] = '<h2>Latest Topics</h2><div class="space-10"></div>'.$forums.'<div class="space30"></div>';
}
