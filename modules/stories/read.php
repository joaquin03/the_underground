<?php



$page->page .= $page->get_temp('templates/stories/read.htm');

$id = $_GET['s'];
$array['id'] = $id;
$story = $db->row("SELECT * FROM stories WHERE id = :id",array("id"=>$id));
if($story['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=stories");
exit;
}
$cat = $db->row("SELECT * FROM storycategories WHERE id = :id",array("id"=>$story['catid']));


///  STUFF
$array['title'] = ''.$short->clean($story['title']).'';

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=stories','Sex Stories',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=stories&file=category&id='.$cat['id'],$short->clean($cat['title']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],$short->clean($story['title']),4);


$array['user'] = $short->user($story['owner'],'result','n');
$array['added'] = $short->timeago($story['stamp']);
$array['views'] = number_format($story['views']);
$array['comments'] = $short->interactbar('story',$story['id'],'y');
$array['copy'] = nl2br($short->clean($story['body']));
$array['pagetitle'] = ''.$short->clean($story['title']).'';
$array['cat'] = '<a href="../?mod=stories&file=category&id='.$cat['id'].'">'.$short->clean($cat['title']).'</a>';

//// INCREASE VIEWS
$views = $story['views']+1;
$db->query("UPDATE stories SET views = :t WHERE id = :id", array("t"=>$views,"id"=>$story['id']),PDO::FETCH_ASSOC,"n");




//////////////////////////////// POST MOVE ADMIN
if(isset($_POST['move']))
{
if($_SESSION['userid'] == '100')
{
/// UPDATE STORY
$db->query("UPDATE stories SET catid = :c WHERE id = :id", array("c"=>$_POST['movecat'],"id"=>$id),PDO::FETCH_ASSOC,"n");
//// UPDATE CATEGORIES
$oldcat = $story['catid'];
$newcat = $_POST['movecat'];
/// OLD CAT
$last = $db->row("SELECT stamp FROM stories WHERE catid = :c ORDER BY id DESC",array("c"=>$oldcat));
$lasttime = $last['stamp'];
$count = $db->query("SELECT id FROM stories WHERE catid = :c",array("c"=>$oldcat),PDO::FETCH_NUM,'y');
$db->query("UPDATE storycategories SET stories = :s, laststamp =:t WHERE id = :id",
array("s"=>$count,"t"=>$lasttime,"id"=>$oldcat),PDO::FETCH_ASSOC,"n");
/// NEW CAT
$last = $db->row("SELECT stamp FROM stories WHERE catid = :c ORDER BY id DESC",array("c"=>$newcat));
$lasttime = $last['stamp'];
$count = $db->query("SELECT id FROM stories WHERE catid = :c",array("c"=>$newcat),PDO::FETCH_NUM,'y');
$db->query("UPDATE storycategories SET stories = :s, laststamp =:t WHERE id = :id",
array("s"=>$count,"t"=>$lasttime,"id"=>$newcat),PDO::FETCH_ASSOC,"n");

$_SESSION['gmessage'] = 'Story Moved Successfully';
header("Location:".$array['rooturl']."/?s=".$id);
exit;
}
}





//// AD
$array['ad1'] = $short->contentad($mobilemod).'<div class="divline"></div>
';




//// SIMILAR
$currenturl = '/?s='.$story['id'].'';
$phrase = addslashes($story['title']);
$query = $db->query("SELECT url,type,title,description, MATCH (title, description) AGAINST ('".$phrase."') FROM search WHERE type = 'story' AND url != :url AND MATCH (title,description) AGAINST ('".$phrase."') LIMIT 5",array("url"=>$currenturl),PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$id = str_replace('/?s=','',$data['url']);
$similar .= $spacer.$short->story($id,'result','n');
}
$array['similar'] = ($similar == '') ? '': '<h2>Similar Stories</h2><div class="space-10"></div>'.$similar.'<div class="space30"></div>';





//// New
$query = $db->query("SELECT * FROM stories WHERE id != :id ORDER BY id desc LIMIT 5",array("id"=>$story['id']),PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$newstories .= $spacer.$short->story($data['id'],'result','n');
}
$array['newstories'] = ($newstories == '') ? '': '<h2>Latest Stories</h2><div class="space-10"></div>'.$newstories.'<div class="space30"></div>';




//////  MODERATION
$array['delete'] = ($_SESSION['userid'] == '100' || $_SESSION['userid'] == $story['owner']) ? '<a href="../phpfiles/actions.php?delstory='.$story['id'].'"><span class="button">Delete Story</span></a>
<div class="space20"></div>': '';

$array['moderator'] = '';
if($_SESSION['userid'] == '100')
{
////////////////// GET CATS
$array['cats'] = '<option value="">All</option>';
$query = $db->query("SELECT * FROM storycategories",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$cats .= ($data['id'] == $story['catid']) ? '<option value="'.$data['id'].'" selected="selected">'.$data['title'].'</option>': '<option value="'.$data['id'].'">'.$data['title'].'</option>';
}

$array['moderator'] = '<h2>Moderator Menu</h2>
<span class="formt">Change Category</span>
<form action="" method="post">
<select name="movecat" class="formfield"  autocorrect="off" autocapitalize="off" autocomplete="off">
    '.$cats.'
</select>
<input id="move" name="move" type="submit" value="Move" class="button ib">
</form>
<div class="space30"></div>';
}
