<?php

set_time_limit(4000);
//// DELETE FAILED EMAILS
$s1 = "user doesn't have a yahoo";
$s2 = "550 Requested action not taken: mailbox unavailable";
$s3 = "does not exist";
$s4 = "account has been disabled or discontinued";
$s5 = "user doesn't have a";
$s6 = "account or domain may not exist";
$s7 = "email account that you tried to reach is disabled";
$s8 = "user doesn't have a rocketmail.com account";
$s9 = "5.1.1";
$s10 = "misspelled or may not exist";
$s11 = "550 ";
$s12 = "550 Mailbox unavailable";
$s13 = "Mailbox is inactive";
$s14 = "550 #5.1.0 Address rejected";
$s15 = "Unknown address";
$s16 = "Unrouteable address";
$s17 = "invalid mailbox";
$s18 = "User unknown";
$s19 = "The email account that you tried to reach does not exist";
$s20 = "you don't have permission to send to it";
$s21 = "Mail rejected";
$s22 = "mail from specific email addresses";
$s23 = "Account expired";
$s24 = "Account Inactive";
$s25 = "retry timeout exceeded";
$s26 = "Domain pending confirmation";
$s27 = "unsolicited content";
$s28 = "permanently deferred";
//// DELETE WARNING EMAILS
$d1 = "Warning: message";
$d2 = "diskspace";
$d3 = "cloudmark";
$d4 = "retry time not reached for any host after a long failure period";
$d5 = "temporarily deferred due to user complaints";
$d6 = "ver quota";
$d7 = "is full";
$d8 = "blocked due to spam content";




///////////////////////////////////////////////////////  DO NOTIFICATIONS ACCOUNT INBOX

// Connect to gmail
$imapPath = '{mail.theundergroundsexclub.com:143/novalidate-cert}INBOX';
$username = 'notifications@theundergroundsexclub.com';
$password = $emailpassword;
$inbox = imap_open($imapPath,$username,$password) or die('Cannot connect to Email: ' . imap_last_error());
$emails = imap_search($inbox,'ALL');
$output = '';
foreach($emails as $mail) {
////  CHECK FOR SUBJECT TO BE THE CORRECT SUBJECT
$headerInfo = imap_headerinfo($inbox,$mail);
$subject .= $headerInfo->subject;
$body = imap_body($inbox, $mail, FT_PEEK);
$body = $short->clean($body);
// IF CORRECT TERM
if(strpos($body, $s1) !== false || strpos($body, $s2) !== false || strpos($body, $s3) !== false || strpos($body, $s4) !== false || strpos($body, $s5) !== false || strpos($body, $s6) !== false || strpos($body, $s7) !== false || strpos($body, $s8) !== false || strpos($body, $s9) !== false || strpos($body, $s10) !== false || strpos($body, $s11) !== false || strpos($body, $s12) !== false || strpos($body, $s13) !== false || strpos($body, $s14) !== false || strpos($body, $s15) !== false || strpos($body, $s16) !== false || strpos($body, $s17) !== false || strpos($body, $s18) !== false || strpos($body, $s19) !== false || strpos($body, $s20) !== false || strpos($body, $s21) !== false || strpos($body, $s22) !== false || strpos($body, $s23) !== false || strpos($body, $s24) !== false || strpos($body, $s25) !== false || strpos($body, $s26) !== false || strpos($body, $s27) !== false || strpos($body, $s28) !== false)
{
//// GET EMAIL
$em = '';
$reg_exUrl = "/[A-Za-z0-9_-]+@[A-Za-z0-9_-]+\.([A-Za-z0-9_-][A-Za-z0-9_]+)/";
/// IF THE EMAIL ADDRESS EXISTS
if(preg_match($reg_exUrl, $short->clean($body), $em)) {
$em = $em[0];
///// PROCESS DELETION OF MEMBER
$user = $db->row("SELECT id,username FROM members WHERE email = :u",array("u"=>$em));
/// IF USER EXISTS
if($user['id'] > 0)
{
$db->query("UPDATE members SET
notify_follow = 'n',
notify_commentsme = 'n',
notify_commentsgallery = 'n',
notify_commentsphoto = 'n',
notify_commentsstory = 'n',
notify_commentsfeed = 'n',
notify_votesmember = 'n',
notify_votesgallery = 'n',
notify_votesphoto = 'n',
notify_votesstory = 'n',
notify_votesfeed = 'n',
notify_joinmygroup = 'n',
notify_groupcomments = 'n',
email_pm = 'n',
email_forum = 'n',
email_newsletter = 'n'
WHERE id = :id", array("id"=>$user['id']),PDO::FETCH_ASSOC,"n");
}
imap_delete($inbox,$mail);
}
}

//// DELETE WARNING EMAILS
if(strpos($subject, $d1) !== false || strpos($body, $d2) !== false || strpos($body, $d3) !== false || strpos($body, $d4) !== false || strpos($body, $d5) !== false || strpos($body, $d6) !== false || strpos($body, $d7) !== false || strpos($body, $d8) !== false)
{
imap_delete($inbox,$mail);
}




}
// colse the connection
imap_expunge($inbox);
imap_close($inbox);
