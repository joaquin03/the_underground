<?php
session_start();

///   INCLUDE SITE CONFIG
include('/var/www/vhosts/theundergroundsexclub.com/httpdocs/configfile.php');
include_once(''.$serverpath.'/addons/Db.class.php');
$db = new Db();
include(''.$serverpath.'/addons/short.php');
include(''.$serverpath.'/addons/page.php');



if ($_SERVER['HTTP_REFERER'] != "")
{
$_SESSION['history'] = $_SERVER['HTTP_REFERER'];
}












///////////////////////////////////////////////////   MEMBERS PAST HERE
if(!isset($_SESSION['userid']))
{
header("Location: http://www.theundergroundsexclub.com/?mod=register");
exit;
}
///////  CHECK MEMBER REALLY EXISTS
$member = $db->row("SELECT id FROM members WHERE id = :id AND validated = 'y'",array("id"=>$_SESSION['userid']));
if($member['id'] == 0)
{
header("Location: http://www.theundergroundsexclub.com/?mod=register");
exit;
}




///////////////////////// DEL PERSONAL AD
if(isset($_GET['delphoto']))
{
$dphoto = $db->row("SELECT * FROM galleryimages WHERE id = :id",array("id"=>$_GET['delphoto']));
if($_SESSION['userid'] == $sysadminid || $dphoto['owner'] == $_SESSION['userid'])
{
$short->deletegalleryimage($_GET['delphoto']);
$_SESSION['gmessage'] = 'Photo Deleted';
}
header("Location: ".$_SESSION['history']."");
exit;
}



