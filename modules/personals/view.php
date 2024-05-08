<?php


$page->page .= $page->get_temp('templates/personals/view.htm');



$id = $_GET['a'];
$array['id'] = $id;
$personal = $db->row("SELECT * FROM classifieds WHERE id = :id",array("id"=>$id));
if($personal['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=personals");
exit;
}
///
$cat = $db->row("SELECT * FROM classifieds_categories WHERE id = :id",array("id"=>$personal['category']));
$country = $db->row("SELECT * FROM loc_countries WHERE id = :id",array("id"=>$personal['country']));
$state = $db->row("SELECT * FROM loc_states WHERE id = :id",array("id"=>$personal['state']));
$area = $db->row("SELECT * FROM loc_areas WHERE id = :id",array("id"=>$personal['area']));


if($adpost['delstamp'] > 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=personals&co=".$country['id']."&st=".$state['id']."&ar=".$area['id']."&cat=".$cat['id']."");
exit;
}


//////////////////////////////// POST MOVE ADMIN
if(isset($_POST['move']))
{
if($_SESSION['userid'] == '100')
{
$db->query("UPDATE classifieds SET category = :c WHERE id = :id", array("c"=>$_POST['movecat'],"id"=>$personal['id']),PDO::FETCH_ASSOC,"n");
$_SESSION['gmessage'] = 'Ad Moved Successfully';
header("Location:".$array['rooturl']."/?a=".$id);
exit;
}
}

//////////////////////////////// POST BLACKLIST WORD ADMIN
if(isset($_POST['blword']))
{
if($_SESSION['userid'] == '100' && $_POST['blword'] != '')
{

$db->query("INSERT INTO s_blacklist_words(word,critical) VALUES(:w,:c)",
array("w"=>$_POST['phrase'],"c"=>'y'),PDO::FETCH_ASSOC,"n");

$_SESSION['gmessage'] = 'Phrase Added Successfully';
header("Location:".$array['rooturl']."/?a=".$id);
exit;
}
}






//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=personals','Personals',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=personals&cat='.$cat['id'],$cat['title'],3);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=personals&co='.$country['id'].'&cat='.$cat['id'],$country['title'],4);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=personals&co='.$country['id'].'&st='.$state['id'].'&cat='.$cat['id'],$state['title'],5);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=personals&co='.$country['id'].'&st='.$state['id'].'&ar='.$area['id'].'&cat='.$cat['id'],$area['title'],6);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],$short->clean($personal['title']),7);



$array['title'] = $short->clean($personal['title']);
$array['pagetitle'] = $short->clean($personal['title']).' &middot; Adult Personals';
$array['pagedescription'] =$short->clean($personal['description']).' - '.$cat['title'].' '.$area['title'].' '.$state['code'].' '.$country['code'].'';
$array['copy'] = $short->clean($personal['description']);

$array['user'] = $short->user($personal['owner'],'result','n');
$array['added'] = $short->timeago($personal['stamp']);
$array['views'] = number_format($personal['views']);
$array['cat'] = '<a href="../?mod=personals&cat='.$cat['id'].'">'.$short->clean($cat['title']).'</a>';
$array['location'] = '<a href="../?mod=personals&co='.$country['id'].'&st='.$state['id'].'&ar='.$area['id'].'">'.$area['title'].'</a> &middot; <a href="../?mod=personals&co='.$country['id'].'&st='.$state['id'].'">'.$state['code'].'</a> &middot; <a href="../?mod=personals&co='.$country['id'].'">'.$country['code'].'</a>';


/// IMAGE
$array['image'] = ($personal['image'] != '') ? '<div class="space20"></div><a href="../?a='.$personal['id'].'" title="'.$short->clean($personal['title']).'"><img class="maxwidth100" border="0"  src="'.$rooturl.'/images/personals/'.$personal['image'].'.jpg"></a>': '';




//// INCREASE VIEWS
$views = $personal['views']+1;
$db->query("UPDATE classifieds SET views = :t WHERE id = :id", array("t"=>$views,"id"=>$personal['id']),PDO::FETCH_ASSOC,"n");



//// AD
$array['ad1'] = $short->contentad($mobilemod).'<div class="divline"></div>
';




//// SIMILAR
$currenturl = '/?a='.$personal['id'].'';
$phrase = addslashes($personal['title']);
$x=0;
$query = $db->query("SELECT url,type,title,description, MATCH (title, description) AGAINST ('".$phrase."') FROM search WHERE type = 'personal' AND url != :url AND MATCH (title,description) AGAINST ('".$phrase."') LIMIT 5",array("url"=>$currenturl),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$id = str_replace('/?a=','',$data['url']);
$similar .= $spacer.$short->personal($id,'result','n');
}
$array['similar'] = ($similar == '') ? '': '<h2>Similar Ads</h2>'.$similar.'<div class="space30"></div>';




//// New
$query = $db->query("SELECT * FROM classifieds WHERE id != :id AND title != '' AND delstamp = 0 ORDER BY id desc LIMIT 5",array("id"=>$personal['id']),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$new .= $spacer.$short->personal($data['id'],'result','n');
}
$array['new'] = ($new == '') ? '': '<h2>Latest Ads</h2>'.$new.'<div class="space30"></div>';





///////////////  REPLY BUTTON
if(isset($_SESSION['userid']))
{
$array['replybutton'] = '<span class="button" id="newmsg" onClick="show(\'newform\')">Reply &nbsp; &rsaquo;</span>&nbsp;';
$array['replyform'] = '
<div id="newform" style="display:none;"><div class="space20"></div><h2>Post a Reply</h2>
<form id="form" name="form" action="" method="post">
<span class="formt">Subject</span>
<input name="subject" type="text" class="formfield" readonly style="width:100%" value="RE: '.$array['title'].'"/>
<span class="formt">Message</span>
<textarea rows="6" class="formfield" name="message" id="message" style="width:100%;resize:vertical;"></textarea>
<div class="space10"></div>
<input type="submit" class="button" name="button" id="button" value="Send Reply"/>
</form>
</div>';
}
else
{
$array['replybutton'] = '<a href="../?mod=register" alt="Post Reply" title="Post Reply"><span class="button" >Reply &nbsp; &rsaquo;</span></a>&nbsp;';
$array['replyform'] = '';
}

///////////////  OWNER STUFF AND MODERATOR STUFF
$array['delbutton'] = ($personal['owner'] == $_SESSION['userid'] || $_SESSION['userid'] == '100') ? '<a href="../phpfiles/actions.php?delad='.$personal['id'].'" alt="Delete" title="Delete"><span class="button">Delete</span></a>': '';




//////////////////////////////// POST MAIL
if(isset($_POST['button']))
{
$array['replyform'] = '
<div id="newform"><div class="space20"></div><h2>Post a Reply</h2>
<form id="form" name="form" action="" method="post">
<span class="formt">Subject</span>
<input name="subject" type="text" class="formfield" readonly style="width:100%" value="RE: '.$array['title'].'"/>
<span class="formt">Message</span>
<textarea rows="6" class="formfield" name="message" id="message" style="width:100%;resize:vertical;"></textarea>
<div class="space10"></div>
<input type="submit" class="button" name="button" id="button" value="Send Reply" />
</form>
</div>';

foreach($_POST as $key => $value)
{
$value = addslashes($value);
$value = trim($value);
$post[$key] = $value;
}
if($post['message'] == '')
{
$array['errormessage'] = $short->message('Message was Blank', 'r');
}
else
{
$from = $_SESSION['userid'];
$to = $personal['owner'];
$convo = ($to > $from) ? $from.'-'.$to : $to.'-'.$from;
$ok = 'y';
//is sender blocked?
$check = $db->query("SELECT id FROM blocks WHERE owner = :o AND who = :w limit 1",array("o"=>$to,"w"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');
if($check == 0)
{
$query= $db->query("SELECT * FROM s_blacklist_words",null,PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
if(strpos(' '.$post['message'], $data['word'])) {$ok = 'n';}
}
if($ok == 'y')
{
/////  CHECK TOOK LONG TIME TO POST
$time = time();
$timewaited = $time - $_SESSION['reft'];



/// INSERT MESSAGE
$db->query("INSERT INTO pm(`from`,`to`,stamp,message,conversation,subject,personal,timetopost) VALUES(:f,:t,:s,:m,:c,:sub,:p,:ttp)",
array("f"=>$_SESSION['userid'],"t"=>$to,"s"=>$time,"m"=>$post['message'],"c"=>$convo,"sub"=>$post['subject'],"p"=>$personal['id'],"ttp"=>$timewaited),PDO::FETCH_ASSOC,"n");
$mailid = $db->lastInsertId();
////   INSERT ENTRY TO EMAIL
$short->privatemessageemail($to);
}
}
$_SESSION['gmessage'] = 'Reply Sent Successfully';
header("Location:".$array['rooturl']."/?a=".$personal['id']);
exit;
}
}
else ///  NOT POSTING A MESSAGE
{
/// SET SESSION TO CALC TIME
$_SESSION['reft'] = time();
}


$array['moderator'] = '';

if($_SESSION['userid'] == '100')
{
////////////////// GET CATS
$array['cats'] = '<option value="">All</option>';
$query = $db->query("SELECT * FROM classifieds_categories",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$cats .= ($data['id'] == $personal['category']) ? '<option value="'.$data['id'].'" selected="selected">'.$data['title'].'</option>': '<option value="'.$data['id'].'">'.$data['title'].'</option>';
}

$array['moderator'] = '<h2>Moderator Menu</h2>
<span class="formt">Change Category</span>
<form action="" method="post">
<select name="movecat" class="formfield"  autocorrect="off" autocapitalize="off" autocomplete="off">
    '.$cats.'
</select>
<input id="move" name="move" type="submit" value="Move" class="button ib">
</form>

<div class="space30"></div>
<h2>Add Blacklist Word</h2>
<form action="" method="post">
<input name="phrase" type="text" class="formfield width100" id="phrase" value="" placeholder="Phrase" autocorrect="off"  autocomplete="off"   />
<input id="blword" name="blword" type="submit" value="Add" class="button ib">
</form>

<div class="space30"></div>';
}
