<?php
$id = $_GET['item'];
$array['id'] = $id;
$feed = $db->row("SELECT * FROM feed WHERE id = :id",array("id"=>$id));
if($feed['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$rooturl);
exit;
}


$user = $db->row("SELECT * FROM members WHERE id = :id",array("id"=>$feed['owner']));
if($feed['websiteid'] > 0)
{
$website = $db->row("SELECT * FROM websites WHERE id = :id",array("id"=>$feed['websiteid']));
}


/////////////////////////   EDIT THE POST ADMIN ONLY
if(isset($_POST['ebutton']))
{
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}
//
$db->query("UPDATE feed SET body = :b WHERE id = :id",array("b"=>$post['body'],"id"=>$id),PDO::FETCH_ASSOC,"n");
$_SESSION['gmessage'] = 'Post Edited Successfully';
header("Location:".$array['rooturl']."/?item=".$id);
exit;
}



//////////////////////////////// POST BLACKLIST WORD ADMIN
if(isset($_POST['blword']))
{
if($_SESSION['userid'] == '100' && $_POST['blword'] != '')
{

$db->query("INSERT INTO s_blacklist_words(word,critical) VALUES(:w,:c)",
array("w"=>$_POST['phrase'],"c"=>'y'),PDO::FETCH_ASSOC,"n");

$_SESSION['gmessage'] = 'Phrase Added Successfully';
header("Location:".$array['rooturl']."/?item=".$feed['id']);
exit;
}
}







$trimtitle = mb_strimwidth($short->clean($feed['body']), 0, 40, "...");
$trimdesc = mb_strimwidth($short->clean($feed['body']), 0, 150, "...");

$array['pagetitle'] = ''.$short->clean($user['username']).': '.$trimtitle;
$array['pagedescription'] = 'Post by: '.$short->clean($user['username']).': '.$trimdesc;
$page->page .= $page->get_temp('templates/feed/index.htm');
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Post by '.$short->clean($user['username']).'',2);
///
$array['user'] = $short->user($feed['owner'],'result','n');

$array['copy'] = nl2br($short->clean($feed['body']));
//
$array['website'] = ($website['title'] != '') ? '<div class="space30"></div><h2>Link Details:</h2>'.$short->website($feed['websiteid']).'' : '';

$array['comments'] = $short->interactbar('feed',$feed['id'],'y');





////////   OWNERS POST
$array['deletebutton'] = '';
if($feed['owner'] == $_SESSION['userid'] || $_SESSION['userid'] == '100')
{
$array['deletebutton'] = '<a href="../phpfiles/actions.php?delpost='.$feed['id'].'"><span class="button width100">Delete Post</span></a><div class="space30"></div>';
}

$array['moderator'] = '';

/// MODERATOR
if($_SESSION['userid'] == '100')
{
$array['copy'] = '<form id="form1" name="form1" method="post" action="">
<textarea name="body" cols="" rows="15" class="formfield" style="resize:vertical;width:100%">'.$feed['body'].'</textarea>
<input name="ebutton" type="submit" class="button" id="ebutton" value="Edit" />';


$array['moderator'] = '
<h2>Add Blacklist Word</h2>
<form action="" method="post">
<input name="phrase" type="text" class="formfield width100" id="phrase" value="" placeholder="Phrase" autocorrect="off"  autocomplete="off"   />
<input id="blword" name="blword" type="submit" value="Add" class="button ib">
</form><div class="space30"></div>
';
}
