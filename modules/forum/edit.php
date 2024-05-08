<?php
$page->page .= $page->get_temp('templates/forum/edit.htm');
if($_SESSION['userid'] != '100')
{
header("Location: ".$rooturl."?f=".$_GET['id']);
exit;
}




$postdata = $db->row("SELECT * FROM forumposts WHERE id = :id",array("id"=>$_GET['id']));
$topic = $db->row("SELECT * FROM forumtopics WHERE id = :id",array("id"=>$postdata['topic']));
$cat = $db->row("SELECT * FROM forumcategories WHERE id = :id",array("id"=>$topic['category']));

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=forum','Sex Forum',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=forum&file=category&id='.$cat['id'],$short->clean($cat['title']),3);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?f='.$topic['id'],$short->clean($topic['title']),4);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Edit Post',5);

///////////  DELETE IMAGE
if(isset($_GET['delimage']))
{
/// REMOVE IMAGES
@unlink($serverpath.'/images/forum/'.$postdata['image'].'-original.jpg');
@unlink($serverpath.'/images/forum/'.$postdata['image'].'.jpg');
@unlink($serverpath.'/images/forum/'.$postdata['image'].'-thumb.jpg');
$db->query("UPDATE forumposts SET image = :v WHERE id = :id", array("v"=>'',"id"=>$postdata['id']),PDO::FETCH_ASSOC,"n");
$_SESSION['gmessage'] = 'Image Removed Successfully';
header("Location:".$array['rooturl']."/?mod=forum&file=edit&id=".$postdata['id']);
exit;
}




$array['pagetitle'] = 'Edit Post';

$array['titlebox'] = '';
$array['t1'] = '';
$array['t2'] = '';
$array['body'] = $postdata['body'];
$array['quote'] = $postdata['quoteid'];
$array['title'] = $topic['title'];


if($postdata['original'] == 'y')
{
$array['titlebox'] = '<span class="formt">Title<span class="labelerror">'.$array['t2'].'</span></span>
<input name="title" type="text" class="formfield '.$array['t1'].'" id="title" style="width:100%;" value="'.$array['title'].'" autocorrect="off" autocapitalize="off"  autocomplete="off"   />
';
}

$array['image'] = ($postdata['image'] != '') ? '<div class="space20"></div><img style="max-height:200px;" src="'.$rooturl.'/images/forum/'.$postdata['image'].'.jpg"/><div class="space20"></div><a href="../?mod=forum&file=edit&id='.$postdata['id'].'&delimage=y"><span class="button">Delete Image</span></a>': '';





if(isset($_POST['button']))
{
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}


$db->query("UPDATE forumposts SET body = :b, quoteid = :q WHERE id = :id",
array("b"=>$post['body'],"q"=>$post['quote'],"id"=>$postdata['id']),PDO::FETCH_ASSOC,"n");

if($postdata['original'] == 'y')
{
/// UPDATE
$db->query("UPDATE forumtopics SET title = :t, searchable = 'n' WHERE id = :id",
array("t"=>$post['title'],"id"=>$topic['id']),PDO::FETCH_ASSOC,"n");
///  REMOVE SEARCH ENTRY
$turl = '/?f='.$topic['id'];
$db->query("DELETE FROM search WHERE url = :url",array("url"=>$turl),PDO::FETCH_ASSOC,"n");
}


///  DEAL WITH IMAGE IF EXISTS
if($_FILES['file']['name'] != '')
{
/// REMOVE IMAGES
@unlink($serverpath.'/images/forum/'.$postdata['image'].'-original.jpg');
@unlink($serverpath.'/images/forum/'.$postdata['image'].'.jpg');
@unlink($serverpath.'/images/forum/'.$postdata['image'].'-thumb.jpg');
  $icode = $short->createcode(10);
  include_once(''.$serverpath.'/addons/image.php');
  $image->save($_FILES['file']['tmp_name'],'jpg','800','0','images/forum/'.$icode.'-original.jpg','100','');
  $image->save($_FILES['file']['tmp_name'],'jpg','728','0','images/forum/'.$icode.'.jpg','80','');
  $image->save($_FILES['file']['tmp_name'],'jpg','160','160','images/forum/'.$icode.'-thumb.jpg','70','');
  $db->query("UPDATE forumposts SET image = :v WHERE id = :id", array("v"=>$icode,"id"=>$postdata['id']),PDO::FETCH_ASSOC,"n");
}

$_SESSION['gmessage'] = 'Post Edited Successfully';
header("Location:".$array['rooturl']."/?f=".$topic['id']);
exit;
}