///////////////////////// DEL PERSONAL AD
if(isset($_GET['delad']))
{
$ad = $db->row("SELECT * FROM classifieds WHERE id = :id",array("id"=>$_GET['delad']));
$id = $ad['id'];
if($_SESSION['userid'] == 100 || $ad['owner'] == $_SESSION['userid'])
{
///
@unlink($serverpath.'/images/personals/'.$ad['image'].'-original.jpg');
@unlink($serverpath.'/images/personals/'.$ad['image'].'-thumb.jpg');
@unlink($serverpath.'/images/personals/'.$ad['image'].'.jpg');
$db->query("UPDATE classifieds SET delstamp = :t WHERE id = :id", array("t"=>$time,"id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE NEWS
$db->query("DELETE FROM news WHERE itemid = :id AND type = 'personalad'", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE Search
$link = '/?a='.$ad['id'];
$db->query("DELETE FROM search WHERE url = :url", array("url"=>$link),PDO::FETCH_ASSOC,"n");
////  UPDATE PMS TO UN INCLUDE THE AD LINK
$db->query("UPDATE pm SET personal = :v WHERE personal = :ad", array("v"=>'0',"ad"=>$id),PDO::FETCH_ASSOC,"n");
////
$_SESSION['gmessage'] = 'Ad Removed Successfully';
}
else
{
$_SESSION['rmessage'] = 'Ad Removal Failed';
}
header("Location: http://www.theundergroundsexclub.com/?mod=personals&co=".$ad['country']."&st=".$ad['state']."&ar=".$ad['area']."&cat=".$ad['category']."");
exit;
}















///////////////////////// DEL STORY
if(isset($_GET['delstory']))
{
$id = $_GET['delstory'];
$story = $db->row("SELECT * FROM stories WHERE id = :id",array("id"=>$id));
if($_SESSION['userid'] == 100 || $story['owner'] == $_SESSION['userid'])
{
/// DEL STORY
$db->query("DELETE FROM stories WHERE id = :id", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE NEWS
$db->query("DELETE FROM news WHERE itemid = :id AND type = 'story'", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE Search
$link = '/?s='.$id;
$db->query("DELETE FROM search WHERE url = :url", array("url"=>$link),PDO::FETCH_ASSOC,"n");
////////////////  UPDATE STORY CATEGORY INFO
$last = $db->row("SELECT * FROM stories WHERE catid = :c ORDER BY id DESC",array("c"=>$story['catid']));
$lasttime = $last['stamp'];
$count = $db->query("SELECT id FROM stories WHERE catid = :c",array("c"=>$story['catid']),PDO::FETCH_NUM,'y');
$db->query("UPDATE storycategories SET stories = :s, laststamp =:t WHERE id = :id", array("s"=>$count,"t"=>$lasttime,"id"=>$story['catid']),PDO::FETCH_ASSOC,"n");
//// DELETE NOTIFICATIONS
$db->query("DELETE FROM notifications WHERE itemid = :id AND itemtype = 'story'", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE COMMENTS
$db->query("DELETE FROM comments WHERE itemid = :id AND type = 'story'", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE VOTES
$db->query("DELETE FROM votes WHERE itemid = :id AND type = 'story'", array("id"=>$id),PDO::FETCH_ASSOC,"n");

////
$_SESSION['gmessage'] = 'Story Removed Successfully';
}
else
{
$_SESSION['rmessage'] = 'Story Removal Failed';
}
header("Location: ".$_SESSION['history']."");
exit;
}























//////////////////////////////////////////////////////////////////////////////////////////////////////////////   VOTE
if($_GET['action'] == 'vote')
{
$type = $_POST['type'];
$id = $_POST['id'];

if($_POST['type'] != '' & $_POST['id'] > 0)
{
// CHECK FOR EXISTING
$check = $db->query("SELECT * FROM votes WHERE owner = :u AND itemid = {$id} AND type = '{$type}' LIMIT 1",
array("u"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');

// IF VOTE DOESN'T EXISTS POST
if($check == 0)
{
$db->query("INSERT INTO votes(owner,itemid,type,stamp) VALUES(:u,:item,:type,:st)",
array("u"=>$_SESSION['userid'],"item"=>$id,"type"=>$type,"st"=>$time),PDO::FETCH_ASSOC,"n");
$voteid = $db->lastInsertId();
/// INSERT NEWS IF NOT A FEED VOTE NEWS
if($type != 'news' && $type != 'feed')
{
$db->query("INSERT INTO news(owner,itemid,type,stamp) VALUES(:u,:item,:type,:st)",
array("u"=>$_SESSION['userid'],"item"=>$voteid,"type"=>'vote',"st"=>$time),PDO::FETCH_ASSOC,"n");
}

/////  SEND OUT NOTIFICATIONS
if($type == 'member')
{
$short->postnotify($id,$_SESSION['userid'],'membervote','member',0);//$owner,$who,$type,$dbtype,$itemid
}
else if($type == 'gallery')
{
$d = $db->row("SELECT owner FROM galleries WHERE id = :id AND completed = 'y' ",array("id"=>$id));
if($d['owner'] > 0)
{
$short->postnotify($d['owner'],$_SESSION['userid'],'galleryvote','gallery',$id);//$owner,$who,$type,$dbtype,$itemid
}
}
else if($type == 'photo')
{
$d = $db->row("SELECT owner FROM galleryimages WHERE id = :id",array("id"=>$id));
if($d['owner'] > 0)
{
$short->postnotify($d['owner'],$_SESSION['userid'],'photovote','photo',$id);//$owner,$who,$type,$dbtype,$itemid
}
}
else if($type == 'feed')
{
$d = $db->row("SELECT owner FROM feed WHERE id = :id",array("id"=>$id));
if($d['owner'] > 0)
{
$short->postnotify($d['owner'],$_SESSION['userid'],'feedvote','feed',$id);//$owner,$who,$type,$dbtype,$itemid
}
}
else if($type == 'story')
{
$d = $db->row("SELECT owner FROM stories WHERE id = :id",array("id"=>$id));
if($d['owner'] > 0)
{
$short->postnotify($d['owner'],$_SESSION['userid'],'storyvote','story',$id);//$owner,$who,$type,$dbtype,$itemid
}
}
//// END NOTIFICATIONS



}
/////////////////  RENDER NEW VOTE SECTION
$votes = $db->query("SELECT id FROM votes WHERE type = '{$type}' and itemid = {$id}",null,PDO::FETCH_NUM,'y');
echo '<div id="votebuttonbox"><span title="Liked" class="is star2"></span>'.number_format($votes).'</div>';

}
else/// ERROR
{
	echo '<div id="votebuttonbox">Vote Error</div>';
}

}/// END VOTE


















//////////////////////////////////////////////////////////////////////////////////////////////////////   COMMENT
if($_GET['action'] == 'comment')
{
/// CHECK COMMENT LIMIT
$timeago = $time-(60*60824);
$commentcount = $db->query("SELECT id FROM comments WHERE owner = :u AND stamp > :t",array("u"=>$_SESSION['userid'],"t"=>$timeago),PDO::FETCH_NUM,'y');
$ok = 'y';

$comment = $_POST['comment'];
$type = $_POST['type'];
$id = $_POST['id'];



////  CHECK BANNED WORDS AND BLACKLIST
$banquery= $db->query("SELECT * FROM s_blacklist_words",null,PDO::FETCH_ASSOC,"n");
$ban = 'n';
foreach($banquery as $bandata)
{
if(strpos(' '.$comment, $bandata['word'])) {$ban = 'y';}
}
//////  IF FAILED THE BAN, BLACKLIST NOW AND EXIT
if($ban == 'y')
{
$ok = 'n';
/// EMAIL ADMIN THE BANNED POST
@mail($adminemailaddress, 'Banned Comment Blacklisted', $array['title'].' ----- '. $comment, "From: ".$adminemailaddress);
$short->deletemember($_SESSION['userid'],'y');
/// LOGOUT
session_destroy();
session_start();
ob_start();
setcookie('active', '', strtotime("-3 months"), '/');
header("Location: http://www.theundergroundsexclub.com");
exit;
}



//// POST IF OK TO POST
if($ok == 'y')
{
if($_POST['comment'] != '' & $_POST['type'] != '' & $_POST['id'] > 0)
{
$body = trim($_POST['comment']);
$body = str_replace(",", "&#8218;", $body);
/// INSERT COMMENT
$insert = $db->query("INSERT INTO comments(owner,itemid,type,body,stamp) VALUES(:u,:item,:type,:body,:st)",
array("u"=>$_SESSION['userid'],"item"=>$id,"type"=>$type,"body"=>$body,"st"=>$time),PDO::FETCH_ASSOC,"n");
$commentid = $db->lastInsertId();

/// INSERT NEWS IF NOT A FEED VOTE NEWS
if($type != 'news' && $type != 'feed')
{
$db->query("INSERT INTO news(owner,itemid,type,stamp) VALUES(:u,:item,:type,:st)",
array("u"=>$_SESSION['userid'],"item"=>$commentid,"type"=>'comment',"st"=>$time),PDO::FETCH_ASSOC,"n");
}

/////  SEND OUT NOTIFICATIONS
if($type == 'member')
{
$short->postnotify($id,$_SESSION['userid'],'membercomments','member',0);//$owner,$who,$type,$dbtype,$itemid
}
else if($type == 'gallery')
{
$d = $db->row("SELECT owner FROM galleries WHERE id = :id AND completed = 'y' ",array("id"=>$id));
if($d['owner'] > 0)
{
$short->postnotify($d['owner'],$_SESSION['userid'],'gallerycomments','gallery',$id);//$owner,$who,$type,$dbtype,$itemid
}
}
else if($type == 'photo')
{
$d = $db->row("SELECT owner FROM galleryimages WHERE id = :id",array("id"=>$id));
if($d['owner'] > 0)
{
$short->postnotify($d['owner'],$_SESSION['userid'],'photocomments','photo',$id);//$owner,$who,$type,$dbtype,$itemid
}
}
else if($type == 'feed')
{
$d = $db->row("SELECT owner FROM feed WHERE id = :id",array("id"=>$id));
if($d['owner'] > 0)
{
$short->postnotify($d['owner'],$_SESSION['userid'],'feedcomments','feed',$id);//$owner,$who,$type,$dbtype,$itemid
}
}
else if($type == 'story')
{
$d = $db->row("SELECT owner FROM stories WHERE id = :id",array("id"=>$id));
if($d['owner'] > 0)
{
$short->postnotify($d['owner'],$_SESSION['userid'],'storycomments','story',$id);//$owner,$who,$type,$dbtype,$itemid
}
}
else if($type == 'group')
{
// NOTIFY ALL GROUP MEMBERS OF COMMENT
$query = $db->query("SELECT owner FROM groupfollows WHERE groupid = :id",array("id"=>$id),PDO::FETCH_ASSOC,"n");
foreach($query as $d)
{
$short->postnotify($d['owner'],$_SESSION['userid'],'groupcomment','group',$id);//$owner,$who,$type,$dbtype,$itemid
}
}
//// END NOTIFICATIONS

}//  END DATA IS COMPLETE
}//  END OK TO SEND - UNDER LIMIT

/// IF MESSAGE LIMIT IS REACHED
else
{
$commentlist .= '<div class="space20"></div>Comment Limit Reached. Your Account is limited to 5 comments in any 24 hour period. Upgrade your account to remove this restriction.<div class="space10"></div><a href="../?mod=upgrade"><span class="button">Upgrade Information</span></a><div class="space10"></div><div class="divline"></div>';
}


//// COMMENTS
$cquery = $db->query("SELECT * FROM comments WHERE type = '{$type}' and itemid = {$id} ORDER BY id DESC",null,PDO::FETCH_ASSOC,"n");
$cc = 0;
foreach($cquery as $cdata)
{
$cc++;
$cdiv = ($cc != 1) ? '<div class="divline"></div>': '<div class="space10"></div>';
$commentlist .= ''.$cdiv.'
<div class="fleft inline">
'.$short->user($cdata['owner'],'image','n').'
</div>
<div class="disp70">
'.$short->user($cdata['owner'],'text','n').': '.$short->clean($cdata['body']).'
<div class="space5"></div>
<span class="grey fs10">'.$short->timeago($cdata['stamp']).''.$delete.'</span>
</div>
<div class="clear"></div>';
}

/////////////////  RENDER NEW COMMENT SECTION
echo $commentlist;
}/// END COMMENT








/////////////////////////  DELETE COMMENT
if(isset($_GET['delcomment']))
{
$comment = $db->row("SELECT * FROM comments WHERE id = :id",array("id"=>$_GET['delcomment']));
if($_SESSION['userid'] == 100 || $comment['owner'] == $_SESSION['userid'] || ($comment['itemid'] == $_SESSION['userid'] && $comment['type'] == 'member'))
{
$db->query("DELETE FROM comments WHERE id = :id", array("id"=>$comment['id']),PDO::FETCH_ASSOC,"n");
//// DELETE NOTIFICATIONS
$type = $comment['type'].'comments';
$db->query("DELETE FROM notifications WHERE who = :w AND type = :t AND itemid = :iid AND stamp = :s",
array("w"=>$comment['owner'],"t"=>$type,"iid"=>$comment['itemid'],"s"=>$comment['stamp']),PDO::FETCH_ASSOC,"n");

$_SESSION['gmessage'] = 'Comment Removed Successfully';
}
else
{
$_SESSION['rmessage'] = 'Comment Removal Failed';
}
header("Location: ".$_SESSION['history']."");
exit;
}























/////////////////////////  FOLLOW A USER
if(isset($_GET['follow']))
{
$id = $_GET['follow'];

//// CHECK BOTH ENTITIES EXIST




////  CHECK DUPLICATE ENTRY
$check = $db->query("SELECT id FROM friends WHERE who = {$id} and owner = :u",array("u"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');

////  ADD ENTRY
if($check == 0)
{
//// ADD FOLLOW
$insert = $db->query("INSERT INTO friends(owner,who,stamp) VALUES(:u,:id,:st)",
array("u"=>$_SESSION['userid'],"id"=>$id,"st"=>$time),PDO::FETCH_ASSOC,"n");
//// ADD NEWS ENTRY
$db->query("INSERT INTO news(owner,itemid,type,stamp) VALUES(:u,:item,:type,:st)",
array("u"=>$_SESSION['userid'],"item"=>$id,"type"=>'follow',"st"=>$time),PDO::FETCH_ASSOC,"n");
//// ADD NOTIFICATION
$short->postnotify($id,$_SESSION['userid'],'follow','member',0);//$owner,$who,$type,$dbtype,$itemid
}

$_SESSION['gmessage'] = 'Member Followed Successfully';

header("Location: ".$_SESSION['history']."");
exit;
}



/////////////////////////  UNFOLLOW A USER
if(isset($_GET['unfollow']))
{
$id = $_GET['unfollow'];
////  DELETE JOIN
$db->query("DELETE FROM friends WHERE who = {$id} and owner = :u", array("u"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");
//// DELETE NEWS
$db->query("DELETE FROM news WHERE itemid = {$id} and owner = :u AND type = 'follow'", array("u"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");
//// DELETE NOTIFICATION
$db->query("DELETE FROM notifications WHERE owner = {$id} and who = :u AND type = 'follow'", array("u"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");

$_SESSION['gmessage'] = 'Unfollowed Successfully';

header("Location: ".$_SESSION['history']."");
exit;
}


/////////////////////////  BLOCK A USER
if(isset($_GET['block']))
{
$id = $_GET['block'];
////  CHECK ENTRY
$check = $db->query("SELECT id FROM blocks WHERE who = {$id} and owner = :u",array("u"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');

////  ADD ENTRY
if($check == 0)
{
//// ADD BLOCK
$insert = $db->query("INSERT INTO blocks(owner,who) VALUES(:u,:id)",
array("u"=>$_SESSION['userid'],"id"=>$id),PDO::FETCH_ASSOC,"n");
}

$_SESSION['gmessage'] = 'Member Blocked Successfully';

header("Location: ".$_SESSION['history']."");
exit;
}


/////////////////////////  UNBLOCK A USER
if(isset($_GET['unblock']))
{
$id = $_GET['unblock'];
////  DELETE JOIN
$db->query("DELETE FROM blocks WHERE who = {$id} and owner = :u", array("u"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");
$_SESSION['gmessage'] = 'Unblocked Successfully';

header("Location: ".$_SESSION['history']."");
exit;
}
















/////////////////////////  JOIN GROUP
if(isset($_GET['join']))
{
$id = $_GET['join'];
////  CHECK ENTRY
$check = $db->query("SELECT id FROM groupfollows WHERE groupid = {$id} and owner = :u",array("u"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');

////  ADD ENTRY
if($check == 0)
{
//// ADD GROUP FOLLOW
$insert = $db->query("INSERT INTO groupfollows(owner,groupid,stamp) VALUES(:u,:id,:st)",
array("u"=>$_SESSION['userid'],"id"=>$id,"st"=>$time),PDO::FETCH_ASSOC,"n");
//// ADD NEWS ENTRY
$db->query("INSERT INTO news(owner,itemid,type,stamp) VALUES(:u,:item,:type,:st)",
array("u"=>$_SESSION['userid'],"item"=>$id,"type"=>'join',"st"=>$time),PDO::FETCH_ASSOC,"n");
}

/// UPDATE GROUP COUNT
$tot = $db->query("SELECT id FROM groupfollows WHERE groupid = :id",array("id"=>$id),PDO::FETCH_NUM,'y');
$db->query("UPDATE groups SET members = :t WHERE id = :id", array("t"=>$tot,"id"=>$id),PDO::FETCH_ASSOC,"n");

/// UPDATE GROUP COUNT ON MEMBER
$tot = $db->query("SELECT id FROM groupfollows WHERE owner = :o",array("o"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');
$db->query("UPDATE members SET count_groups = :t WHERE id = :id", array("t"=>$tot,"id"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");

$_SESSION['gmessage'] = 'Group Joined Successfully';
header("Location: ".$_SESSION['history']."");
exit;
}




/////////////////////////  LEAVE GROUP
if(isset($_GET['unjoin']))
{
$id = $_GET['unjoin'];
////  DELETE JOIN
$db->query("DELETE FROM groupfollows WHERE groupid = {$id} and owner = :u", array("u"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");
//// DELETE NEWS
$db->query("DELETE FROM news WHERE itemid = {$id} and owner = :u AND type = 'join'", array("u"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");
/// UPDATE GROUP COUNT ON GROUP
$tot = $db->query("SELECT id FROM groupfollows WHERE groupid = :id",array("id"=>$id),PDO::FETCH_NUM,'y');
$db->query("UPDATE groups SET members = :t WHERE id = :id", array("t"=>$tot,"id"=>$id),PDO::FETCH_ASSOC,"n");
/// UPDATE GROUP COUNT ON MEMBER
$tot = $db->query("SELECT id FROM groupfollows WHERE owner = :o",array("o"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');
$db->query("UPDATE members SET count_groups = :t WHERE id = :id", array("t"=>$tot,"id"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");

$_SESSION['gmessage'] = 'Group Left Successfully';
header("Location: ".$_SESSION['history']."");
exit;
}




///////////////////////// DEL GROUP
if(isset($_GET['delgroup']))
{
$id = $_GET['delgroup'];
$group = $db->row("SELECT * FROM groups WHERE id = :id",array("id"=>$id));
if($_SESSION['userid'] == 100 || $group['owner'] == $_SESSION['userid'])
{
//// REMOVE IMAGES
@unlink($serverpath.'/images/groups/'.$group['image'].'-original.jpg');
@unlink($serverpath.'/images/groups/'.$group['image'].'.jpg');
@unlink($serverpath.'/images/groups/'.$group['image'].'-thumb.jpg');
/// DEL STORY
$db->query("DELETE FROM groups WHERE id = :id", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE NEWS
$db->query("DELETE FROM news WHERE itemid = :id AND (type = 'group' OR type = 'join')", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE Search
$link = '/?g='.$id;
$db->query("DELETE FROM search WHERE url = :url", array("url"=>$link),PDO::FETCH_ASSOC,"n");
//// DELETE NOTIFICATIONS
$db->query("DELETE FROM notifications WHERE itemid = :id AND itemtype = 'group'", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE COMMENTS
$db->query("DELETE FROM comments WHERE itemid = :id AND type = 'group'", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE VOTES
$db->query("DELETE FROM votes WHERE itemid = :id AND type = 'group'", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE GROUP FOLLOWS LOOP
$query = $db->query("SELECT * FROM groupfollows WHERE groupid = :id",array("id"=>$id),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
/// DELETE LINE
$db->query("DELETE FROM groupfollows WHERE id = :id", array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
/// UPDATE MEMBER COUNT
$tot = $db->query("SELECT id FROM groupfollows WHERE groupid = :id AND owner = :o",array("id"=>$id,"o"=>$data['owner']),PDO::FETCH_NUM,'y');
$db->query("UPDATE members SET count_groups = :t WHERE id = :id", array("t"=>$tot,"id"=>$data['owner']),PDO::FETCH_ASSOC,"n");
}
////  UPDATE FORUM TOPICS FOR GROUP
$db->query("UPDATE forumtopics SET `group` = 0 WHERE `group` = :id", array("id"=>$id),PDO::FETCH_ASSOC,"n");

////  UPDATE GALLERIES FOR THE GROUP
$db->query("UPDATE galleries SET `group` = 0 WHERE `group` = :id", array("id"=>$id),PDO::FETCH_ASSOC,"n");

////
$_SESSION['gmessage'] = 'Group Removed Successfully';
}
else
{
$_SESSION['rmessage'] = 'Group Removal Failed';
}
header("Location: ".$_SESSION['history']."");
exit;
}











/////////////////////////   DELETE A MESSAGE
if(isset($_GET['delmessage']))
{
$id = $_GET['delmessage'];
//
$check = $db->query("SELECT id FROM pm WHERE `to` = :u AND id = :id",array("u"=>$_SESSION['userid'],"id"=>$id),PDO::FETCH_NUM,'y');
if($check == 1)
{
$db->query("UPDATE pm SET delto = :v WHERE id = :id", array("v"=>'y',"id"=>$id),PDO::FETCH_ASSOC,"n");
}
//
$check = $db->query("SELECT id FROM pm WHERE `from` = :u AND id = :id",array("u"=>$_SESSION['userid'],"id"=>$id),PDO::FETCH_NUM,'y');
if($check == 1)
{
$db->query("UPDATE pm SET delfrom = :v WHERE id = :id", array("v"=>'y',"id"=>$id),PDO::FETCH_ASSOC,"n");
}
///// CHECK IF TO AND FROM IS DELETED
$check = $db->query("SELECT id FROM pm WHERE id = :id AND delto = 'y' AND delfrom = 'y'",array("id"=>$id),PDO::FETCH_NUM,'y');
if($check == 1)
{
$idata = $db->row("SELECT * FROM pm WHERE id = :id",array("id"=>$id));
@unlink($serverpath.'/images/messages/'.$idata['image'].'-original.jpg');
@unlink($serverpath.'/images/messages/'.$idata['image'].'.jpg');
}


$_SESSION['gmessage'] = 'Message Removed';
header("Location: ".$_SESSION['history']."");
exit;
}

















///////////////////////////////////////////////////////////  POST NEWS FEED
if(isset($_POST['feedpost']))
{
$ok = 'y';
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}


//// CHECK IF BLANK
if($post['feedpost'] == '')
{
$ok = 'n';
$_SESSION['rmessage'] = 'Nothing to Post';
}


////  CHECK BANNED WORDS AND BLACKLIST
$banquery= $db->query("SELECT * FROM s_blacklist_words",null,PDO::FETCH_ASSOC,"n");
$ban = 'n';
foreach($banquery as $bandata)
{
if(strpos(' '.$post['feedpost'], $bandata['word'])) {$ban = 'y';}
}
//////  IF FAILED THE BAN, BLACKLIST NOW AND EXIT
if($ban == 'y')
{
$ok = 'n';
/// EMAIL ADMIN THE BANNED POST
@mail($adminemailaddress, 'Feed Post Blacklisted', $post['feedpost'], "From: ".$adminemailaddress);
$short->deletemember($_SESSION['userid'],'y');
/// LOGOUT
session_destroy();
session_start();
ob_start();
setcookie('active', '', strtotime("-3 months"), '/');
header("Location: http://www.theundergroundsexclub.com");
exit;
}





/// ALL OK TO POST
if($ok == 'y')
{
$web = 0;
///// GET THE WEBSITE IF IN TEXT AND STORE
$string = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2",$post['feedpost']);
$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
if(preg_match($reg_exUrl, $string, $url)) {
$url = $url[0];
/// CHECK IF URL IS STORED
$check = $db->query("SELECT id FROM websites WHERE url = :url LIMIT 1",array("url"=>$url),PDO::FETCH_NUM,'y');
if($check != 1)
{
$db->query("INSERT INTO websites(url) VALUES(:url)", array("url"=>$url),PDO::FETCH_ASSOC,"n");
$webid = $db->lastInsertId();
}
else
{
$wdata = $db->row("SELECT * FROM websites WHERE url = :url LIMIT 1",array("url"=>$url));
$webid = $wdata['id'];
}
}
/// POST FEED
$db->query("INSERT INTO feed(owner,body,websiteid,stamp) VALUES(:o,:b,:w,:s)",
array("o"=>$_SESSION['userid'],"b"=>$string,"w"=>$webid,"s"=>$time),PDO::FETCH_ASSOC,"n");
$feedid = $db->lastInsertId();

////  INSERT NEWS
$db->query("INSERT INTO news(owner,itemid,type,stamp) VALUES(:u,:item,:type,:st)",
array("u"=>$_SESSION['userid'],"item"=>$feedid,"type"=>'feed',"st"=>$time),PDO::FETCH_ASSOC,"n");

}/// END POST IS OK
/// MESSAGES ALREADY SET
header("Location: ".$_SESSION['history']."");
exit;
}




//////////////  DELETE FEED POST
if(isset($_GET['delpost']))
{
$id = $_GET['delpost'];
$feed = $db->row("SELECT * FROM feed WHERE id = :id",array("id"=>$id));
if($_SESSION['userid'] == 100 || $feed['owner'] == $_SESSION['userid'])
{
//// DELETE NOTIFICATIONS
$db->query("DELETE FROM notifications WHERE itemid = :id AND itemtype = 'feed'", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE NEWS
$db->query("DELETE FROM news WHERE itemid = :id AND type = 'feed'", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE Search
$link = '/?item='.$id;
$db->query("DELETE FROM search WHERE url = :url", array("url"=>$link),PDO::FETCH_ASSOC,"n");
//// DELETE COMMENTS
$db->query("DELETE FROM comments WHERE itemid = :id AND type = 'feed'", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//// DELETE VOTES
$db->query("DELETE FROM votes WHERE itemid = :id AND type = 'feed'", array("id"=>$id),PDO::FETCH_ASSOC,"n");
/// DELETE THE POST
$db->query("DELETE FROM feed WHERE id = :id", array("id"=>$id),PDO::FETCH_ASSOC,"n");
//
$_SESSION['gmessage'] = 'Post Deleted';
}
else
{
$_SESSION['rmessage'] = 'Failed to Delete Post';
}

header("Location: ".$_SESSION['history']."");
exit;
}








///// DELETE MEMBER
if(isset($_GET['deletemember']))
{
//  REMOVE BLOCKS
$short->deletemember($_SESSION['userid'],'n');
session_destroy();
session_start();
ob_start();
header("Location: http://www.theundergroundsexclub.com");
exit;
}








?>
