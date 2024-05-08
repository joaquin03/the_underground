<?php
/////////////////////////  POST FILTER AND REDIRECT
if(isset($_POST['filter']))
{
$tag = ($_POST['tags'] != '') ? '&tag='.urlencode($short->clean($_POST['tags'])) : '&tag=';
$sort = ($_POST['cat'] != '') ? '&cat='.$short->clean($_POST['cat']) : '&cat=';
header("Location: ".$array['rooturl']."/?mod=groups".$sort.$tag);
exit;
}



include(''.$serverpath.'/addons/form_validation.php');
$cattitle = '';
$tagstitle = '';
$array['tags'] = '';

///// CREATE LINK
$array['newgroup'] = (isset($_SESSION['userid'])) ? '<a href="'.$rooturl.'/?mod=groups&file=new"><span class="button">Create a Group</span></a><div class="divline"></div>' : '';


///////////////////////   SEARCH
if(isset($_GET['cat']) || isset($_GET['tag']))
{
$page->page .= $page->get_temp('templates/groups/search.htm');

//////////  SQL FOR SEX
$catsql = '';
if($_GET['cat'] != '')
{
$catdata = $db->row("SELECT * FROM groupcategories WHERE id = :u",array("u"=>$_GET['cat']));
$catsql = "AND catid = ".$_GET['cat']."";
$cattitle = $catdata['title'].' ';
}



/////   SQL FOR TAGS
$ordersql = 'ORDER BY members DESC';
$tagsql = '';
$tagsql2 = '';
$titletag = '';
if($_GET['tag'] != '')
{
$tagstitle = ' with tag: '.$_GET['tag'].'';
$array['tags'] = $_GET['tag'];
$ordersql = '';
$find = strip_tags($_GET['tag']);
$find = trim ($find);
$find = addslashes($find);
$find = $short->clean($find);
$tagsql = " AND MATCH (title,slogan,description) AGAINST ('".$find."')";
$tagsql2 = ", MATCH (title,slogan,description) AGAINST ('".$find."')";
}
//////////////////////////////////////////////////////////////////////// SHOW RESULTS



//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=groups','Groups',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],$cattitle.'Sex Groups'.$tagstitle,3);


$array['pagetitle'] = ''.$cattitle.'Sex Groups'.$tagstitle.'';
$array['pagedescription'] = ''.$cattitle.'Sex Groups'.$tagstitle.'. Find and join '.$cattitle.'sex groups near you. Free to register, and contact others for free.';
$array['h1title'] = ''.$cattitle.'Sex Groups'.$tagstitle.'';




/// PAGINATION
$resultcount = $db->query("SELECT id $tagsql2 FROM groups WHERE id > 0 $tagsql $catsql $ordersql", null,PDO::FETCH_NUM,'y');
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
$query = $db->query("SELECT id $tagsql2 FROM groups WHERE id > 0 $tagsql $catsql $ordersql LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$array['groups'] .= $spacer.$short->group($data['id'],'result');

if($x==5 || $x==10)
{
  $array['groups'] .= $spacer.$short->contentad($mobilemod);
}


}

////  NO RESULTS
if($array['groups'] == '')
{
$array['groups'] = 'Sorry, your search returned no results.';
}



}


//// NO SEARCH
else
{
$page->page .= $page->get_temp('templates/groups/index.htm');


//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Groups',2);

$array['pagetitle'] = ''.$cattitle.'Sex Groups'.$tagstitle.'';
$array['pagedescription'] = ''.$cattitle.'Sex Groups'.$tagstitle.'. Find and join '.$cattitle.'sex groups near you. Free to register, and contact others for free.';



/// LATEST
$query = $db->query("SELECT id FROM groups WHERE image != '' AND members > 0 ORDER BY id DESC LIMIT 10",null,PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
$x++;
$col = ($x < 6) ? '1': '2';

$spacer = ($x==1 || $x==6) ? '': '<div class="divline"></div>';
$array['latest'.$col.''] .= $spacer.$short->group($data['id'],'result');
}

////  POPULAR
$query = $db->query("SELECT id FROM groups WHERE image != '' ORDER BY members DESC LIMIT 10",null,PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
$x++;
$col = ($x < 6) ? '1': '2';
$spacer = ($x==1 || $x==6) ? '': '<div class="divline"></div>';
$array['popular'.$col.''] .= $spacer.$short->group($data['id'],'result');
}





//// AD
$a = $db->row("SELECT * FROM ads WHERE type = :t AND active = 'y' ORDER BY rand() LIMIT 1",array("t"=>'content'));
$array['ad1'] = '<div class="space30"></div><a href="'.$a['link'].$array['admobilelink'].'"><img src="'.$staticads.'/images/ads/'.$a['id'].'.'.$a['ext'].'" width="100%" style="max-width:'.$a['display_x'].'" border="0"alt=""/></a>
';





}












////////////////// GET CATS
$array['cats'] = '';
$query = $db->query("SELECT * FROM groupcategories",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
if($data['id'] == $_GET['cat'])
{
$array['cats'] .= '<option value="'.$data['id'].'" selected="selected">'.$data['title'].'</option>';
}
else
{
$array['cats'] .= '<option value="'.$data['id'].'">'.$data['title'].'</option>';
}
}
