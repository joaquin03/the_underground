<?php
if(!isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']."");
exit;
}


$array['pagetitle'] = 'Edit Gallery';
$array['pagedescription'] = '';
$page->page .= $page->get_temp('templates/galleries/add.htm');

$array['i1'] = '';
$array['i2'] = '';


/// CHECK OWNER
$gallery = $db->row("SELECT * FROM galleries WHERE id = :id",array("id"=>$_GET['id']));
if($gallery['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=galleries");
exit;
}
$id = $gallery['id'];
$array['title'] = $short->clean($gallery['title']);

/// BOOT IF NOT OWNED OR NOT ADMIN
if($_SESSION['userid'] == '100' || $_SESSION['userid'] == $gallery['owner'])
{
}
else
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=galleries");
exit;
}





///////////   DELETE IMAGE
if(isset($_GET['delimage']))
{
/// IMAGE DATA
$theimage = $db->row("SELECT id,image FROM galleryimages WHERE id = :id AND (owner = :u OR :m1 = :m2 )",array("id"=>$_GET['delimage'],"u"=>$_SESSION['userid'],"m1"=>$sysadminid,"m2"=>$_SESSION['userid']));
if($theimage['id'] > 0)
{
/// REMOVE IMAGES
@unlink($serverpath.'/images/galleries/'.$id.'/'.$theimage['image'].'-original.jpg');
@unlink($serverpath.'/images/galleries/'.$id.'/'.$theimage['image'].'.jpg');
@unlink($serverpath.'/images/galleries/'.$id.'/'.$theimage['image'].'-thumb.jpg');

/// DELETE DATABASE ENTRY
$db->query("DELETE FROM galleryimages WHERE id = :id", array("id"=>$_GET['delimage']),PDO::FETCH_ASSOC,"n");
//// DELETE NOTIFICATIONS
$db->query("DELETE FROM notifications WHERE itemid = :id AND itemtype = 'photo'", array("id"=>$_GET['delimage']),PDO::FETCH_ASSOC,"n");
//// DELETE COMMENTS
$db->query("DELETE FROM comments WHERE itemid = :id AND type = 'photo'", array("id"=>$_GET['delimage']),PDO::FETCH_ASSOC,"n");
//// DELETE VOTES
$db->query("DELETE FROM votes WHERE itemid = :id AND type = 'photo'", array("id"=>$_GET['delimage']),PDO::FETCH_ASSOC,"n");
//// DELETE  NEWS
$db->query("DELETE FROM news WHERE itemid = :id AND type = 'photo'", array("id"=>$_GET['delimage']),PDO::FETCH_ASSOC,"n");


//// CHECK IF ONLY PIC
$count = $db->query("SELECT id FROM galleryimages WHERE gallery = :g",array("g"=>$id),PDO::FETCH_NUM,'y');
if($count == 0)
{
///////  MARK GALLERY AS INCOMPLETED
$db->query("UPDATE galleries SET completed = :c, searchable = 'n' WHERE id = :id",array("c"=>'n',"id"=>$id),PDO::FETCH_ASSOC,"n");
//////////// UPDATE USER COUNT
$count=$db->query("SELECT id FROM galleries WHERE owner = :u AND completed = 'y'",array("u"=>$gallery['owner']),PDO::FETCH_NUM,'y');
$db->query("UPDATE members SET count_galleries = :p WHERE id = :id", array("p"=>$count,"id"=>$gallery['owner']),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY NOTIFICATIONS
$db->query("DELETE FROM notifications WHERE itemid = :id AND itemtype = 'gallery'", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY COMMENTS
$db->query("DELETE FROM comments WHERE itemid = :id AND type = 'gallery'", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY VOTES
$db->query("DELETE FROM votes WHERE itemid = :id AND type = 'gallery'", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY NEWS
$db->query("DELETE FROM news WHERE itemid = :id AND type = 'gallery'", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY SEARCH
$url = '/?g='.$id;
$db->query("DELETE FROM search WHERE url = :url", array("url"=>$url),PDO::FETCH_ASSOC,"n");
}
$_SESSION['gmessage'] = 'Image Removed Successfully';
}
header("Location: ".$array['rooturl']."/?mod=galleries&file=add&id=".$id."");
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
//// CREATE FOLDER
if (!file_exists(''.$serverpath.'/images/galleries/'.$id.'')) {
    mkdir(''.$serverpath.'/images/galleries/'.$id.'', 0755, true);
}
/// CREATE CODE
$icode = $short->createcode(10);


include_once(''.$serverpath.'/addons/image.php');
$image->save($_FILES['file']['tmp_name'],'jpg','800','0','images/galleries/'.$id.'/'.$icode.'-original.jpg','100','');
$image->save($_FILES['file']['tmp_name'],'jpg','800','0','images/galleries/'.$id.'/'.$icode.'.jpg','60','');
$image->save($_FILES['file']['tmp_name'],'jpg','300','300','images/galleries/'.$id.'/'.$icode.'-thumb.jpg','20','');

/// INSERT DATABASE ENTRY
$db->query("INSERT INTO galleryimages(gallery,added,owner,image) VALUES(:g,:s,:o,:i)", array("g"=>$id,"s"=>$time,"o"=>$gallery['owner'],"i"=>$icode),PDO::FETCH_ASSOC,"n");

/// IF GALLERY WAS INCOMPLETE
if($gallery['completed'] == 'n')
{
///////  MARK GALLERY AS COMPLETED
$db->query("UPDATE galleries SET completed = :c, stamp = :s WHERE id = :id",array("c"=>'y',"s"=>$time,"id"=>$id),PDO::FETCH_ASSOC,"n");
//////////// UPDATE  MEMBER COUNT
$count=$db->query("SELECT id FROM galleries WHERE owner = :u AND completed = 'y'",array("u"=>$gallery['owner']),PDO::FETCH_NUM,'y');
$db->query("UPDATE members SET count_galleries = :p WHERE id = :id", array("p"=>$count,"id"=>$gallery['owner']),PDO::FETCH_ASSOC,"n");
/////  ADD NEWS FEED
$db->query("INSERT INTO news(owner,itemid,type,stamp) VALUES(:u,:item,:type,:st)",
array("u"=>$gallery['owner'],"item"=>$id,"type"=>'gallery',"st"=>$time),PDO::FETCH_ASSOC,"n");
}






$_SESSION['gmessage'] = 'Image Added';
header("Location:".$array['rooturl']."/?mod=galleries&file=add&id=".$id."");
exit;
}/// END IF OK
}








////////////  GET ALL THE IMAGES
$num = ($mobilemod == '') ? '8': '5';
$query = $db->query("SELECT id,image FROM galleryimages WHERE gallery = :gid ORDER BY id DESC",array("gid"=>$gallery['id']),PDO::FETCH_ASSOC,"n");
$rx = 0;
/// OPEN CURRENT ROW
$array['images'] .= '<tr>';
foreach($query as $data)
{
$rx++;
if($rx == $num)
{
$array['images'] .= '</tr><tr>';
$rx=1;
}
$array['images'] .= '<td class="one7th">
<span class="minus2around" style="text-align:center;">
<img src="'.$rooturl.'/images/galleries/'.$gallery['id'].'/'.$data['image'].'-thumb.jpg" class="maxwidth100" alt=""/>
<div class="space1"></div>
<a href="../?mod=galleries&file=add&id='.$id.'&delimage='.$data['id'].'" class="fs10">Delete</a>
<div class="space20"></div>
</span>
</td>';
}
/// CLOSE CURRENT ROW
$array['images'] .= '</tr>';

if($rx == 0)
{
$array['images'] = 'No Images to Display';
}




/*
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
//// CREATE FOLDER
if (!file_exists(''.$rooturl.'/images/galleries/'.$id.'')) {
    mkdir(''.$rooturl.'/images/galleries/'.$id.'', 0755, true);
}
/// CREATE CODE
$icode = $short->createcode(10);

include_once(''.$serverpath.'/addons/image.php');
$image->save($_FILES['file']['tmp_name'],'jpg','800','0','images/galleries/'.$id.'/'.$icode.'-original.jpg','100','');
$image->save($_FILES['file']['tmp_name'],'jpg','800','0','images/galleries/'.$id.'/'.$icode.'.jpg','60','');
$image->save($_FILES['file']['tmp_name'],'jpg','300','300','images/galleries/'.$id.'/'.$icode.'-thumb.jpg','20','');

/// INSERT DATABASE ENTRY
$db->query("INSERT INTO galleryimages(gallery,added,owner,image) VALUES(:g,:s,:o,:i)", array("g"=>$id,"s"=>$time,"o"=>$gallery['owner'],"i"=>$icode),PDO::FETCH_ASSOC,"n");

/// IF GALLERY WAS INCOMPLETE
if($gallery['completed'] == 'n')
{
///////  MARK GALLERY AS COMPLETED
$db->query("UPDATE galleries SET completed = :c, stamp = :s WHERE id = :id",array("c"=>'y',"s"=>$time,"id"=>$id),PDO::FETCH_ASSOC,"n");
//////////// UPDATE  MEMBER COUNT
$count=$db->query("SELECT id FROM galleries WHERE owner = :u AND completed = 'y'",array("u"=>$gallery['owner']),PDO::FETCH_NUM,'y');
$db->query("UPDATE members SET count_galleries = :p WHERE id = :id", array("p"=>$count,"id"=>$gallery['owner']),PDO::FETCH_ASSOC,"n");
/////  ADD NEWS FEED
$db->query("INSERT INTO news(owner,itemid,type,stamp) VALUES(:u,:item,:type,:st)",
array("u"=>$gallery['owner'],"item"=>$id,"type"=>'gallery',"st"=>$time),PDO::FETCH_ASSOC,"n");
}

$_SESSION['gmessage'] = 'Image Added';
header("Location:".$array['rooturl']."/?mod=galleries&file=add&id=".$id."");
exit;
}/// END IF OK
}
