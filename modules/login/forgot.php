<?php
/// DECLARE MAIL USE CLASS
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if(isset($_SESSION['userid']))
{
header("Location: ".$rooturl);
exit;
}


$array['pagetitle'] = 'Forgot Details';
$array['pagedescription'] = 'Retrieve your login details to access the USC.';
$page->page .= $page->get_temp('templates/login/forgot.htm');
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=login','Member Login',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Retrieve Details',3);
$array['extrameta'] .= '
<meta name="robots" content="noindex,follow">';






if(isset($_POST['button2']))
{
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}

$memberdata = $db->row("SELECT id,email,username,validated,usercode FROM members WHERE email = :em",array("em"=>$post['email']));
if($memberdata['id'] > 0)
{
///// CREATE NEW PASSWORD
$newpass = substr(md5(rand().rand()), 0, 5);
$password = $short->password($newpass);

///////  PUT PASSWORD INTO DATABASE
$db->query("UPDATE members SET password = :pw WHERE id = :id", array("pw"=>$password,"id"=>$memberdata['id']),PDO::FETCH_ASSOC,"n");

////////   SEND MESSAGE WITHOUT VALidATION
if($memberdata['validated'] == 'n')
{
$message = 'Hello '.$memberdata['username'].',<br/><br/>

You have requested your login details with a new password which are below, however, you will not be able to use them until your email address is validated.<br/><br/>

Validation Code: '.$memberdata['usercode'].'<br/>
Validation Page: <a href="https://www.theundergroundsexclub.com/?validate='.$memberdata['usercode'].'">https://www.theundergroundsexclub.com/?validate='.$memberdata['usercode'].'</a><br/><br/>

Once Validated, you can use these login details:<br/><br/>

Username: '.$memberdata['username'].'<br/>
Password: '.$newpass.'<br/><br/>

Thanks';
}
else
{
$message = 'Hello '.$memberdata['username'].',<br/><br/>

Your Login Details, including your new password, are as follows:<br/><br/>

Username: '.$memberdata['username'].'<br/>
Password: '.$newpass.'<br/><br/>

Login Here: <a href="https://www.theundergroundsexclub.com/?mod=login">https://www.theundergroundsexclub.com/?mod=login</a><br/><br/>

Please note: You can edit your password in your account section once logged in.<br/><br/>

Thanks';
}
///   SEND MESSAGE
$heading = 'Login Details';




//// HTML
$emailbody = $page->get_temp(''.$serverpath.'/templates/main/email.htm');
$emailbody = str_replace("{body}", $message, $emailbody);
$emailbody = str_replace("{heading}", $heading, $emailbody);
// TEXT ONLY EMAIL
$textbody = str_replace("<br/>","",$message);
//// SEND
require_once ''.$serverpath.'/phpmailer6/src/Exception.php';
require_once ''.$serverpath.'/phpmailer6/src/PHPMailer.php';
require_once ''.$serverpath.'/phpmailer6/src/SMTP.php';
try {
$mail = new PHPMailer(true);
//$mail->SMTPDebug = 3;
$mail->isSMTP();
$mail->Host = 'mail.'.$domainonly.'';
$mail->SMTPAuth = true;
$mail->Username = 'notifications@'.$domainonly.'';
$mail->Password = $emailpassword;
$mail->SMTPSecure = 'ssl';
$mail->SMTPOptions = array('ssl' => array('verify_peer' => false,'verify_peer_name' => false,'allow_self_signed' => true));
$mail->Port = 465;
$mail->setFrom('notifications@'.$domainonly.'', 'The USC');
$mail->addReplyTo('notifications@'.$domainonly.'', 'The USC');
$mail->addAddress($memberdata['email']);
$mail->isHTML(true);
$mail->Subject = $heading;
$mail->Body    = $emailbody;
$mail->AltBody = $textbody;
$mail->send();
} catch (Exception $e) {
@mail($adminemailaddress, 'Send Mail Error: Forgot Password', 'Error: '.$mail->ErrorInfo, "From: ".$adminemailaddress);
}



}/// END MEMBER id EXISTS

///////  DISPLAY SUCCESS
$array['errormessage'] = $short->message('If Email Exists in Our System, Login Details Sent', 'g');
}
