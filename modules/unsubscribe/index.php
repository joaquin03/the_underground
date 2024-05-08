<?php
$page->page .= $page->get_temp('templates/unsubscribe/index.htm');


if(isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']."/?mod=myhome&file=settings");
exit;
}


$array['extrameta'] .= '
<meta name="robots" content="noindex,follow">';

$array['pagetitle'] = 'Unsubscribe';

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Unsubscribe or Change',2);


////////  PRIVATE MESSAGE
if($_GET['type'] == 'system')
{
$array['h1'] = 'Unsubscribe';
$array['content'] = '<h2>No Subscription Detected</h2>The email you received is a system message. Not part of a subscription. There is no need to unsubscribe as it is just a one off email.<div class="space30"></div><h2>Examples</h2><strong>Forgot Password</strong>: It is only send to you if a user goes to the forgot password page and enters in a valid email address.<strong><div class="space10"></div>Email Validation</strong>: When a new user registers, our system sends a single validation email. If the link in this email is not clicked, the email address is removed from our system.';
}




else
{
$email = urldecode($_GET['email']);
$uc = $_GET['uc'];
//////// CONFIRM USER
$user = $db->row("SELECT * FROM members WHERE email = :em AND usercode = :uc AND validated = 'y'",array("em"=>$email,"uc"=>$uc));
if($user['id'] == 0)
{
$array['content'] = '<h2>No Such User</h2>This unsubscribe URL does not represent a user.';
}

//// THERE IS A USER
else
{



////////  PRIVATE MESSAGE
if($_GET['type'] == 'privatemessage')
{
	$array['h1'] = 'Unsubscribe';
	$array['content'] = '<h2>'.$user['username'].' ??</h2>You are receiving this email because you are a member of The USC and have received a real message from a real member.<br>
	<br>
	In case you didn\'t know. We are one of the few dating sites that are 100% legit. No fake profiles, no fake messages - all real.<div class="space20"></div>
	<a href="'.$array['rooturl'].'/?mod=login"><span class="button">Login & Stay Subscribed</span></a>
	<div class="space30"></div>
	<h2>Unsubscribe &nbsp; :(</h2>
	If you no longer wish to be notified via email when you receive private messages, please use the button below to unsubscribe.
	<div class="space20"></div>
	<a href="'.$array['rooturl'].'/?mod=unsubscribe&email='.$_GET['email'].'&uc='.$_GET['uc'].'&type='.$_GET['type'].'&confirm=y"><span class="button">Unsubscribe</span></a>
	';
	if($_GET['confirm'] == 'y')
	{
		$array['content'] = '<h2>Unsubscribe Successful</h2>You have successfully unsubscribed from message notification emails.
		<div class="space10"></div>
		<a href="'.$array['rooturl'].'"><span class="button">Home Page</span></a>
		';
		//// MARK USER AS UNSUBSCRIBED
		$db->query("UPDATE members SET email_pm = :v WHERE id = :id", array("v"=>'n',"id"=>$user['id']),PDO::FETCH_ASSOC,"n");
	}

}


















else///  NO TYPE SPECIFIED
{
header("Location:".$array['rooturl']."/");
exit;
}


}
}
