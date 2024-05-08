<?php
if(!isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']."/?mod=register");
exit;
}
include(''.$serverpath.'/addons/form_validation.php');

$page->page .= $page->get_temp('templates/forum/add.htm');

//// SET SQL FOR LOCKED
if($_SESSION['userid'] == 100)
{
$locked = "id > 0";
}
else
{
$locked = "locked = 'n'";
}



//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=forum','Sex Forum',2);


/// GET CATEGORY
$catdata = $db->row("SELECT * FROM forumcategories WHERE id = :id AND $locked",array("id"=>$_GET['cat']));
if($catdata['id'] > 0)
{


$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=forum&file=category&id='.$catdata['id'],$short->clean($catdata['title']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Post Topic',3);


}
else
{
if(isset($_GET['cat']))
{
header("Location:".$array['rooturl']."/?mod=forum&file=add");
exit;
}

$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Post Topic',2);
$catlink = '$cat='.$_GET['cat'];
}


//// GROUP id FOR TOPIC
$array['groupid'] = '0';
if(isset($_GET['group']))
{
$array['groupid'] = $_GET['group'];
}



$array['pagetitle'] = 'Add Forum Topic';

///
$array['t1'] = '';
$array['t2'] = '';
$array['c1'] = '';
$array['c2'] = '';
$array['r1'] = '';
$array['r2'] = '';
$array['i1'] = '';
$array['i2'] = '';
$array['body'] = '';
$array['title'] = '';





