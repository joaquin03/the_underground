<?php

if(!isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']."/?mod=register");
exit;
}

///
$array['pagetitle'] = 'Create a Sex Group';
$array['pagedescription'] = '';
$page->page .= $page->get_temp('templates/groups/new.htm');
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=groups','Sex Groups',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'New Group',3);



/////////////////////
$array['t1'] = '';
$array['t2'] = '';
$array['c1'] = '';
$array['c2'] = '';
$array['i1'] = '';
$array['i2'] = '';
$array['d1'] = '';
$array['d2'] = '';

//////////////// DATA
$array['title'] = '';
$array['description'] = '';
$array['slogan'] = '';
$array['website'] = 'http://';









////////////////  POST FORM
if(isset($_POST['button']))
{
$array['title'] = $_POST['title'];
$array['description'] = $_POST['description'];
$array['slogan'] = $_POST['slogan'];
$array['website'] = $_POST['website'];
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}
$ok = 'y';

////  CHECK BANNED WORDS AND BLACKLIST
$query= $db->query("SELECT * FROM s_blacklist_words",null,PDO::FETCH_ASSOC,"n");
$ban = 'n';
foreach($query as $data)
{
if(strpos(' '.$post['title'], $data['word'])) {$ban = 'y';}
if(strpos(' '.$post['description'], $data['word'])) {$ban = 'y';}
if(strpos(' '.$post['slogan'], $data['word'])) {$ban = 'y';}
}
//////  IF FAILED THE BAN, BLACKLIST NOW
if($ban == 'y')
{
$ok = 'n';$array['d1'] = 'error';	$array['d2'] = 'is Blocked';
$ok = 'n';
/// EMAIL ADMIN THE BANNED POST
@mail($adminemailaddress, 'Banned Group Blacklisted', $array['title'].' ----- '. $array['description'], "From: ".$adminemailaddress);
$short->deletemember($_SESSION['userid'],'y');
/// LOGOUT
session_destroy();
session_start();
ob_start();
setcookie('active', '', strtotime("-3 months"), '/');
header("Location: ".$rooturl);
exit;
}





////   CHECK POST HAS A BODY
if($post['description'] == '')
{
$ok = 'n';$array['d1'] = 'error';	$array['d2'] = 'is Required';
}
////   CHECK POST HAS A TITLE
if($post['title'] == '')
{
$ok = 'n';$array['t1'] = 'error';	$array['t2'] = 'is Required';
}
////   CHECK POST HAS A CATEGORY
if($post['cat'] == '')
{
$ok = 'n';$array['c1'] = 'error';	$array['c2'] = 'is Required';
}
////    CHECK IMAGE IF ALL OK AND IMAGE EXISTS
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
$post['website'] = ($post['website'] == 'http://') ? '': $post['website'];
/// INSERT GROUP
$db->query("INSERT INTO groups(owner,title,description,catid,stamp,website,slogan) VALUES(:o,:t,:d,:c,:s,:w,:sl)",
array("o"=>$_SESSION['userid'],"t"=>$post['title'],"d"=>$post['description'],"c"=>$post['cat'],"s"=>$time,"w"=>$post['website'],"sl"=>$post['slogan']),PDO::FETCH_ASSOC,"n");
$gid = $db->lastInsertId();

///  DEAL WITH IMAGE IF EXISTS
if($_FILES['file']['name'] != '')
{
$icode = $short->createcode(10);
include_once(''.$serverpath.'/addons/image.php');
$image->save($_FILES['file']['tmp_name'],'jpg','800','0','images/groups/'.$icode.'-original.jpg','100','');
$image->save($_FILES['file']['tmp_name'],'jpg','500','0','images/groups/'.$icode.'.jpg','80','');
$image->save($_FILES['file']['tmp_name'],'jpg','100','100','images/groups/'.$icode.'-thumb.jpg','70','');
$db->query("UPDATE groups SET image = :v WHERE id = :id", array("v"=>$icode,"id"=>$gid),PDO::FETCH_ASSOC,"n");
}

////  INSERT GROUP FOLLOWER
$db->query("INSERT INTO groupfollows(owner,groupid,mine) VALUES(:o,:g,:m)",
array("o"=>$_SESSION['userid'],"g"=>$gid,"m"=>'y'),PDO::FETCH_ASSOC,"n");

/////  ADD NEWS FEED
$db->query("INSERT INTO news(owner,itemid,type,stamp) VALUES(:u,:item,:type,:st)",
array("u"=>$_SESSION['userid'],"item"=>$gid,"type"=>'group',"st"=>$time),PDO::FETCH_ASSOC,"n");

/// UPDATE GROUP COUNT
$tot = $db->query("SELECT id FROM groupfollows WHERE groupid = :id",array("id"=>$gid),PDO::FETCH_NUM,'y');
$db->query("UPDATE groups SET members = :t WHERE id = :id", array("t"=>$tot,"id"=>$gid),PDO::FETCH_ASSOC,"n");

/// UPDATE GROUP COUNT ON MEMBER
$tot = $db->query("SELECT id FROM groupfollows WHERE owner = :o",array("o"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');
$db->query("UPDATE members SET count_groups = :t WHERE id = :id", array("t"=>$tot,"id"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");

$_SESSION['gmessage'] = 'Ad Posted Successfully';
header("Location:".$array['rooturl']."/?g=".$gid);
exit;
}
}











////////////////// GET CATS
$array['cats'] .= '<option value="">-- Select --</option>';
$query = $db->query("SELECT * FROM groupcategories",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$array['cats'] .= ($data['id'] == $post['cat']) ? '<option value="'.$data['id'].'" selected="selected">'.$data['title'].'</option>': '<option value="'.$data['id'].'">'.$data['title'].'</option>';
}

/*


///////////////////////// POST
if(isset($_POST['button']))
{
foreach($_POST as $key => $value)
{
$value = trim($value);
$value = $short->dbsafequery($short->safetextdisplay($value,'y'));
$post[$key] = $value;
$array[$key] = $value;
}

$ok = 'y';

if($post['cat'] == '')
{
$ok = 'n';$array['c1'] = 'error';	$array['c2'] = 'must be selected';
}
if($post['title'] == '')
{
$ok = 'n';$array['t1'] = 'error';	$array['t2'] = 'can not be blank';
}


if($ok == 'y')
{
$mysql->insert($mysql->perfix."groups", "owner,title,description,catid,stamp,website,slogan", "{$_SESSION['id']},{$post['title']},{$post['description']},{$post['cat']},$time,{$post['website']},{$post['slogan']}");
$sid = $mysql->insert_id;
$mysql->insert($mysql->perfix."groupfollows", "owner,groupid,mine", "{$_SESSION['id']},$sid,y");


////////////////   ADD NEWS ITEM
$mysql->insert($mysql->perfix."news", "owner,itemid,type,stamp,commentstamp","{$_SESSION['id']},$sid,group,$time,$time");
///// RANKING SCORE
$short->rank($_SESSION['id'],10);





$_SESSION['gmessage'] = 'Your Group has been Created Successfully';
header("Location:http://www.theundergroundsexclub.com/?g=".$sid);
exit;
}
}






*/
