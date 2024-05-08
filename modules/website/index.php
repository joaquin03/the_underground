<?php
$id = $_GET['w'];
$array['id'] = $id;
$website = $db->row("SELECT * FROM websites WHERE id = :id AND title != ''",array("id"=>$id));
if($website['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$rooturl);
exit;
}





$array['pagetitle'] = $short->clean($website['title']);
$array['pagedescription'] = 'Website profile for: '.$short->clean($website['title']).'. URL: '.$short->clean($website['url']);
$page->page .= $page->get_temp('templates/website/index.htm');

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],$website['title'],2);
///
$array['title'] = $website['title'];
//
$image = ($website['image'] != '') ? '<a href="../?w='.$id.'"><img class="fleft" border="0" height="60" width="60" src="'.$rooturl.'/images/websites/'.$website['image'].'-thumb.jpg"></a>': '';
$class = ($website['image'] == '') ? '': 'disp70';

$array['website'] = ''.$image.'
<div class="'.$class.'">
<span class="onelinetext"><a href="'.$website['url'].'" rel="nofollow" target="_blank">'.$website['title'].'</a></span>
<div class="space5"></div>
<span class="onelinetext">'.$website['description'].'</span>
<div class="space5"></div>
<span class="onelinetext"><a href="'.$website['url'].'" rel="nofollow" target="_blank">'.$short->clean($website['url']).'</a></span>
</div>
<div class="clear"></div>';


///// MENTIONS
$query = $db->query("SELECT * FROM feed WHERE websiteid = :id ORDER BY id desc",array("id"=>$website['id']),PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$spacer = ($x==1) ? '' : '<div class="divline"></div>';
$mentions .= ''.$spacer.$short->user($data['owner'],'result','n').'<span class="is minidown"></span><div class="space10"></div>'.$short->feed($data['id'],'full').'';
}

$array['mentions'] = ($mentions == '') ? '': '<div class="space30"></div><h2>Site Mentions</h2>'.$mentions;
