<?php
if(!isset($_GET['mod']))
{
die();
}


$page->page .= $page->get_temp('templates/galleries/index.htm');





///// CREATE LINK
$array['newgallery'] = (isset($_SESSION['userid'])) ? '<a href="'.$rooturl.'/?mod=galleries&file=new"><span class="button">Create a Gallery</span></a><div class="divline"></div>' : '';

/////////////////////////  POST FILTER AND REDIRECT
if(isset($_POST['filter']))
{
$tag = ($_POST['tags'] != '') ? '&tag='.urlencode($_POST['tags']) : '';
$sort = ($_POST['sort'] != '') ? '&sort='.$_POST['sort'] : '';
header("Location: ".$rooturl."/?mod=galleries".$sort.$tag);
exit;
}
//
$array['tags'] = urldecode($_GET['tag']);
$array['anyone'] = '';
$array['women'] = '';
$array['men'] = '';
$sextitle='';
if(isset($_GET['sort']))
{
$array[$_GET['sort']] = 'selected="selected"';
$sextitle = ucwords($_GET['sort']).'';
}
else
{
$array['anyone'] = 'selected="selected"';
}
//////////  SQL FOR SEX
if($_GET['sort'] == 'women')
{
$sexsql = "members.sex = 'Female' AND ";
}
else if($_GET['sort'] == 'men')
{
$sexsql = "members.sex = 'Male' AND ";
}
else
{
$sexsql = "";
}



/////   SQL FOR TAGS
$ordersql = 'ORDER BY galleries.stamp DESC';
$tagsql = '';
$tagsql2 = '';
$titletag = '';
if($_GET['tag'] != '')
{
$ordersql = '';
$find = strip_tags($_GET['tag']);
$find = trim ($find);
$find = addslashes($find);
$tagsql = "MATCH (galleries.title,galleries.tags) AGAINST ('".$find."') AND";
$tagsql2 = ", MATCH (galleries.title,galleries.tags) AGAINST ('".$find."')";
$tagtitle = urldecode(ucwords($short->clean($_GET['tag']))).'';
}







//// BOTH TAG AND SEX
if($_GET['tag'] != '' && isset($_GET['sort']))
{
$array['title'] = 'Galleries Added By '.$sextitle.' &middot; Tag: '.$tagtitle.'';
$array['pagetitle'] = 'Photo Galleries Added By '.$sextitle.' &middot; Tag: '.$tagtitle.'';
$array['pagedescription'] = 'Amateur Photo Galleries added by '.$sextitle.' using the tag: '.$tagtitle.'. On the Underground Sex Club. Sex Galleries are added by our Club Members. Uploaded for Free.';
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=galleries','Galleries',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=galleries&sort='.strtolower($sextitle),'Added By '.$sextitle,3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Tag: '.$tagtitle,4);
}
///  JUST SEX
else if($_GET['tag'] == '' && isset($_GET['sort']))
{
$array['title'] = 'Galleries Added By '.$sextitle.'';
$array['pagetitle'] = 'Photo Galleries Added By '.$sextitle.'';
$array['pagedescription'] = 'Amateur Photo Galleries added by '.$sextitle.' for the Underground Sex Club. Sex Galleries are added by our Club Members. Uploaded for Free.';
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=galleries','Galleries',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Added by '.$sextitle,3);
}
////  JUST TAG
else if($_GET['tag'] != '' && !isset($_GET['sort']))
{
$array['title'] = 'Galleries With Tag: '.$tagtitle.'';
$array['pagetitle'] = 'Photo Galleries With Tag: '.$tagtitle.'';
$array['pagedescription'] = 'Amateur Photo Galleries using the tag: '.$tagtitle.'. On the Underground Sex Club. Sex Galleries are added by our Club Members. Uploaded for Free.';
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=galleries','Galleries',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Tag: '.$tagtitle,3);
}
/// NEITHER
else
{
$array['title'] = 'Photo Galleries';
$array['pagetitle'] = 'Sex Galleries';
$array['pagedescription'] = 'Amateur Sex Photo Galleries for the Underground Sex Club. Sex Galleries are added by our Club Members. Uploaded for Free.';

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Galleries',2);
}






/// PAGINATION
$resultcount = $db->query("SELECT galleries.*,members.id as memid,members.sex $tagsql2 FROM galleries INNER JOIN members ON galleries.owner = members.id WHERE $tagsql $sexsql completed = 'y' $ordersql", null,PDO::FETCH_NUM,'y');
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
$query = $db->query("SELECT galleries.*,members.id as memid,members.sex $tagsql2 FROM galleries INNER JOIN members ON galleries.owner = members.id WHERE $tagsql $sexsql completed = 'y' $ordersql LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$array['galleries'] .= $spacer.$short->gallery($data['id'],'large','y');

if($x==5 || $x==10)
{
  $array['galleries'] .= $spacer.$short->contentad($mobilemod);
}


}

////  NO RESULTS
if($array['galleries'] == '')
{
$array['galleries'] = 'Sorry, your search returned no results.';
}



$plur = ($resultcount == '1') ? 'y': 'ies';
$array['subheading'] = number_format($resultcount).' Galler'.$plur;
