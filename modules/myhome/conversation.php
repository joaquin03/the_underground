<?php
if(!isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']."/?mod=login");
exit;
}


$array['pagedescription'] = '';

$page->page .= $page->get_temp('templates/myhome/conversation.htm');

/// CONVERT USERCODE to USER ID
$usercode = $_GET['id'];
$userfrom = $db->row("SELECT * FROM members WHERE usercode = :usercode",array("usercode"=>$usercode));
$fusercode = $userfrom['usercode'];


//$userfrom = $db->row("SELECT * FROM members WHERE id = :id",array("id"=>$_GET['id']));
if($userfrom['id'] == 0)
{
header("Location:".$array['rooturl']."/?mod=myhome&file=mail");
}
$fid = $userfrom['id'];
$array['pagetitle'] = 'Conversation: '.$userfrom['username'].'';
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=myhome&file=mail','Messages',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Conversation',3);
$array['newformstyle'] = 'display:none;';
$array['buttonstyle'] = '';
$array['textbody'] = '';
$array['subject'] = '';
$array['m1'] = '';
$array['m2'] = '';
$array['s1'] = '';
$array['s2'] = '';
$array['i1'] = '';
$array['i2'] = '';

/// THEIR DATA
$array['fromname'] = $short->clean($userfrom['username']);
/// MARK AS READ
$db->query("UPDATE pm SET `read` = :v WHERE `to` = :id AND `from` = :fid", array("v"=>'y',"id"=>$_SESSION['userid'],"fid"=>$fid),PDO::FETCH_ASSOC,"n");


$query= $db->query("SELECT * FROM pm WHERE ((`to` = {$_SESSION['userid']} AND `from` = {$fid} AND delto = 'n') OR (`from` = {$_SESSION['userid']} AND `to` = {$fid} AND delfrom = :v)) ORDER BY stamp DESC",array("v"=>'n'),PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
$x++;
$subject = ($data['subject'] != '') ? $short->clean($data['subject']) : 'No Subject';
///  SPACER FOR FIRST MESSAGE
if($x!=1)
{
$spacer = '<div class="divline"></div>';
}
/// CLEAR SPACER AND CREAT NEW SUBJECT LINE FOR FORM - FIRST ENTRY ONLY
else// IS FIRST ONE
{
$spacer = '';
if($data['to'] == $_SESSION['userid'])
{
$array['subject'] = (substr($data['subject'], 0, 3) == 'RE:') ? $short->clean($data['subject']) : 'RE: '.$subject;
}
else
{
$array['subject'] = '';
}
}
/// IF IMAGE
$image = ($data['image'] != '') ? '<div class="space20"></div><img style="max-width:100%;" src="'.$rooturl.'/images/messages/'.$data['image'].'.jpg"/>': '';
///  IF THERE IS A PERSONAL AD REPLY
$personal = ($data['personal'] == '' || $data['personal'] == 0) ? '': 'This is a Reply to Your Ad: <a href="../?a='.$data['personal'].'">View Ad</a>
<div class="space5"></div>';

$body = ($data['from'] == '100') ? nl2br($data['message']): nl2br($short->clean($data['message']));

$array['messages'] .= $spacer.'<div class="fleft">'.$short->user($data['from'], 'image','n').'</div>
<div class="disp70">
'.$personal.'<span class="lightgrey">Subject:</span> '.$subject.'
<div class="space5"></div>
<span class="lightgrey">From:</span> '.$short->user($data['from'], 'text','n').'
<div class="space10"></div>
'.$body.$image.'
<div class="space10"></div>
<span class="lightgrey">Sent:</span> '.$short->timeago($data['stamp']).' &middot; <a href="'.$array['rooturl'].'/phpfiles/actions.php?delmessage='.$data['id'].'">Delete</a>
</div>
<div class="clear"></div>';

}


if($array['messages'] == '')
{
$array['messages'] = 'No messages in this conversation.';
}













//// POST NEW MESSAGE
if(isset($_POST['button']))
{
$array['newformstyle'] = '';
$array['buttonstyle'] = 'display:none;';
$array['textbody'] = $_POST['message'];
$array['subject'] = $_POST['subject'];
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}
$ok = 'y';
$error = 'n';

