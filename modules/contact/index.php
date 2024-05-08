<?php
/// DECLARE MAIL USE CLASS
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if($userip == '5.164.198.60' || $userip == '185.252.187.249')
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: https://www.wikihow.com/Eat-Spam");
exit;
}

$array['pagetitle'] = 'Contact Us';
$array['pagedescription'] = 'Contact us if you need any assistance.';

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Contact Us',2);


$array['extrameta'] .= '<script src=\'https://www.google.com/recaptcha/api.js\'></script>';


$array['extrameta'] .= '
<meta name="robots" content="noindex,follow">';



$page->page .= $page->get_temp('templates/contact/index.htm');

$array['n1'] = '';
$array['n2'] = '';
$array['e1'] = '';
$array['e2'] = '';
$array['s1'] = '';
$array['s2'] = '';
$array['b1'] = '';
$array['b2'] = '';
$array['rt2'] = '';


$array['name'] = '';
$array['email'] = '';
$array['subject'] = '';
$array['body'] = '';

if(isset($_POST['cbutton']))
{
$ok = 'y';
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}

$array['name'] = $_POST['name'];
$array['email'] = $_POST['email'];
$array['subject'] = $_POST['subject'];
$array['body'] = $_POST['body'];


/////////  CHECK NAME
if($post['name'] == '')
{
$ok = 'n';$array['n1'] = 'error';	$array['n2'] = 'is Required';
}

/////////  CHECK EMAIL
if($post['email'] == '')
{
$ok = 'n';$array['e1'] = 'error';	$array['e2'] = 'is Required';
}
if($post['email'] == 'sample@email.tst')
{
$ok = 'n';$array['e1'] = 'error';	$array['e2'] = '- Stop Trying to SQL Inject you idiot';
}
/////////  CHECK SUBJECT
if($post['subject'] == '')
{
$ok = 'n';$array['s1'] = 'error';	$array['s2'] = 'is Required';
}

/////////  CHECK NAME
if($post['body'] == '')
{
$ok = 'n';$array['b1'] = 'error';	$array['b2'] = 'is Required';
}


if($ok == 'y')
{
	//// CAPTURE
$secret = '6LccKQ8UAAAAAI9rnWyCn4IxO3Vvy3L9jxLuAOlq';
$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);

if($responseData->success)
{
}
else
{
$ok = 'n';	$array['rt2'] = 'is Required';
}

}





if($ok == 'y')
{
$array['errormessage'] = $short->message('Message Sent Successfully', 'g');
$array['name'] = '';
$array['email'] = '';
$array['subject'] = '';
$array['body'] = '';


///   SEND MESSAGE STO SENDER
$heading = 'Auto Reply';
$message = 'Hello,<br/><br/>

Thankyou for contacting The USC.<br/><br/>

If required, someone will be in contact with you soon.<br/><br/>

Thanks';

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
$mail->addAddress($post['email']);
$mail->isHTML(true);
$mail->Subject = $heading;
$mail->Body    = $emailbody;
$mail->AltBody = $textbody;
$mail->send();
} catch (Exception $e) {
@mail($adminemailaddress, ''.$userip.' - Send Mail Error: Contact Form', 'Error: '.$mail->ErrorInfo, "From: ".$adminemailaddress);
}




/////////////  SEND SYSTEM MESSAGE
$email = $adminemailaddress;
$msg = str_replace("&#8218;", ",", $_POST['body']);
$msg = str_replace("\r\n", "\n", $msg);
mail($email, "Subject: ".$_POST['subject'], "".$msg."

".stripslashes($post['name'])."

IP: ".$userip."", "From:".$post['email']);

}

}
