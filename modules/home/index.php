<?php
if(!isset($_GET['mod']))
{
die();
}
$array['pagetitle'] = '';
$array['pagetitlejoiner'] = '';
$array['pagedescription'] = 'Join the underground sex club, and chat with other members who are also interested in sex. The underground sex club is free to join, and free to chat.';
$page->page .= $page->get_temp('templates/home/index.htm');

$array['breadcrumbs'] = '';



include($serverpath.'/addons/news.php');
/// PAGINATION
$resultcount = $db->query("SELECT id FROM news ORDER BY stamp DESC", null,PDO::FETCH_NUM,'y');
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
$query = $db->query("SELECT id FROM news ORDER BY stamp DESC LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
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








/// LOCAL
$query = $db->query("SELECT url,suburb,state FROM towns WHERE local_active = 'y' ORDER BY rand() LIMIT 15", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
$a1 = array(
'1' => 'Women',
'2' => 'Sluts'
);
$a2 = rand(1,2);
$pagetype = $a1[$a2];

$array['local'] .= '<span class="onelinetext"><span class="red fstyle1 fs22 m0">&rsaquo;</span> &nbsp; <a href="'.$rooturl.'/?local-'.strtolower($pagetype).'='.$data['url'].'">'.$data['suburb'].' ('.$data['state'].') Local '.$pagetype.'</a></span>';
}
