<?php
if(!isset($_SESSION['userid']))
{
header("Location: ".$rooturl);
exit;
}




$id = $_GET['id'];
$adpost = $db->row("SELECT * FROM classifieds WHERE id = :id AND owner = :o",array("id"=>$id,"o"=>$_SESSION['userid']));
if($adpost['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=personals");
exit;
}
if($adpost['title'] != '')
{
header("Location:".$array['rooturl']."/?a=".$id);
exit;
}
if($adpost['state'] == 0)
{
header("Location:".$array['rooturl']."/?mod=personals&file=step2&id=".$id);
exit;
}


$state = $db->row("SELECT * FROM loc_states WHERE id = :id",array("id"=>$adpost['state']));
$array['statetitle'] = $state['title'];


$array['pagetitle'] = 'Post a New Ad';
$array['pagedescription'] = '';
$page->page .= $page->get_temp('templates/personals/step3.htm');

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=personals','Personals',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'New &middot; Step 3',3);
/////////////////////
$array['a1'] = '';
$array['a2'] = '';
$array['i1'] = '';
$array['i2'] = '';
$array['t1'] = '';
$array['t2'] = '';
$array['c1'] = '';
$array['c2'] = '';
$array['d1'] = '';
$array['d2'] = '';
$array['title'] = '';
$array['description'] = '';








////////////////  POST FORM
if(isset($_POST['button']))
{
$array['title'] = $_POST['title'];
$array['description'] = $_POST['description'];
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
if(strpos(' '.$array['title'], $data['word'])) {$ban = 'y';}
if(strpos(' '.$array['description'], $data['word'])) {$ban = 'y';}
}
//////  IF FAILED THE BAN, BLACKLIST NOW
if($ban == 'y')
{
$ok = 'n';
/// EMAIL ADMIN THE BANNED POST
@mail($adminemailaddress, 'Banned Personal Blacklisted', $array['title'].' ----- '. $array['description'], "From: ".$adminemailaddress);
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
////   CHECK POST HAS AN AREA
if($post['area'] == '')
{
$ok = 'n';$array['a1'] = 'error';	$array['a2'] = 'is Required';
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
/// UPDATE AD
$db->query("UPDATE classifieds SET area = :a, category = :c, title = :t, description = :d, stamp = :s WHERE id = :id",
array("a"=>$post['area'],"c"=>$post['cat'],"t"=>$post['title'],"d"=>$post['description'],"s"=>$time,"id"=>$adpost['id']),PDO::FETCH_ASSOC,"n");

///  DEAL WITH IMAGE IF EXISTS
if($_FILES['file']['name'] != '')
{
$icode = $short->createcode(10);
include_once(''.$serverpath.'/addons/image.php');
$image->save($_FILES['file']['tmp_name'],'jpg','800','0','images/personals/'.$icode.'-original.jpg','100','');
$image->save($_FILES['file']['tmp_name'],'jpg','800','0','images/personals/'.$icode.'.jpg','60','');
$image->save($_FILES['file']['tmp_name'],'jpg','150','150','images/personals/'.$icode.'-thumb.jpg','20','');
$db->query("UPDATE classifieds SET image = :v WHERE id = :id", array("v"=>$icode,"id"=>$adpost['id']),PDO::FETCH_ASSOC,"n");
}

/////  ADD NEWS FEED
$db->query("INSERT INTO news(owner,itemid,type,stamp) VALUES(:u,:item,:type,:st)",
array("u"=>$_SESSION['userid'],"item"=>$adpost['id'],"type"=>'personalad',"st"=>$time),PDO::FETCH_ASSOC,"n");


$_SESSION['gmessage'] = 'Ad Posted Successfully';
header("Location:".$array['rooturl']."/?a=".$adpost['id']);
exit;
}
}










$mysex = ($member['sex'] == 'Male') ? "sex != 'f'" : "sex != 'm'";
////////////////// GET CATS
$array['cats'] .= '<option value="">-- Select --</option>';
$query = $db->query("SELECT * FROM classifieds_categories WHERE $mysex ORDER BY id ASC",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$array['cats'] .= ($data['id'] == $post['cat']) ? '<option value="'.$data['id'].'" selected="selected">'.$data['title'].'</option>': '<option value="'.$data['id'].'">'.$data['title'].'</option>';
}



////////////////// GET AREAS
$array['droplist'] .= '<option value="">-- Select --</option>';
$query = $db->query("SELECT * FROM loc_areas WHERE state = :s",array("s"=>$adpost['state']),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$array['droplist'] .= ($data['id'] == $post['area']) ? '<option value="'.$data['id'].'" selected="selected">'.$data['title'].'</option>': '<option value="'.$data['id'].'">'.$data['title'].'</option>';
}
