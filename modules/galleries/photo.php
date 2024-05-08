<?php
if(!isset($_GET['mod']))
{
die();
}



$photo = $db->row("SELECT * FROM galleryimages WHERE id = :id",array("id"=>$_GET['i']));
if($photo['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=galleries");
exit;
}
$gallery = $db->row("SELECT * FROM galleries WHERE id = :id",array("id"=>$photo['gallery']));



//// INCREASE VIEWS
$views = $photo['views']+1;
$db->query("UPDATE galleryimages SET views = :t WHERE id = :id", array("t"=>$views,"id"=>$photo['id']),PDO::FETCH_ASSOC,"n");





//// AD
$array['ad1'] = $short->contentad($mobilemod);







/////////////  GET ALL THE IMAGES
$query = $db->query("SELECT id,image FROM galleryimages WHERE gallery = :gid ORDER BY id DESC",array("gid"=>$gallery['id']),PDO::FETCH_ASSOC,"n");
$prev = '';
$next = '';
$first = '';
$last = '';
$theone = 0;
$x = 0;
$rx = 0;
$views = 0;
$likes = 0;
$comments = 0;
/// OPEN CURRENT ROW
$array['images'] .= '<tr>';
foreach($query as $data)
{
$rx++;
$x++;
if($rx == 8)
{
$array['images'] .= '</tr><tr>';
$rx=1;
}
$views = $views+$data['views'];
/// MARK FIRST id
if($x==1){$first = $data['id'];}
/// IF THE LAST RUN WAS THE ONE
if($theone == $x-1)
{
$next = $data['id'];
}
//// IF THIS IS THE CURRENT id
if($data['id'] == $photo['id'])
{
$theone = $x;
$prev = $last;
}
/////  MARK LAST id JUST RUN
$last = $data['id'];
$array['images'] .= '<td class="one7th">
      <span class="minus2around"><a href="../?i='.$data['id'].'"><img src="'.$rooturl.'/images/galleries/'.$gallery['id'].'/'.$data['image'].'-thumb.jpg" class="maxwidth100" alt=""/></a></span>
      </td>';
}

/// CLOSE CURRENT ROW
$array['images'] .= '</tr>';

$previd = ($prev != '') ? $prev : $last;
$nextid = ($next != '') ? $next : $first;


$array['prevnext'] = ($x==1) ? '': '<a href="../?i='.$previd.'"><span class="fleft button">&lsaquo; &nbsp; Prev Photo</span></a>
<a href="../?i='.$nextid.'"><span class="fright button">Next Photo &nbsp; &rsaquo;</span></a>
<div class="clear"></div>
<div class="space10"></div>
';

////
$array['image'] = $rooturl.'/images/galleries/'.$gallery['id'].'/'.$photo['image'].'.jpg';




//// BREADCRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=galleries','Galleries',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?p='.$gallery['id'],$short->clean($gallery['title']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Photo '.$theone.' of '.$x.'',4);




///////
$array['pagetitle'] = 'Photo '.$theone.' of '.$x.' from the Gallery: '.$short->clean($gallery['title']).'';
$array['pagedescription'] = 'Photo '.$theone.' of '.$x.' from the Gallery: '.$short->clean($gallery['title']).'. '.$short->clean($gallery['description']).'. Photo from a Gallery on the Underground Sex Club';
$page->page .= $page->get_temp('templates/galleries/photo.htm');

///
$array['gtitle'] = $short->clean($gallery['title']);
$array['interact'] = $short->interactbar('photo',$photo['id'],'y');






////
$vplur = ($x==1) ? '': 's';
/// GROUP
if($gallery['group'] != '' && $gallery['group'] != 0)
{
$group = '<div class="space30"></div><h2>For the Group</h2>'.$short->group($gallery['group'],'result').'';
}



/////  TAGS
$tags = explode(" ", $gallery['tags']);
foreach($tags as $key => $tag)
{
if($tag != '')
{
$tagb .= '<a href="'.$rooturl.'/?mod=galleries&tag='.urlencode($tag).'"><span class="tagsbutton">'.stripslashes($tag).'</span></a> ';
}
}
$tagsection = ($tagb == '') ? '': '<h2>Tags</h2>'.$tagb.'<div class="space20"></div>';


if($mobilemod == '')
{
$array['infoblock'] = '<a href="../?p='.$gallery['id'].'"><span class="button width100">&lsaquo; &nbsp; Back to Gallery</span></a>
<div class="space20"></div>
<h2>This Photo</h2>
<div class="space10"></div>
<span class="is minieye"></span>'.number_format($photo['views']).' Photo Views
<div class="space5"></div>
<span class="is minical"></span>'.$short->timeago($photo['added']).'
<div class="space5"></div>
<span class="onelinetext"><span class="is miniphoto"></span>Gallery: <a href="../?p='.$gallery['id'].'">'.$short->clean($gallery['title']).'</a></span>
<div class="space30"></div>
'.$tagsection.'
<h2>Added By</h2>
'.$short->user($gallery['owner'],'result','n').'
'.$group.'
';
$array['infoblockmobile'] = '';
}
else
{
$array['infoblockmobile'] = '<div class="space10"></div><a href="../?p='.$gallery['id'].'"><span class="button width100">&lsaquo; &nbsp; Back to Gallery</span></a>
<div class="space30"></div><h2>This Photo</h2>
<div class="space10"></div>
<span class="is minieye"></span>'.number_format($photo['views']).' Photo Views
<div class="space5"></div>
<span class="is minical"></span>'.$short->timeago($photo['added']).'
<div class="space5"></div>
<span class="onelinetext"><span class="is miniphoto"></span>Gallery: <a href="../?p='.$gallery['id'].'">'.$short->clean($gallery['title']).'</a></span>
<div class="space30"></div>
'.$tagsection.'
<h2>Added By</h2>
'.$short->user($gallery['owner'],'result','n').'
'.$group.'
';
$array['infoblock'] = '';}




$array['edit'] = '';
if($gallery['owner'] == $_SESSION['id'])
{
$array['edit'] = '<a href="..?mod=galleries&file=add&id='.$gallery['id'].'"><span class="button width100">Edit Gallery &nbsp; &rsaquo;</span></a><div class="space20"></div>';
}




/////   MODERATOR
$array['moderation'] = '';
if($sysadminid == $_SESSION['userid'])
{
$array['moderation'] = '<h2>Moderation<h2>
<a href="'.$rooturl.'/phpfiles/actions.php?delphoto='.$photo['id'].'"><span class="button width100">Delete Image &nbsp; &rsaquo;</span></a><div class="space20"></div>';
}
