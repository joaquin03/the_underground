<?php


$gallery = $db->row("SELECT * FROM galleries WHERE id = :id AND completed = 'y' ",array("id"=>$_GET['p']));
if($gallery['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=galleries");
exit;
}



//// BREADCRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=galleries','Galleries',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],$short->clean($gallery['title']),3);

$array['pagetitle'] = ''.$short->clean($gallery['title']).'';
$array['pagedescription'] = ''.$short->clean($gallery['title']).'. '.$short->clean($gallery['description']).'. Photo Gallery on the Underground Sex Club';
$page->page .= $page->get_temp('templates/galleries/view.htm');

///
$array['gtitle'] = $short->clean($gallery['title']);
$array['interact'] = $short->interactbar('gallery',$gallery['id'],'y');
$description = ($gallery['description'] == '') ? '': $short->clean($gallery['description']);



//// AD
$array['ad1'] = '<div class="space1"></div>'.$short->contentad($mobilemod);



//////////////////////////////// POST BLACKLIST WORD ADMIN
if(isset($_POST['blword']))
{
if($_SESSION['userid'] == '100' && $_POST['blword'] != '')
{

$db->query("INSERT INTO s_blacklist_words(word,critical) VALUES(:w,:c)",
array("w"=>$_POST['phrase'],"c"=>'y'),PDO::FETCH_ASSOC,"n");

$_SESSION['gmessage'] = 'Phrase Added Successfully';
header("Location:".$array['rooturl']."/?p=".$gallery['id']);
exit;
}
}






/////////////  GET ALL THE IMAGES
$query = $db->query("SELECT id,image,views FROM galleryimages WHERE gallery = :gid ORDER BY id DESC",array("gid"=>$gallery['id']),PDO::FETCH_ASSOC,"n");
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

$array['images'] .= '<td class="one7th">
      <span class="minus2around"><a href="../?i='.$data['id'].'"><img src="'.$rooturl.'/images/galleries/'.$gallery['id'].'/'.$data['image'].'-thumb.jpg" class="maxwidth100" alt=""/></a></span>
      </td>';



}
/// CLOSE CURRENT ROW
$array['images'] .= '</tr>';




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
$array['infoblock'] = '<h2>This Gallery</h2>
'.$description.'
<div class="space10"></div>
<span class="is minieye"></span>'.number_format($views).' Photo Views
<div class="space5"></div>
<span class="is minical"></span>'.$short->timeago($gallery['stamp']).'
<div class="space5"></div>
<span class="is miniphoto"></span>'.number_format($x).' Photo'.$vplur.'
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
$array['infoblockmobile'] = '<div class="space30"></div><h2>This Gallery</h2>
'.$description.'
<div class="space10"></div>
<span class="is minieye"></span>'.number_format($views).' Photo Views
<div class="space5"></div>
<span class="is minical"></span>'.$short->timeago($gallery['stamp']).'
<div class="space5"></div>
<span class="is miniphoto"></span>'.number_format($x).' Photo'.$vplur.'
<div class="space30"></div>
'.$tagsection.'
<h2>Added By</h2>
'.$short->user($gallery['owner'],'result','n').'
'.$group.'
';
$array['infoblock'] = '';}




$array['edit'] = '';
if($_SESSION['userid'] == $gallery['owner'] || $_SESSION['userid'] == '100')
{
$array['edit'] = '<div class="space30"></div><a href="..?mod=galleries&file=add&id='.$gallery['id'].'"><span class="button">Edit Gallery &nbsp; &rsaquo;</span></a><div class="space30"></div>';
}




$array['moderator'] = '';

if($_SESSION['userid'] == '100')
{
$array['moderator'] = '
<div class="space30"></div>
<h2>Add Blacklist Word</h2>
<form action="" method="post">
<input name="phrase" type="text" class="formfield width100" id="phrase" value="" placeholder="Phrase" autocorrect="off"  autocomplete="off"   />
<input id="blword" name="blword" type="submit" value="Add" class="button ib">
</form>';
}