////// CHECK IF BLOCKED
$check = $db->query("SELECT id FROM blocks WHERE owner = :o AND who = :w limit 1",array("o"=>$fid,"w"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');
if($check == 0)
{
$from = $_SESSION['userid'];
$to = $fid;
$convo = ($to > $from) ? $from.'-'.$to : $to.'-'.$from;





////  CHECK BANNED WORDS AND BLACKLIST
$query= $db->query("SELECT * FROM s_blacklist_words",null,PDO::FETCH_ASSOC,"n");
$ban = 'n';
foreach($query as $data)
{
if(strpos(' '.$post['message'], $data['word'])) {$ban = 'y';}
}
//////  IF FAILED THE BAN, BLACKLIST NOW
if($ban == 'y')
{
$ok = 'n';
/// EMAIL ADMIN THE BANNED POST
@mail($adminemailaddress, 'Banned Message Blacklisted', $post['message'], "From: ".$adminemailaddress);
$short->deletemember($_SESSION['userid'],'y');
/// LOGOUT
session_destroy();
session_start();
ob_start();
setcookie('active', '', strtotime("-3 months"), '/');
/// SEND SPAM
$banredirect =  "https://www.theundergroundsexclub.com/link.php?b=asstok&c=black";
header("Location: ".$banredirect);
exit;
}




/////  CHECK TOOK LONG TIME TO POST
$time = time();
$timewaited = $time - $_SESSION['reft'];

///// REDIRECT SPAM BOTS WITH FAST TIMES
$okwaittime = '5';
////
if($timewaited < $okwaittime || $_SESSION['reft'] == 0)
{
  //$ok = 'n';
  /// EMAIL ADMIN THE BANNED POST
  @mail($adminemailaddress, 'Banned Message Time to post: '.$timewaited, $post['message'].' Time to post: '.$timewaited.' User: https://www.theundergroundsexclub.com/?u='.$_SESSION['userid'], "From: ".$adminemailaddress);
  //$short->deletemember($_SESSION['userid'],'y');
  /// LOGOUT
  //session_destroy();
  //session_start();
  //ob_start();
  //setcookie('active', '', strtotime("-3 months"), '/');
  /// SEND SPAM
  $banredirect =  "https://www.theundergroundsexclub.com/link.php?b=asstok&c=black";
  header("Location: ".$banredirect);
  exit;
}



////   CHECK MESSAGE HAS A BODY
if($post['message'] == '')
{
$error = 'y';$array['m1'] = 'error';	$array['m2'] = 'is Required';
}
////   CHECK MESSAGE HAS A SUBJECT
if($post['subject'] == '')
{
$error = 'y';$array['s1'] = 'error';	$array['s2'] = 'is Required';
}
////    PROCESS IMAGE
if($ok == 'y' && $error == 'n' && $_FILES['file']['name'] != '')
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
if($ok == 'y' && $error == 'n')
{

/// INSERT MESSAGE
$db->query("INSERT INTO pm(`from`,`to`,stamp,message,conversation,subject,timetopost) VALUES(:f,:t,:s,:m,:c,:sub,:ttp)",
array("f"=>$_SESSION['userid'],"t"=>$fid,"s"=>$time,"m"=>$post['message'],"c"=>$convo,"sub"=>$post['subject'],"ttp"=>$timewaited),PDO::FETCH_ASSOC,"n");
$mailid = $db->lastInsertId();

////   INSERT ENTRY TO EMAIL CRON
$short->privatemessageemail($fid);



///  DEAL WITH IMAGE IF EXISTS
if($_FILES['file']['name'] != '')
{
$icode = $short->createcode(10);
include_once(''.$serverpath.'/addons/image.php');
$image->save($_FILES['file']['tmp_name'],'jpg','800','0','images/messages/'.$icode.'.jpg','60','');
$db->query("UPDATE pm SET image = :v WHERE id = :id", array("v"=>$icode,"id"=>$mailid),PDO::FETCH_ASSOC,"n");
}
}
}//  END IS NOT BLOCKED


if($error == 'n')
{
///  REDIRECT
header("Location:".$array['rooturl']."/?mod=myhome&file=conversation&id=".$fusercode);
exit;
}
}
else ///  NOT POSTING A MESSAGE
{
/// SET SESSION TO CALC TIME
$_SESSION['reft'] = time();
}
