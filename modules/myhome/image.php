<?php
ini_set('memory_limit', '256M');

if(!isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']."/?mod=login");
exit;
}




$array['pagetitle'] = 'Edit Profile Picture';
$array['pagedescription'] = '';
$page->page .= $page->get_temp('templates/myhome/image.htm');

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Edit Profile Picture',2);


$array['i1'] = '';
$array['i2'] = '';



///////////////////////////////////////// DELETE
if(isset($_POST['del']))
{
/// REMOVE IMAGES
@unlink($serverpath.'/images/members/'.$member['image'].'-original.jpg');
@unlink($serverpath.'/images/members/'.$member['image'].'.jpg');
@unlink($serverpath.'/images/members/'.$member['image'].'-thumb.jpg');
//
$db->query("UPDATE members SET image = '' WHERE id = :id",array("id"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");


$_SESSION['gmessage'] = 'Image Deleted';
header("Location:".$array['rooturl']."/?mod=myhome&file=image");
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
@unlink($serverpath.'/images/members/'.$member['image'].'-original.jpg');
@unlink($serverpath.'/images/members/'.$member['image'].'.jpg');
@unlink($serverpath.'/images/members/'.$member['image'].'-thumb.jpg');

$icode = $short->createcode(10);

include_once(''.$serverpath.'/addons/image.php');
$image->save($_FILES['file']['tmp_name'],'jpg','800','0','images/members/'.$icode.'-original.jpg','100','');
$image->save($_FILES['file']['tmp_name'],'jpg','500','0','images/members/'.$icode.'.jpg','60','');
$image->save($_FILES['file']['tmp_name'],'jpg','150','150','images/members/'.$icode.'-thumb.jpg','20','');

$db->query("UPDATE members SET image = :a WHERE id = :id",array("a"=>$icode,"id"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");



$_SESSION['gmessage'] = 'Image Updated';
header("Location:".$array['rooturl']."/?mod=myhome&file=image");
exit;
}
}




/////////////////////////////////
$array['image'] = ($member['image'] != '') ? '<img src="'.$rooturl.'/images/members/'.$member['image'].'.jpg" class="maxwidth100"/><div class="space10"></div><form id="form1" name="form1" method="post" action=""><input type="submit"  id="del" name="del" class="button bgblack" value="Remove Image" ></form>': 'No Image';