////////////////  POST FORM
if(isset($_POST['button']))
{
$array['title'] = $_POST['title'];
$array['body'] = $_POST['body'];
$catdata['id'] = $_POST['cat'];
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
////   CHECK MESSAGE HAS A TITLE
if($post['title'] == '')
{
$ok = 'n';$array['t1'] = 'error';	$array['t2'] = 'is Required';
}
////   CHECK MESSAGE HAS A CATEGORY
if($post['cat'] == '')
{
$ok = 'n';$array['c1'] = 'error';	$array['c2'] = 'is Required';
}

/////    CHECK BLACKLIST WORDS AND BAN MEMBER
if($ok == 'y')
{
$bquery= $db->query("SELECT word,critical FROM s_blacklist_words",null,PDO::FETCH_ASSOC,"n");
$x=0;
foreach($bquery as $bdata)
{
if(strpos(' '.strtolower($post['body']), strtolower($bdata['word'])) || strpos(' '.strtolower($post['title']), strtolower($bdata['word']))) {
///  IF WORD IS CRITICAL
if($bdata['critical'] == 'y')
{
$ok = 'n';
/// EMAIL ADMIN THE BANNED POST
@mail($adminemailaddress, 'Banned Post Blacklisted', $post['body'], "From: ".$adminemailaddress);
/// DELETE MEMBER AND BLACKLIST
$short->deletemember($_SESSION['userid'],'y');
/// LOGOUT
session_destroy();
session_start();
ob_start();
setcookie('active', '', strtotime("-3 months"), '/');
header("Location: ".$rooturl);
exit;
}
/// NOT CRITICAL
else
{
$ok = 'n';
header("Location: ".$rooturl);
exit;
}

}/// END IF WORD EXIXTS
}// END LOOP WORDS
}// OK IS YES BEFORE CHECKING BANNED WORDS

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
/// INSERT TOPIC
$db->query("INSERT INTO forumtopics(title,category,`group`,added,addedby,posts,lastpost) VALUES(:t,:c,:g,:a,:ab,:p,:lp)",
array("t"=>$post['title'],"c"=>$post['cat'],"g"=>$post['group'],"a"=>$time,"ab"=>$_SESSION['userid'],"p"=>'1',"lp"=>$time),PDO::FETCH_ASSOC,"n");
$topic['id'] = $db->lastInsertId();
/// INSERT  POST
$db->query("INSERT INTO forumposts(body,topic,added,addedby,original) VALUES(:b,:t,:a,:o,:or)",
array("b"=>$post['body'],"t"=>$topic['id'],"a"=>$time,"o"=>$_SESSION['userid'],"or"=>'y'),PDO::FETCH_ASSOC,"n");
$newid = $db->lastInsertId();

///  DEAL WITH IMAGE IF EXISTS
if($_FILES['file']['name'] != '')
{
$icode = $short->createcode(10);
include_once(''.$serverpath.'/addons/image.php');
$image->save($_FILES['file']['tmp_name'],'jpg','800','0','images/forum/'.$icode.'-original.jpg','100','');
$image->save($_FILES['file']['tmp_name'],'jpg','800','0','images/forum/'.$icode.'.jpg','60','');
$image->save($_FILES['file']['tmp_name'],'jpg','300','300','images/forum/'.$icode.'-thumb.jpg','20','');
$db->query("UPDATE forumposts SET image = :v WHERE id = :id", array("v"=>$icode,"id"=>$newid),PDO::FETCH_ASSOC,"n");
}


/////  CALCULATE AND UPDATE USER POSTS COUNT
$count = $db->query("SELECT id FROM forumposts WHERE addedby = :u",array("u"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');
$db->query("UPDATE members SET forumposts = :p WHERE id = :id", array("p"=>$count,"id"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");


///// CALCULATE AND UPDATE CATEGORY POSTS AND TOPICS TOTALS
$query= $db->query("SELECT posts FROM forumtopics WHERE category = :c",array("c"=>$catdata['id']),PDO::FETCH_ASSOC,"n");
$x=0;
$p=0;
foreach($query as $data)
{
$x++;
$p = $data['posts']+$p;
}
$db->query("UPDATE forumcategories SET topics = :t, posts = :p, laststamp = :a WHERE id = :id", array("t"=>$x,"p"=>$p,"a"=>$time,"id"=>$catdata['id']),PDO::FETCH_ASSOC,"n");


/////  ADD NEWS FEED
$db->query("INSERT INTO news(owner,itemid,type,stamp) VALUES(:u,:item,:type,:st)",
array("u"=>$_SESSION['userid'],"item"=>$topic['id'],"type"=>'newforum',"st"=>$time),PDO::FETCH_ASSOC,"n");


/////  AUTO SUBSCRIBE USER TO TOPIC
$db->query("DELETE FROM forumbookmarks WHERE topic = :id and owner = :u", array("id"=>$topic['id'],"u"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");
$insert = $db->query("INSERT INTO forumbookmarks(owner,topic) VALUES(:u,:t)",
array("u"=>$_SESSION['userid'],"t"=>$topic['id']),PDO::FETCH_ASSOC,"n");



$_SESSION['gmessage'] = 'Topic Posted Successfully';
header("Location:".$array['rooturl']."/?f=".$topic['id']);
exit;
}
}










//////////////////////////// GROUP NAMES
$query = $db->query("SELECT * FROM groupfollows WHERE owner = :id",array("id"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");
$mid = "0,";
$mname = "No Group --,";
$x=0;
foreach($query as $data)
{
$x++;
$group = $db->row("SELECT id,title FROM groups WHERE id = :id",array("id"=>$data['groupid']),PDO::FETCH_ASSOC,"n");
$mid .= trim(stripslashes($group['id'])).",";
$mname .= trim(stripslashes($group['title'])).",";
$x++;
}
$array['groupslist'] = ($x==0) ? '': '<span class="formt">Post to a Group<span class="lightgrey"> &middot; Not Required</span></span>
<select name="group" size="1" class="formfield" >'.$form->select_post_fix($mid, $mname, $array['groupid']).'</select>';






///////////////   GET FORUMS MAIN CATS
$query = $db->query("SELECT * FROM forumcategories WHERE $locked ORDER BY categoryorder ASC, topics DESC",null,PDO::FETCH_ASSOC,"n");
$cat = '';
foreach($query as $data)
{
////  SEE IF WE NEED A HEADER
if($data['category'] != $cat)
{
$cat = $data['category'];
$array['topiclist'] .= '<option value="">'.$data['category'].'</option>';
}
$selected = ($catdata['id'] == $data['id']) ? 'selected="selected"': '';
$array['topiclist'] .= '<option value="'.$data['id'].'" '.$selected.'>&nbsp;&nbsp; &middot; '.$data['title'].'</option>';
}
