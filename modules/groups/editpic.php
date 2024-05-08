<?php


$id = $_GET['id'];
$array['id'] = $id;
$array['i1'] = '';
$array['i2'] = '';



$group = $db->row("SELECT * FROM groups WHERE id = :id",array("id"=>$id));

if($group['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=groups");
exit;
}


/// BOOT IF NOT OWNED OR NOT ADMIN
if($_SESSION['userid'] != $group['owner'] && $_SESSION['userid'] != $sysadminid)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=groups");
exit;
}




$array['pagetitle'] = 'Edit Pic';
$array['pagedescription'] = '';
$page->page .= $page->get_temp('templates/groups/editpic.htm');


//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=groups','Groups',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?g='.$id,$short->clean($group['title']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Edit Group Picture',4);


///////////////////////////////////////// DELETE
if(isset($_POST['del']))
{
/// REMOVE IMAGES
@unlink($serverpath.'/images/groups/'.$groups['image'].'-original.jpg');
@unlink($serverpath.'/images/groups/'.$groups['image'].'.jpg');
@unlink($serverpath.'/images/groups/'.$groups['image'].'-thumb.jpg');

//
$db->query("UPDATE groups SET image = '' WHERE id = :id",array("id"=>$id),PDO::FETCH_ASSOC,"n");


$_SESSION['gmessage'] = 'Image Deleted';
header("Location:".$array['rooturl']."/?mod=groups&file=editpic&id=".$id);
exit;
}



///////////////////////////////////////// NEW IMAGE
if(isset($_POST['button']))
{
$ok = 'y';
/// TOTAL SIZE CHECK
if($_FILES['file']['size'] > (1048576*5))// 5MB
{
$ok = 'n';$array['i1'] = 'error';	$array['i2'] = 'must be less than 5MB';
}
////  CHECK MINIMUM WidTH
$filename = $_FILES['file']['tmp_name'];
list($width_orig, $height_orig) = getimagesize($filename);
if($width_orig < 500)
{
$ok = 'n';$array['i1'] = 'error';	$array['i2'] = 'must be a minimum of 500px wide';
}
/// CHECK EXTENSION
$ext = end((explode(".", $_FILES['file']['name'])));
if($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'gif')
{
$ok = 'n';$array['i1'] = 'error';	$array['i2'] = 'must be: .jpg .jpeg .png .gif';
}
//// IF THERE IS AN UPLOADED IMAGE
if($_FILES['file']['name'] == '')
{
$ok = 'n';$array['i1'] = 'error';	$array['i2'] = 'is Not Selected';
}

if($ok == 'y')
{
@unlink($serverpath.'/images/groups/'.$group['image'].'-original.jpg');
@unlink($serverpath.'/images/groups/'.$group['image'].'.jpg');
@unlink($serverpath.'/images/groups/'.$group['image'].'-thumb.jpg');

$icode = $short->createcode(10);

include_once(''.$serverpath.'/addons/image.php');
$image->save($_FILES['file']['tmp_name'],'jpg','800','0','images/groups/'.$icode.'-original.jpg','100','');
$image->save($_FILES['file']['tmp_name'],'jpg','500','0','images/groups/'.$icode.'.jpg','60','');
$image->save($_FILES['file']['tmp_name'],'jpg','150','150','images/groups/'.$icode.'-thumb.jpg','20','');


$db->query("UPDATE groups SET image = :a WHERE id = :id",array("a"=>$icode,"id"=>$id),PDO::FETCH_ASSOC,"n");


$_SESSION['gmessage'] = 'Image Updated';
header("Location:".$array['rooturl']."/?g=".$id);
exit;
}
}


$array['image'] = ($group['image'] != '') ? '<img src="'.$rooturl.'/images/groups/'.$group['image'].'.jpg" class="maxwidth100"/><div class="space10"></div><form id="form1" name="form1" method="post" action=""><input type="submit"  id="del" name="del" class="button bgblack" value="Remove Image" ></form>': 'No Image';
