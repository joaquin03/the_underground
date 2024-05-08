<?php
if(!isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']."/?mod=register");
exit;
}
include(''.$serverpath.'/addons/form_validation.php');

$array['pagetitle'] = 'Add a Photo Gallery';
$page->page .= $page->get_temp('templates/galleries/new.htm');
$array['pagedescription'] = '';

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=galleries','Galleries',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'New Gallery',3);


/////////////////////
$array['t1'] = '';
$array['t2'] = '';
$array['d1'] = '';
$array['d2'] = '';
$array['tag1'] = '';
$array['tag2'] = '';
$array['tags'] = '';
$array['title'] = '';
$array['description'] = '';
$array['groupid'] = '0';

if(isset($_GET['group']))
{
$array['groupid'] = $_GET['group'];
}












///////////////////////// POST
if(isset($_POST['button']))
{
$array['title'] = $_POST['title'];
$array['description'] = $_POST['description'];
$array['groupid'] = $_POST['group'];
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}
$ok = 'y';
///
if($post['title'] == '')
{
$ok = 'n';$array['t1'] = 'error';	$array['t2'] = 'is required';
}
if($post['description'] == '')
{
$ok = 'n';$array['d1'] = 'error';	$array['d2'] = 'is required';
}


////  CHECK BANNED WORDS AND BLACKLIST
$query= $db->query("SELECT * FROM s_blacklist_words",null,PDO::FETCH_ASSOC,"n");
$ban = 'n';
foreach($query as $data)
{
if(strpos(' '.$array['title'], $data['word'])) {$ban = 'y';}
if(strpos(' '.$array['description'], $data['word'])) {$ban = 'y';}
}
///////  IF FAILED THE BAN, BLACKLIST NOW
if($ban == 'y')
{
$ok = 'n';
/// EMAIL ADMIN THE BANNED POST
@mail($adminemailaddress, 'Banned Gallery Blacklisted', $array['title'].' ----- '. $array['description'], "From: ".$adminemailaddress);
$short->deletemember($_SESSION['userid'],'y');
/// LOGOUT
session_destroy();
session_start();
ob_start();
setcookie('active', '', strtotime("-3 months"), '/');
header("Location: ".$rooturl);
exit;
}




/////////////  CHECK DUPLICATE TAGS
$string = str_replace('&#8218;',' ',$post['tags']);
$string = preg_replace("/([,.?!])/"," \\1",$string);
$parts = explode(" ",$string);
$unique = array_unique($parts);
$unique = implode(" ",$unique);
$unique = preg_replace("/\s([,.?!])/","\\1",$unique);
$post['tags'] = $unique;
$tot = str_word_count($post['tags']);
if($tot > 20)
{
$ok = 'n';$array['tag1'] = 'error';	$array['tag2'] = '20 Tag Words Max';
}
if($post['tags'] == '')
{
$ok = 'n';$array['tag1'] = 'error';	$array['tag2'] = 'are required';
}
//
if($ok == 'y')
{
$db->query("INSERT INTO galleries(title,description,`group`,tags,stamp,owner) VALUES(:t,:d,:g,:tg,:s,:o)",
array("t"=>$post['title'],"d"=>$post['description'],"g"=>$post['group'],"tg"=>$post['tags'],"s"=>$time,"o"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");
$id = $db->lastInsertId();
header("Location:".$array['rooturl']."/?mod=galleries&file=add&id=".$id."");
exit;
}
$array['tags'] = $post['tags'];
}









//////////////////////////// GROUP NAMES
$query = $db->query("SELECT * FROM groupfollows WHERE owner = :id",array("id"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");
$mid = "0,";
$mname = "No Group --,";
$x=0;
foreach($query as $data)
{
$x++;
$group = $db->row("SELECT id,title FROM groups WHERE id = :id",array("id"=>$data['groupid']));
$mid .= trim(stripslashes($group['id'])).",";
$mname .= trim(stripslashes($group['title'])).",";
$x++;
}
$array['groupslist'] = ($x==0) ? '': '<span class="formt">Post to a Group<span class="lightgrey"> &middot; Not Required</span></span>
<select name="group" size="1" class="formfield" >'.$form->select_post_fix($mid, $mname, $array['groupid']).'</select>';
