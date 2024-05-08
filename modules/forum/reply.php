<?php
if(!isset($_SESSION['userid']))
{
header("Location: ".$rooturl);
exit;
}

$page->page .= $page->get_temp('templates/forum/reply.htm');

$id = $_GET['id'];
$array['id'] = $id;
$topic = $db->row("SELECT * FROM forumtopics WHERE id = :id",array("id"=>$id));
if($topic['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=forum");
exit;
}
//
$cat = $db->row("SELECT * FROM forumcategories WHERE id = :id",array("id"=>$topic['category']));

if(isset($_GET['quote']))
{
$quote = $db->row("SELECT * FROM forumposts WHERE topic = :tid AND id = :qid",array("tid"=>$topic['id'],"qid"=>$_GET['quote']));
}
$array['quotebox'] = ($quote['body'] == '') ? '': '<span class="formt">Included Quote</span><div class="quotebox"><span class="lightgrey">Quoting </span>'.$short->user($quote['addedby'],'text','n').'<span class="lightgrey">: </span><span class="italic">'.nl2br($short->clean($quote['body'])).'</span></div>';




//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=forum','Sex Forum',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=forum&file=category&id='.$cat['id'],$short->clean($cat['title']),3);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?f='.$topic['id'],$short->clean($topic['title']),4);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Reply',5);



$array['pagetitle'] = 'Reply: '.$short->clean($topic['title']).'';

///
$array['title'] = $short->clean($topic['title']);
$array['r1'] = '';
$array['r2'] = '';
$array['i1'] = '';
$array['i2'] = '';
$array['body'] = '';






//// POST NEW MESSAGE
if(isset($_POST['button']))
{
$array['body'] = $_POST['body'];
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}
$ok = 'y';

////   CHECK MESSAGE HAS A BODY
if($post['body'] == '')
{
$ok = 'n';$array['r1'] = 'error';	$array['r2'] = 'is Required';
}
////    PROCESS IMAGE
if($ok == 'y' && $_FILES['file']['name'] != '')
{
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
}

///
if($ok == 'y')
{
/// INSERT TOPIC POST
$db->query("INSERT INTO forumposts(body,topic,added,addedby,quoteid) VALUES(:b,:t,:a,:o,:q)",
array("b"=>$post['body'],"t"=>$id,"a"=>$time,"o"=>$_SESSION['userid'],"q"=>$quote['id']),PDO::FETCH_ASSOC,"n");
$newid = $db->lastInsertId();

///  DEAL WITH IMAGE IF EXISTS
if($_FILES['file']['name'] != '')
{
$icode = $short->createcode(10);
include_once(''.$serverpath.'/addons/image.php');
$image->save($_FILES['file']['tmp_name'],'jpg','800','0','images/forum/'.$icode.'-original.jpg','100','');
$image->save($_FILES['file']['tmp_name'],'jpg','728','0','images/forum/'.$icode.'.jpg','80','');
$image->save($_FILES['file']['tmp_name'],'jpg','160','160','images/forum/'.$icode.'-thumb.jpg','70','');
$db->query("UPDATE forumposts SET image = :v WHERE id = :id", array("v"=>$icode,"id"=>$newid),PDO::FETCH_ASSOC,"n");
}

/////   CALCULATE TOPIC POSTS COUNT AND TIME AND UPDATE
$count = $db->query("SELECT id FROM forumposts WHERE topic = :t",array("t"=>$topic['id']),PDO::FETCH_NUM,'y');
$db->query("UPDATE forumtopics SET posts = :p, lastpost = :t WHERE id = :id", array("p"=>$count,"t"=>$time,"id"=>$topic['id']),PDO::FETCH_ASSOC,"n");

/////  CALCULATE AND UPDATE USER POSTS COUNT
$count = $db->query("SELECT id FROM forumposts WHERE addedby = :u",array("u"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');
$db->query("UPDATE members SET forumposts = :p WHERE id = :id", array("p"=>$count,"id"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");


///// CALCULATE AND UPDATE CATEGORY POSTS AND TOPICS TOTALS
$query= $db->query("SELECT posts FROM forumtopics WHERE category = :c",array("c"=>$cat['id']),PDO::FETCH_ASSOC,"n");
$x=0;
$p=0;
foreach($query as $data)
{
$x++;
$p = $data['posts']+$p;
}
$db->query("UPDATE forumcategories SET topics = :t, posts = :p, laststamp = :a WHERE id = :id", array("t"=>$x,"p"=>$p,"a"=>$time,"id"=>$cat['id']),PDO::FETCH_ASSOC,"n");


/////  ADD NEWS FEED
$db->query("INSERT INTO news(owner,itemid,type,stamp) VALUES(:u,:item,:type,:st)",
array("u"=>$_SESSION['userid'],"item"=>$id,"type"=>'forum',"st"=>$time),PDO::FETCH_ASSOC,"n");


/////  AUTO SUBSCRIBE USER TO TOPIC
$db->query("DELETE FROM forumbookmarks WHERE topic = {$id} and owner = :u", array("u"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");
$insert = $db->query("INSERT INTO forumbookmarks(owner,topic) VALUES(:u,:t)",
array("u"=>$_SESSION['userid'],"t"=>$id),PDO::FETCH_ASSOC,"n");


/////  USER NOTIFICATIONS AND EMAILS
$short->forumemail($id,$_SESSION['userid']);

$_SESSION['gmessage'] = 'Reply Posted Successfully';
header("Location:".$array['rooturl']."/?f=".$topic['id']);
exit;
}
}
