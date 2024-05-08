<?php
$page->page .= $page->get_temp('templates/personals/index.htm');





$pagetitle = 'Personals';
$array['dropfield'] = '';
$array['otherzones'] = '';
$array['tags'] = '';
$titlemisc = 'Adult';
$array['clear'] = '';


///// CREATE LINK
$array['postad'] = (isset($_SESSION['userid'])) ? '<a href="'.$rooturl.'/?mod=personals&file=new"><span class="button">Post a New Ad</span></a><div class="divline"></div>' : '';



////  GET TAGS
$ordersql = 'ORDER BY stamp DESC';
$tagsql = '';
$tagsql2 = '';
if($_GET['tags'] != '')
{
$ordersql = '';
$tags = urldecode($_GET['tags']);
$array['tags'] = $tags;
$find = strip_tags($_GET['tags']);
$find = trim ($find);
$find = addslashes($find);
$tagsql = " AND MATCH (alltext) AGAINST ('".$find."')";
$tagsql2 = ", MATCH (alltext) AGAINST ('".$find."')";
}





//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=personals','Personals',2);


////  GET CAT LINK
$catsql = '';
if($_GET['cat'] != '')
{
$catdata = $db->row("SELECT * FROM classifieds_categories WHERE id = :id",array("id"=>$_GET['cat']));
$catlink = '&cat='.$catdata['id'];
$precat = $catdata['title'].' ';
$pagetitle = $catdata['title'];
$titlemisc = $catdata['title'];
$catsql = "AND category = {$_GET['cat']}";
$breadpre = 'Personals|s|../?mod=personals|,|';
}




//////////////  POST AND REDIRECT
if(isset($_POST['button']))
{
$cat = ($_POST['cat'] != '') ? '&cat='.$_POST['cat'] : '';
$tags = ($_POST['tags'] != '') ? '&tags='.urlencode($_POST['tags']) : '';
if($_POST['co'] != '')
{
$add = '&co='.$_POST['co'];
}
else if($_POST['st'] != '')
{
$add = '&co='.$_GET['co'].'&st='.$_POST['st'];
}
else if($_POST['ar'] != '')
{
$add = '&co='.$_GET['co'].'&st='.$_GET['st'].'&ar='.$_POST['ar'];
}
else
{
$add = ($_GET['co'] != '') ? '&co='.$_GET['co'] : '';
$add .= ($_GET['st'] != '') ? '&st='.$_GET['st'] : '';
$add .= ($_GET['ar'] != '') ? '&ar='.$_GET['ar'] : '';
}
//
header("Location: ".$array['rooturl']."/?mod=personals".$add.$cat.$tags);
exit;
}








if(isset($_GET['co']) && isset($_GET['st']) && isset($_GET['ar']))
{
$country = $db->row("SELECT * FROM loc_countries WHERE id = :id",array("id"=>$_GET['co']));
$state = $db->row("SELECT * FROM loc_states WHERE id = :id",array("id"=>$_GET['st']));
$area = $db->row("SELECT * FROM loc_areas WHERE id = :id",array("id"=>$_GET['ar']));
$array['pagetitle'] = $precat.$area['title'].' &middot; Adult Personals';
$array['pagedescription'] = $titlemisc.' Personals for '.$area['title'].' '.$state['code'].' '.$country['code'].'. Free to post. Find who you are looking for in '.$area['title'].' today with out adult classifieds.';


/// BREADCRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=personals'.$catlink,$pagetitle,3);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=personals&co='.$country['id'].$catlink,$country['title'],4);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=personals&co='.$country['id'].'&st='.$state['id'].$catlink,$state['title'],5);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],$area['title'],6);





$array['title'] = $precat.$area['title'];
///
$sql = "AND country = {$_GET['co']} AND state = {$_GET['st']} AND area = {$_GET['ar']}";
///
$array['dropfield'] = '';
///////////// Other Zones
$query = $db->query("SELECT * FROM loc_areas WHERE id != :ar AND state = :st AND country = :c ORDER BY id ASC",array("ar"=>$_GET['ar'],"st"=>$_GET['st'],"c"=>$_GET['co']),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$countrylist .= '<a alt="'.$precat.$data['title'].'" title="'.$precat.$data['title'].'" href="/?mod=personals&co='.$_GET['co'].'&st='.$_GET['st'].'&ar='.$data['id'].$catlink.'">'.$data['title'].'</a><div id="space1"></div>';
}
if($countrylist != '')
{
$array['otherzones'] = '<h2>Other '.$state['code'].' Areas</h2>'.$countrylist.'<div class="space30"></div>';
}
}





