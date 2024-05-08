<?php
if(!isset($_GET['mod']))
{
die();
}
if(isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']);
exit;
}


$array['pagetitle'] = 'Login';
$array['pagedescription'] = 'Login to the Underground Sex Club. Gain access to the members area of the Underground Sex Club.';
$page->page .= $page->get_temp('templates/login/index.htm');




//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Member Login',2);






//////////////////////////////////////////////////////////////// POST LOGIN
if(isset($_POST['logbutton']))
{
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}
///// ENCRYPE PASSWORD
$password = $short->password($post['logpassword']);
$memberdata = $db->row("SELECT id,usercode,regip FROM members WHERE email = :e AND password = :password AND validated = 'y'",array("e"=>$post['email'],"password"=>$password));
if($memberdata['id'] == 0)
{
$array['errormessage'] = $short->message('Login Details Incorrect', 'r');
}
else
{
// CHECK USERCODE EXISTS AND CREATE IF EMPTY
if($memberdata['usercode'] == '')
{
$usercode = $short->createusercode();
$db->query("UPDATE members SET usercode = :uc WHERE id = :id", array("uc"=>$usercode,"id"=>$memberdata['id']),PDO::FETCH_ASSOC,"n");
$memberdata['usercode'] = $usercode;
}

///////  SET COOKIE IF REMEMBER
if($_POST['remember'] == 'y' && $memberdata['usercode'] != '')
{ 
//setcookie('active', $memberdata['usercode'], strtotime("+3 months"), '/');
}
$db->query("UPDATE members SET currentlogin = :time, lastonline = :oldtime WHERE id = :id", array("time"=>$time,"oldtime"=>$memberdata['currentlogin'],"id"=>$memberdata['id']),PDO::FETCH_ASSOC,"n");
$_SESSION['userid'] = $memberdata['id'];
$_SESSION['active'] = $memberdata['usercode'];
///////  LOGIN ENTRY
$insert = $db->query("INSERT INTO members_logins(userid,stamp,ipaddress,useragent) VALUES(:u,:s,:ip,:ua)",
array("u"=>$memberdata['id'],"s"=>$time,"ip"=>$userip,"ua"=>$useragent),PDO::FETCH_ASSOC,"n");
///  UPDATE REG IP IF NOT SET
if($memberdata['regip'] == '')
{
$db->query("UPDATE members SET regip = :r WHERE id = :id", array("r"=>$userip,"id"=>$memberdata['id']),PDO::FETCH_ASSOC,"n");
}
//////////// REDIRECT
$_SESSION['gmessage'] = 'You are now Logged in';
header("Location:".$array['rooturl'].$_SESSION['activeredirect']);
exit;
}
}
