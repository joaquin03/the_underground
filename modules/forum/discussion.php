<?php


$page->page .= $page->get_temp('templates/forum/discussion.htm');

$id = $_GET['f'];
$array['id'] = $id;
$topic = $db->row("SELECT * FROM forumtopics WHERE id = :id",array("id"=>$id));
if($topic['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=forum");
exit;
}
//
$cat = $db->row("SELECT * FROM forumcategories WHERE id = :id",array("id"=>$topic['category']));







//////////////////////////////// POST MOVE ADMIN
if(isset($_POST['move']))
{
if($_SESSION['userid'] == '100')
{
$oldcat = $topic['category'];
$newcat = $_POST['movecat'];
/// UPDATE TOPIC
$db->query("UPDATE forumtopics SET category = :c WHERE id = :id", array("c"=>$_POST['movecat'],"id"=>$topic['id']),PDO::FETCH_ASSOC,"n");
///  UPDATE OLD CATEGORY INFO
$query= $db->query("SELECT posts,lastpost FROM forumtopics WHERE category = :c ORDER BY lastpost ASC",array("c"=>$oldcat),PDO::FETCH_ASSOC,"n");
$x=0;
$p=0;
foreach($query as $data)
{
$x++;
$p = $data['posts']+$p;
$lastpost = $data['lastpost'];
}
$db->query("UPDATE forumcategories SET topics = :t, posts = :p, laststamp = :a WHERE id = :id", array("t"=>$x,"p"=>$p,"a"=>$lastpost,"id"=>$oldcat),PDO::FETCH_ASSOC,"n");
//  UPDATE TO CATEGORY INFO
$query= $db->query("SELECT posts,lastpost FROM forumtopics WHERE category = :c ORDER BY lastpost ASC",array("c"=>$newcat),PDO::FETCH_ASSOC,"n");
$x=0;
$p=0;
foreach($query as $data)
{
$x++;
$p = $data['posts']+$p;
$lastpost = $data['lastpost'];
}
$db->query("UPDATE forumcategories SET topics = :t, posts = :p, laststamp = :a WHERE id = :id", array("t"=>$x,"p"=>$p,"a"=>$lastpost,"id"=>$newcat),PDO::FETCH_ASSOC,"n");



/// GET LAST POST FOR RE ADJUSTING LAST TIME
$lasttime = $last['added'];
/////   CALCULATE REMAINING TOPIC POSTS COUNT AND TIME AND UPDATE
$count = $db->query("SELECT id FROM forumposts WHERE topic = :t",array("t"=>$topic['id']),PDO::FETCH_NUM,'y');
$db->query("UPDATE forumtopics SET posts = :p, lastpost = :t WHERE id = :id", array("p"=>$count,"t"=>$lasttime,"id"=>$topic['id']),PDO::FETCH_ASSOC,"n");





$_SESSION['gmessage'] = 'Ad Moved Successfully';
header("Location:".$array['rooturl']."/?f=".$id);
exit;
}
}







//// INCREASE VIEWS
$views = $topic['views']+1;
$db->query("UPDATE forumtopics SET views = :t WHERE id = :id", array("t"=>$views,"id"=>$topic['id']),PDO::FETCH_ASSOC,"n");


$array['forumtitle'] = ''.$short->clean($topic['title']).'';


//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=forum','Sex Forum',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=forum&file=category&id='.$cat['id'],$short->clean($topic['title']),3);


$array['pagetitle'] = $short->clean($topic['title']).'';
$array['pagedescription'] = ''.$short->clean($topic['title']).' -  Join in, make some posts. The Underground Sex Club Free Sex Forum Topic: '.$topic['title'].'';







/// PAGINATION
$resultcount = $db->query("SELECT id FROM forumposts WHERE topic = {$topic['id']} ORDER BY id asc", null,PDO::FETCH_NUM,'y');
$perpage = '20';
$spage = ($_GET['pagenum'] > 1) ? $_GET['pagenum']: 1;
$startnum = ($perpage*($spage-1));
///  REDIRECT FAKE PAGES
if($_GET['page'] > ceil($resultcount/$perpage))
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$short->removepagenum($pageurl));
exit;
}
$array['pagination'] = $short->pagination($resultcount,$perpage,$spage,$pageurl,$paginate_adj);

/// GET RESULTS
$x=0;
$query = $db->query("SELECT id FROM forumposts WHERE topic = {$topic['id']} ORDER BY id asc LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$array['posts'] .= $spacer.$short->forumpost($data['id'],'y');
}

////  NO RESULTS
if($array['posts'] == '')
{
$array['posts'] = 'Sorry, your search returned no results.';
}


$rplur = ($resultcount == 1) ? '': 's';
$array['subtitle'] = ''.number_format($resultcount).' Post'.$rplur.'';






//// SIMILAR
$currenturl = '/?f='.$topic['id'].'';
$phrase = addslashes($topic['title']);
$query = $db->query("SELECT url,type,title,description, MATCH (title, description) AGAINST ('".$phrase."') FROM search WHERE type = 'forumtopic' AND url != :url AND MATCH (title,description) AGAINST ('".$phrase."') LIMIT 5", array("url"=>$currenturl),PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$id = str_replace('/?f=','',$data['url']);
$similar .= $spacer.$short->forumtopic($id,'result','n');
}
$array['similar'] = ($similar == '') ? '': '<h2>Similar Topics</h2><div class="space-10"></div>'.$similar.'<div class="space30"></div>';


//// New
$query = $db->query("SELECT * FROM forumtopics WHERE id != :id ORDER BY id desc LIMIT 5",array("id"=>$topic['id']),PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$new .= $spacer.$short->forumtopic($data['id'],'result','n');
}
$array['newstories'] = ($new == '') ? '': '<h2>Latest Topics</h2><div class="space-10"></div>'.$new.'<div class="space30"></div>';




$array['moderator'] = '';

if($_SESSION['userid'] == '100')
{
////////////////// GET CATS
$array['cats'] = '<option value="">All</option>';
$query = $db->query("SELECT * FROM forumcategories",null,PDO::FETCH_ASSOC,"n");
$cat = '';
foreach($query as $data)
{
////  SEE IF WE NEED A HEADER
if($data['category'] != $cat)
{
$cat = $data['category'];
$cats .= '<option value="">'.$data['category'].'</option>';
}
$selected = ($topic['category'] == $data['id']) ? 'selected="selected"': '';
$cats .= '<option value="'.$data['id'].'" '.$selected.'>&nbsp;&nbsp; &middot; '.$data['title'].'</option>';
}



$array['moderator'] = '<h2>Moderator Menu</h2>
<span class="formt">Change Category</span>
<form action="" method="post">
<select name="movecat" class="formfield"  autocorrect="off" autocapitalize="off" autocomplete="off">
    '.$cats.'
</select>
<input id="move" name="move" type="submit" value="Move" class="button">
</form>
<div class="space30"></div>';
}