else if(isset($_GET['co']) && isset($_GET['st']) && !isset($_GET['ar']))
{
$country = $db->row("SELECT * FROM loc_countries WHERE id = :id",array("id"=>$_GET['co']));
$state = $db->row("SELECT * FROM loc_states WHERE id = :id",array("id"=>$_GET['st']));
$array['pagetitle'] = $precat.$state['title'].' &middot; Adult Personals';
$array['pagedescription'] = $titlemisc.' Personals for '.$state['title'].' '.$country['code'].'. Free to post. Find who you are looking for in '.$state['title'].' today with our adult classifieds.';

/// BREADCRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=personals'.$catlink,$pagetitle,3);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=personals&co='.$country['id'].$catlink,$country['title'],4);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],$state['title'],5);


$array['title'] = $precat.$state['title'];
///
$sql = "AND country = {$_GET['co']} AND state = {$_GET['st']}";
////////////////// GET DROP LIST
$drop = '';
$query = $db->query("SELECT * FROM loc_areas WHERE state = :st ORDER BY id ASC",array("st"=>$_GET['st']),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$drop .= '<option value="'.$data['id'].'">'.$data['title'].'</option>';
}
if($drop != '<option value="">Any</option>')
{
$array['dropfield'] = '<select name="ar" class="formfield dropform inline mr5 mw120"  autocorrect="off" autocapitalize="off" autocomplete="off">
    <option value="">All Areas</option>
'.$drop.'
</select>';
}
///////////// Other Zones
$query = $db->query("SELECT * FROM loc_states WHERE id != :st AND country = :c ORDER BY id ASC",array("st"=>$_GET['st'],"c"=>$_GET['co']),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$countrylist .= '<a alt="'.$precat.$data['title'].'" title="'.$precat.$data['title'].'" href="/?mod=personals&co='.$_GET['co'].'&st='.$data['id'].$catlink.'">'.$data['title'].'</a><div id="space1"></div>';
}
if($countrylist != '')
{
$array['otherzones'] = '<h2>Other '.$country['code'].'  States</h2>'.$countrylist.'<div class="space30"></div>';
}
}





else if(isset($_GET['co']) && !isset($_GET['st']) && !isset($_GET['ar']))
{
$country = $db->row("SELECT * FROM loc_countries WHERE id = :id",array("id"=>$_GET['co']));
$array['pagetitle'] = $precat.$country['title'].' &middot; Adult Personals';
$array['pagedescription'] = $titlemisc.' Personals for '.$country['title'].'. Free to post. Find who you are looking for in '.$country['title'].' today.';

/// BREADCRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=personals'.$catlink,$pagetitle,3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],$country['title'],4);


$array['title'] = $precat.$country['title'];
///
$sql = "AND country = {$_GET['co']}";
////////////////// GET DROP LIST
$drop = '';
$query = $db->query("SELECT * FROM loc_states WHERE country = :c ORDER BY id ASC",array("c"=>$_GET['co']),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$drop .= '<option value="'.$data['id'].'">'.$data['title'].'</option>';
}
if($drop != '<option value="">Any</option>')
{
$array['dropfield'] = '
<select name="st" class="formfield dropform inline mr5 mw120"  autocorrect="off" autocapitalize="off" autocomplete="off">
    <option value="">All States</option>
'.$drop.'
</select>';
}
///////////// Other Zones
$query = $db->query("SELECT * FROM loc_countries WHERE id != :c ORDER BY id ASC",array("c"=>$_GET['co']),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$countrylist .= '<a alt="'.$precat.$data['title'].'" title="'.$precat.$data['title'].'" href="/?mod=personals&co='.$data['id'].$catlink.'">'.$data['title'].'</a><div id="space1"></div>';
}
if($countrylist != '')
{
$array['otherzones'] = '<h2>Other Countries</h2>'.$countrylist.'<div class="space30"></div>';
}
}






else
{
$array['pagetitle'] = $titlemisc.' Personals';
$array['pagedescription'] = 'Personals on the Underground Sex Club. Free to post. Find who you are looking for today.';
$array['title'] = $pagetitle;
if(isset($_GET['cat']))
{
$array['pagetitle'] = $titlemisc.' Personals';
$array['pagedescription'] = $titlemisc.' Personals on the Underground Sex Club. Free to post to '.$titlemisc.'. Find who you are looking for today.';
/// BREADCRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],$titlemisc,3);


}
///
$sql = '';
////////////////// GET DROP LIST
$drop = '';
$query = $db->query("SELECT * FROM loc_countries ORDER BY id ASC",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$drop .= '<option value="'.$data['id'].'">'.$data['title'].'</option>';
}
$array['dropfield'] = '<select name="co" class="formfield dropform inline mr5 mw120" autocorrect="off" autocapitalize="off" autocomplete="off">
    <option value="">All Countries</option>
'.$drop.'
</select>';
$array['otherzones'] = '';
}











/// RENDER CATEGORY SELECTOR
$query = $db->query("SELECT * FROM classifieds_categories ORDER BY id ASC", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
$selected = ($data['id'] == $catdata['id']) ? 'selected="selected"': '';
$array['cats'] .= '<option value="'.$data['id'].'" '.$selected.'>'.$data['title'].'</option>';
}







//// CLEAR LINK
if(isset($_GET['co']) || isset($_GET['st']) || isset($_GET['ar']) || isset($_GET['cat']) || isset($_GET['tags']))
{
$array['clear'] = '&nbsp;&nbsp;&nbsp;<a href="../?mod=personals">Clear Location</a>';
}






/// PAGINATION
$resultcount = $db->query("SELECT id $tagsql2 FROM classifieds WHERE title != '' AND delstamp = 0 $sql $catsql $tagsql $ordersql", null,PDO::FETCH_NUM,'y');
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
$query = $db->query("SELECT id $tagsql2 FROM classifieds WHERE title != '' AND delstamp = 0 $sql $catsql $tagsql $ordersql LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$array['personals'] .= $spacer.$short->personal($data['id'],'result','y');

if($x==5 || $x==10)
{
  //// AD
  $array['personals'] .= $spacer.$short->contentad($mobilemod);
}



}

////  NO RESULTS
if($array['personals'] == '')
{
$array['personals'] = 'Sorry, your search returned no results.';
}

$plur = ($resultcount == '1') ? '': 's';
$array['count'] = number_format($resultcount).' Ad'.$plur;
