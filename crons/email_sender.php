<?php



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$domainonly = 'theundergroundsexclub.com';
$serverpath = '/var/www/vhosts/theundergroundsexclub.com/httpdocs';

require ''.$serverpath.'/phpmailer6/src/Exception.php';
require ''.$serverpath.'/phpmailer6/src/PHPMailer.php';
require ''.$serverpath.'/phpmailer6/src/SMTP.php';







///////   GET EMAILS
$query = $db->query("SELECT * FROM emailing ORDER BY priority ASC LIMIT 50",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$user = $db->row("SELECT username,id,usercode,email FROM members WHERE id = :id",array("id"=>$data['user']));
$ue = $user['email'];
if($user['id'] > 0)
{
try {
$mail = new PHPMailer(true);
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
$mail->addAddress($user['email']);
$mail->isHTML(true);
$mail->Subject = $data['subject'];
$mail->Body    = $data['body'];
if($data['textonly'] != '')
{
$mail->AltBody = $data['textonly'];
}
$mail->send();
} catch (Exception $e) {
@mail('ourteam@theundergroundsexclub.com', 'Send Mail Error: Email Sender Old Site', 'Error: '.$mail->ErrorInfo, "From: ourteam@theundergroundsexclub.com");
}

}
//// DELETE EMAIL FROM QUEUE
$db->query("DELETE FROM emailing WHERE id = :id", array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}
