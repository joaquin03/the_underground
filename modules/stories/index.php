<?php
$page->page .= $page->get_temp('templates/stories/index.htm');

$array['pagetitle'] = 'Sex Stories';
$array['pagedescription'] = 'Adult sex stories. Read and create fun, sexy and erotic sex stories.';


//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Sex Stories',2);




/// ADS
$array['ad1'] = '<div class="divline"></div>'.$short->contentad($mobilemod).'
<div class="divline"></div>';
$array['ad2'] = '<div class="divline"></div>'.$short->contentad($mobilemod).'<div class="divline"></div>';


///// CREATE LINK
$array['newstory'] = (isset($_SESSION['userid'])) ? '<a href="'.$rooturl.'/?mod=stories&file=new"><span class="button">Add a Story</span></a><div class="divline"></div>' : '';


//////   CATEGORIES
$query = $db->query("SELECT * FROM storycategories ORDER BY stories DESC",null,PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
$x++;
$spacer = ($x==1 || $x==6 || $x==11) ? '': '<div class="divline"></div>';
$col = ($x > 5) ? '2': '1';
$col = ($x > 10) ? '3': $col;

$plur = ($data['stories'] == '1') ? 'y': 'ies';
$array['topics'.$col.''] .= $spacer.'<div class="forumright fright">
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




$array['latest1'] = '';
$array['latest2'] = '';
$array['latest3'] = '';
if($mobilemod == '')
{
//////   LATEST MOBILE ONLY
$query = $db->query("SELECT id FROM stories ORDER BY id DESC LIMIT 21",null,PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
$x++;
$spacer = ($x==1 || $x==6 || $x==11) ? '': '<div class="divline"></div>';
$col = ($x > 5) ? '2': '1';
$col = ($x > 10) ? '3': $col;
$array['latest'.$col.''] .= $spacer.$short->story($data['id'],'result','y');
}
}
