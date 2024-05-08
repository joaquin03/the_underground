<?php

$phrase = urldecode($short->clean($_GET['q']));
if($phrase == '')
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']);
exit;
}

$array['extrameta'] .= '
<meta name="robots" content="noindex,follow">';


$array['searchval'] = $phrase;
$page->page .= $page->get_temp('templates/search/index.htm');
/// BREADCRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Search Results',2);

if(isset($_GET['cx']))
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?q=".urlencode($phrase)."");
exit;
}


$array['pagetitle'] = $phrase;




//////   GET CONTENT ADS
$query = $db->query("SELECT * FROM ads WHERE type = 'content' AND active = 'y' ORDER BY rand() LIMIT 4",null,PDO::FETCH_ASSOC,"n");
$adcount = 0;
foreach($query as $data)
{
$adcount++;
$addata[''.$adcount.''] = '<a href="'.$data['link'].$array['admobilelink'].'">
<img src="'.$staticads.'/images/ads/'.$data['id'].'.'.$data['ext'].'" width="100%" style="max-width:'.$data['display_x'].'" border="0"alt=""/></a>';
}









/// PAGINATION
$resultcount = $db->query("SELECT url,type,title,description, MATCH (title, description) AGAINST ('".$phrase."') FROM search WHERE MATCH (title,description) AGAINST ('".$phrase."')", null,PDO::FETCH_NUM,'y');
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




$x=0;
$phrase = addslashes($phrase);
$query = $db->query("SELECT url,type,title,description, MATCH (title, description) AGAINST ('".$phrase."') FROM search WHERE MATCH (title,description) AGAINST ('".$phrase."') LIMIT $startnum,$perpage",array("ph"=>$phrase),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$x++;

if($data['type'] == 'member')
{
$id = str_replace('/?u=','',$data['url']);
$memberdata = $db->row("SELECT image FROM members WHERE id = :u",array("u"=>$id));
$icon = ($memberdata['image'] != '') ? '<img class="fleft" border="0" height="40" width="40" src="'.$staticurl.'/images/members/'.$memberdata['image'].'-thumb.jpg">': '<img class="fleft" border="0" height="40" width="40" src="'.$staticurl.'/images/default/'.$data['type'].'-thumb.jpg">';
}

else if($data['type'] == 'group')
{
$id = str_replace('/?g=','',$data['url']);
$group = $db->row("SELECT image FROM groups WHERE id = :u",array("u"=>$id));
$icon = ($group['image'] != '') ? '<img class="fleft" border="0" height="40" width="40" src="'.$staticurl.'/images/groups/'.$group['image'].'-thumb.jpg">': '<img class="fleft" border="0" height="40" width="40" src="'.$staticurl.'/images/default/'.$data['type'].'-thumb.jpg">';
}

else
{
$icon= '<img class="fleft" border="0" height="40" width="40" src="'.$staticurl.'/images/default/'.$data['type'].'-thumb.jpg">';
}



$array['results'] .= '<div class="divline"></div>
<a href="..'.$data['url'].'">
<span class="fleft">
'.$icon.'
</span>
<span class="disp50 block">
<span class="onelinetext blue">'.$short->clean($data['title']).'</span>
<span id="space5" class="block"></span>
<span class="onelinetext black">'.$short->clean($data['description']).'</span>
</span>
</a>
<div class="clear"></div>';


//////  DISPLAY AD AND INCREASE COUNT

if($x==5)
{
$array['results'] .= '<div class="divline"></div>'.$addata['1'].'';
}
if($x==10)
{
$array['results'] .= '<div class="divline"></div>'.$addata['2'].'';
}
if($x==15)
{
$array['results'] .= '<div class="divline"></div>'.$addata['3'].'';
}


}


if($array['results'] == '')
{
$array['results'] = '<div class="space10"></div><div class="divline"></div><div class="space10"></div>
<img class="fleft" border="0" height="40" width="40" src="'.$array['rooturl'].'/images/search-none.png">
<div class="disp50">
<span class="onelinetext">No Results</span>
<span id="space5" class="block"></span>
<span class="onelinetext ">Your search returned no results. Please try again, or use the menu to navigate.</span>
</div>';
}
