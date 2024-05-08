<?php
if(!isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']."/?mod=login");
exit;
}
$array['pagetitle'] = 'Messages';
$page->page .= $page->get_temp('templates/myhome/mail.htm');

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Messages',2);




$array['messages'] = '';


$query= $db->query("SELECT *, MAX(stamp) FROM pm WHERE (`to` = {$_SESSION['userid']} AND delto = 'n') OR (`from` = {$_SESSION['userid']} AND delfrom = 'n') GROUP BY conversation ORDER BY MAX(stamp) DESC",null,PDO::FETCH_ASSOC,"n");
$x=0;
$ad = 0;
foreach($query as $data)
{
$ad++;
$x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$mail = $db->row("SELECT * FROM pm WHERE ((`to` = {$_SESSION['userid']} AND delto = 'n') OR (`from` = {$_SESSION['userid']} AND delfrom = 'n')) AND conversation = :c ORDER BY stamp DESC LIMIT 1",array("c"=>$data['conversation']));
$subject = ($mail['subject'] == '') ? 'No Subject' : $short->clean($mail['subject']);
$read = ($mail['read'] == 'n' && $mail['from'] != $_SESSION['userid']) ? '<span alt="Unread Message" title="Unread Message" class="smallgreybutton" style="cursor:pointer;">N</span>&nbsp; ' : '';
$img = ($mail['image'] == '') ? '' : '<span alt="Image Included" title="Image Included" style="cursor:pointer;" class="smallgreybutton"> &#10063;</span>&nbsp; ';

//// FROM IS ME
if($data['from'] == $_SESSION['userid'])
{
$otheruser = $mail['to'];
}
/// FROM IS OTHER PERSON
else
{
$otheruser = $mail['from'];
}
$otherusercode .= $short->usercodefromid($otheruser);

$array['messages'] .= $spacer.'<div class="fleft">'.$short->user($otheruser, 'image','n').'</div>
<div class="disp70">
<span class="onelinetext">Subject: <a href="..?mod=myhome&file=conversation&id='.$otherusercode.'">'.$subject.'</a></span>
<div class="space1"></div>
User: '.$short->user($otheruser, 'text','n').'
<div class="space5"></div>
<span class="lightgrey">'.$read.$img.$short->timeago($data['stamp']).'</span>
</div>
<div class="clear"></div>';

/////  INSERT ADS
if($ad==4)
{
$ad=0;
$a = $db->row("SELECT * FROM ads WHERE type = :t AND active = 'y' ORDER BY rand() LIMIT 1",array("t"=>'messages'));
$array['messages'] .= $spacer.'<div class="fleft"><a href="'.$a['link'].$array['admobilelink'].'"><img src="'.$staticads.'/images/ads/'.$a['id'].'.'.$a['ext'].'" width="60"/></a></div>
<div class="disp70">
<span class="onelinetext">Subject: <a href="'.$a['link'].$array['admobilelink'].'">'.$a['text1'].'</a></span>
<div class="space1"></div>
User: <a href="'.$a['link'].$array['admobilelink'].'">'.$a['text2'].'</a>
<div class="space5"></div>
<span class="lightgrey">'.$a['text3'].'</span>
</div>
<div class="clear"></div>
';
}



}


if($array['messages'] == '')
{
$array['messages'] = '<div id="space30"></div>You currently have no messages, but when you do, you will see them here.';
}
