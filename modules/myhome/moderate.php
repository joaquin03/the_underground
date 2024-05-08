<?php
if($_SESSION['userid'] != 100)
{
header("Location:".$array['rooturl']."");
exit;
}



$array['pagetitle'] = 'Moderation';
$page->page .= $page->get_temp('templates/myhome/moderate.htm');

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Moderation',2);
//
$array['keyword'] = $_GET['keyword'];
$array['omit'] = $_GET['omit'];

$theurl = '/?mod=myhome&file=moderate&what='.$_GET['what'].'&keyword='.$_GET['keyword'].'&omit='.$_GET['omit'].'';

////
$array['messages-select'] = '';
$array['comments-select'] = '';
$array['feed-select'] = '';
$array[''.$_GET['what'].'-select'] = 'selected';

/////////// DELETE MEMBER
if(isset($_GET['deluser']))
{
$short->deletemember($_GET['deluser'],$_GET['blacklist']);
$_SESSION['gmessage'] = 'Member Removed Successfully';
header("Location: ".$array['rooturl'].$theurl);
exit;
}








/// DELETE MESSAGE
if(isset($_GET['delmessage']))
{
/// GET CURRENT MESSAGE
$mail = $db->row("SELECT * FROM pm WHERE id = :id LIMIT 1",array("id"=>$_GET['delmessage']));
///
@unlink('/var/www/vhosts/theundergroundsexclub.com/httpdocs/images/messages/'.$mail['image'].'-original.jpg');
@unlink('/var/www/vhosts/theundergroundsexclub.com/httpdocs/images/messages/'.$mail['image'].'.jpg');
$from = '100';
$to = $mail['to'];
$convo = ($to > $from) ? $from.'-'.$to : $to.'-'.$from;
/// UPDATE MESSAGE TO REFLECT A SYSTEM ADMIN MESSAGE
$subject = 'Message Removed';
$message = 'Sorry, this message was originally sent to you by another member, but has been removed because of a violation.';
$db->query("UPDATE pm SET `from` = :f, conversation = :c, subject = :s, message = :m, delfrom = :df, image = :ii WHERE id = :id", array("f"=>'100',"c"=>$convo,"s"=>$subject,"m"=>$message,"df"=>'y',"ii"=>'',"id"=>$_GET['delmessage']),PDO::FETCH_ASSOC,"n");

$_SESSION['gmessage'] = 'Message Deleted Successfully '.$convo;
header("Location: ".$array['rooturl'].$theurl);
exit;
}

















////////////  POST FILTER
if(isset($_GET['what']))
{
$limit = ($_GET['omit'] > 0) ? "LIMIT 1000 OFFSET ".$_GET['omit']."": "LIMIT 1000";

//// MESSAGES
if($_GET['what'] == 'messages')
{
$sql2 = "WHERE delto != 'y' AND `from` != 100 ORDER BY id DESC ".$limit."";
$sql3 = '';
if($_GET['keyword'] != '')
{
$sql2 = "WHERE delto != 'y' AND `from` != 100 AND MATCH (message) AGAINST ('".$_GET['keyword']."')";
$sql3 = ", MATCH (message) AGAINST ('".$_GET['keyword']."')";
}
$query= $db->query("SELECT * $sql3 FROM pm $sql2",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
/// IF IMAGE
$image = ($data['image'] == '') ? '': '<div class="space10"></div><img style="max-height:100px;" src="'.$rooturl.'/images/messages/'.$data['image'].'.jpg"/>';

$array['results'] .= $short->user($data['from'],'text','n').' &nbsp; to &nbsp; '.$short->user($data['to'],'text','n').' &nbsp; &middot; &nbsp; Subject: '.$short->clean($data['subject']).'
<div class="space5"></div>
'.$short->clean($data['message']).$image.'
<div class="space10"></div>
<a href="..'.$theurl.'&delmessage='.$data['id'].'">Delete Message</a> &nbsp; &middot; &nbsp; <a href="..'.$theurl.'&deluser='.$data['from'].'">Delete User</a> &nbsp; &middot; &nbsp; <a href="..'.$theurl.'&deluser='.$data['from'].'&blacklist=y">Blacklist User</a>  &nbsp; &middot; &nbsp; '.$short->timeago($data['stamp']).'  &nbsp; &middot; &nbsp; Post Time: '.$data['timetopost'].' seconds
<div class="divline"></div>';
}

}


////  COMMENTS
else if($_GET['what'] == 'comments')
{
$sql2 = "WHERE owner != 100 ORDER BY id DESC ".$limit."";
$sql3 = '';
if($_GET['keyword'] != '')
{
$sql2 = "WHERE owner != 100 AND MATCH (body) AGAINST ('".$_GET['keyword']."')";
$sql3 = ", MATCH (body) AGAINST ('".$_GET['keyword']."')";
}
$query= $db->query("SELECT * $sql3 FROM comments $sql2",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$array['results'] .= $short->user($data['owner'],'text','n').' &nbsp; &middot; &nbsp; '.ucwords($data['type']).'
<div class="space5"></div>
'.$short->clean($data['body']).'
<div class="space10"></div>
<a href="../phpfiles/actions.php?delcomment='.$data['id'].'">Delete Comment</a> &nbsp; &middot; &nbsp; <a href="..'.$theurl.'&deluser='.$data['owner'].'">Delete User</a> &nbsp; &middot; &nbsp; <a href="..'.$theurl.'&deluser='.$data['owner'].'&blacklist=y">Blacklist User</a>
<div class="divline"></div>';
}

}


////  FEED
else if($_GET['what'] == 'feed')
{
$key = ($_GET['keyword'] != '') ? "AND body LIKE '%".urldecode($_GET['keyword'])."%'": "";
$query= $db->query("SELECT * FROM feed WHERE owner != 100 $key ORDER BY id DESC ".$limit."",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$array['results'] .= $short->user($data['owner'],'text','n').'
<div class="space5"></div>
'.$short->clean($data['body']).'
<div class="space10"></div>
<a href="../phpfiles/actions.php?delpost='.$data['id'].'">Delete Post</a> &nbsp; &middot; &nbsp; <a href="..'.$theurl.'&deluser='.$data['owner'].'">Delete User</a> &nbsp; &middot; &nbsp; <a href="..'.$theurl.'&deluser='.$data['owner'].'&blacklist=y">Blacklist User</a>
<div class="divline"></div>';
}}


}


$array['results'] = ($array['results'] != '') ? $array['results'] : 'Filter to Begin';
