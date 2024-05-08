<?php




$id = $_GET['g'];
$array['id'] = $id;

$group = $db->row("SELECT * FROM groups WHERE id = :id",array("id"=>$id));



if($group['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=groups");
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
header("Location:".$array['rooturl']."/?g=".$id);
exit;
}
}




/////////////   INCREASE VIEWS
$views = $group['views']+1;
$db->query("UPDATE groups SET views = :t WHERE id = :id", array("t"=>$views,"id"=>$group['id']),PDO::FETCH_ASSOC,"n");



////  DECIFER IMAGE LOCATION
$array['image'] = ($group['image'] != '') ? '<img  style="width:100%;max-width:354px;" class="" src="'.$rooturl.'/images/groups/'.$group['image'].'.jpg" alt="'.$short->clean($group['title']).'" title="'.$short->clean($group['title']).'"/>' : '<img border="0" class="" style="width:100%;max-width:354px;" src="'.$staticurl.'/images/default/group.jpg">';





///// SIMILAR GROUPS
$find = strip_tags($group['title']);
$find = trim ($find);
$find = addslashes($find);
$qty = ($mobilemod == '') ? '15': '5';

$find = $short->clean($find);
$query = $db->query("SELECT id, MATCH (title,slogan,description) AGAINST ('".$find."') FROM groups WHERE id != $id  AND MATCH (title,slogan,description) AGAINST ('".$find."') LIMIT $qty",array("id"=>$id),PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
$x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$similar .= $spacer.$short->group($data['id'],'result');
}
$array['similar'] = '';
if($similar != '')
{
$array['similar'] = '<h2>Similar Sex Groups</h2>'.$similar.'<div class="space30"></div>';
}













////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  VIEW MEMBERS
if($_GET['view'] == 'members')
{
$array['pagetitle'] = 'Members of Sex Group: '.$group['title'];
$array['pagedescription'] = 'View the current members of '.$group['title'].' here. Currently, this group has {memtot} members';


//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=groups','Sex Groups',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?g='.$id,$short->clean($group['title']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Members',4);


$page->page .= $page->get_temp('templates/groups/groupdetails.htm');



/// PAGINATION
$resultcount = $db->query("SELECT * FROM groupfollows WHERE groupid = {$id} ORDER BY id DESC", null,PDO::FETCH_NUM,'y');
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
$query = $db->query("SELECT * FROM groupfollows WHERE groupid = {$id} ORDER BY id DESC LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$array['results'] .= $spacer.$short->user($data['owner'],'result','n');
}

////  NO RESULTS
if($array['results'] == '')
{
$array['results'] = 'Sorry, your search returned no results.';
}





$plur = ($resultcount == 1) ? '' : 's';
$array['title'] = ''.number_format($resultcount).' Member'.$plur.' of: '.$group['title'].'';

}////////////////////// END FOLLOWERS



////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  VIEW GALLERIES
else if($_GET['view'] == 'galleries')
{
$array['pagetitle'] = 'Photo Galleries for Sex Group: '.$group['title'].'';
$array['pagedescription'] = 'View the amateur photo galleries for the sex group '.$group['title'].'. This sex group has uploaded {total} amateur galleries.';
$page->page .= $page->get_temp('templates/groups/groupdetails.htm');

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=groups','Sex Groups',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?g='.$id,$short->clean($group['title']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Galleries',4);

/// PAGINATION
$resultcount = $db->query("SELECT id FROM galleries WHERE `group` = {$id} AND completed = 'y' ORDER BY id DESC", null,PDO::FETCH_NUM,'y');
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
$query = $db->query("SELECT id FROM galleries WHERE `group` = {$id} AND completed = 'y' ORDER BY id DESC LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$array['results'] .= $spacer.$short->gallery($data['id'],'large','y');
}

////  NO RESULTS
if($array['results'] == '')
{
$array['results'] = 'Sorry, your search returned no results.';
}



$plur = ($resultcount == 1) ? 'y' : 'ies';
$array['title'] = ''.number_format($resultcount).' Photo Galler'.$plur.' for: '.$short->clean($group['title']);

}////////////////////// END Galleries



////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  VIEW GENERAL INFO
else
{


//// AD
$array['ad1'] = '<div class="space30"></div>'.$short->contentad($mobilemod);


$array['comments'] = $short->interactbar('group',$id,'y');



$page->page .= $page->get_temp('templates/groups/group.htm');
$array['pagetitle'] = ''.$group['title'].' &middot; Sex Group';
$array['pagedescription'] = ''.$group['title'].' is a sex group that you can join for Free. '.$group['title'].' currently has '.$group['members'].' members. Join for free now.';
$array['gmembers'] = '';
$array['galleries'] = '';
$array['forums'] = '';
$array['title'] = $short->clean($group['title']);
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=groups','Sex Groups',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?g='.$id,$short->clean($group['title']),3);

$catdata = $db->row("SELECT * FROM groupcategories WHERE id = :id",array("id"=>$group['catid']));
$array['cat'] = $catdata['title'];
$array['about'] = $group['description'];
$array['started'] = $short->timeago($group['stamp']);
$array['website'] = ($group['website'] == '') ? '' : '<span class="onelinetext"><span class="grey">Website: </span> <a href="'.$group['website'].'" target="_blank" rel="nofollow">View Website</a></span><div class="space10"></div>';

$array['slogan'] = ($group['slogan'] == '') ? '' : '<span class="grey">Slogan: </span>'.$short->clean($group['slogan']).'<div class="space10"></div>';
$array['about'] = ($group['description'] == '') ? '' : '<div class="space10"></div>'.nl2br(stripslashes($short->clean($group['description']))).'';


$array['followlink'] = '?mod=register';
$array['followtext'] = 'Join Group';
$array['delbutton'] = '';
$array['addgallerylink'] = '?g='.$id.'&mem=n';
$array['adddiscussionlink'] = '?g='.$id.'&mem=n';
if(isset($_GET['mem']))
{
$array['errormessage'] = $short->message('You must Join this Group to use that feature', 'r');
}



if(isset($_SESSION['userid']))
{
//////////////////// INDIVidUAL ITEMS
// FOLLOW
$array['followlink'] = 'phpfiles/actions.php?join='.$_GET['g'].'';
$check = $db->query("SELECT id FROM groupfollows WHERE owner = :id AND groupid = $id LIMIT 1",array("id"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');
if($check == 1)
{
$array['followtext'] = 'Leave Group';
$array['followlink'] = 'phpfiles/actions.php?unjoin='.$_GET['g'].'';
//
$array['addgallerylink'] = '?mod=galleries&file=new&group='.$id.'';
$array['adddiscussionlink'] = '?mod=discussions&file=adddiscussion&cat=&group='.$id.'';
}
//////////////////////////////// IF YOUR OWN GROUP
$editbutton = '';
$editimage = '';
$delbutton = '';
if($group['owner'] == $_SESSION['userid'] || $_SESSION['userid'] == '100')
{
$delbutton = '<a href="'.$array['rooturl'].'/phpfiles/actions.php?delgroup='.$id.'"><span class="button width100">Delete Group &nbsp; &rsaquo;</span></a>
<div class="space10"></div>';
$editbutton = '<a href="../?mod=groups&file=edit&id='.$_GET['g'].'"><span class="button width100">Edit Group Info &nbsp; &rsaquo;</span></a><div class="space10"></div>';
$editimage = '
<a href="../?mod=groups&file=editpic&id='.$_GET['g'].'"><span class="button width100">Edit Group Image &nbsp; &rsaquo;</span></a><div class="space10"></div>';
if($group['image'] == '')
{
$editimage = '
<a href="../?mod=groups&file=editpic&id='.$_GET['g'].'"><span class="button width100">Add Group Image &nbsp; &rsaquo;</span></a><div class="space10"></div>';
}

}
}// END ISSET ACTIVE

////// MENU
$array['menu'] = '<a href="../'.$array['followlink'].'"><span class="button width100">'.$array['followtext'].' &nbsp; &rsaquo;</span></a>
<div class="space10"></div>
<a href="../'.$array['addgallerylink'].'"><span class="button width100">Add a Gallery &nbsp; &rsaquo;</span></a>
<div class="space10"></div>
<a href="../'.$array['adddiscussionlink'].'"><span class="button width100">Add a Discussion &nbsp; &rsaquo;</span></a>
<div class="space10"></div>
'.$editbutton.$editimage.$delbutton.'

';


///// GALLERIES
$query = $db->query("SELECT id FROM galleries WHERE `group` = :id AND completed = 'y' ORDER BY id DESC",array("id"=>$id),PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$spacer = ($x == 1) ? '': '<div class="divline"></div>';
if($x < 5)
{
$galleries .= $spacer.$short->gallery($data['id'],'large','y').'';
}
}
/// MORE
$more = ($x > 4) ? ' <a href="../?g='.$id.'&view=galleries">View All</a>': '';
if($galleries != '')
{
$array['galleries'] = '<div class="space30"></div><h2>Group Galleries <span class="lightgrey">('.number_format($x).')</span>'.$more.'</h2>'.$galleries.'';
}





//////   DISCUSSIONS
$query = $db->query("SELECT id FROM forumtopics WHERE `group` = :id ORDER BY lastpost DESC",array("id"=>$id),PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
if($x < 12)
{
$forums .= $short->forumtopic($data['id'],'result','y');
}
}
/// MORE
$more = ($x > 5) ? ' <a href="../?mod=forum&file=category&gid='.$id.'">View All</a>': '';
if($forums != '')
{
$array['forums'] = '<div class="space30"></div><h2>Group Discussions <span class="lightgrey">('.number_format($x).')</span>'.$more.'</h2><div class="space-10"></div>
'.$forums;
}




//// COLUMNS
$array['galcol'] = ($forums != '' && $galleries != '') ? 'lh728': '';
$array['forumcol'] = ($forums != '' && $galleries != '') ? 'rh728': '';

$array['galleryad'] = '';
//// AD UNDER GALLERIES
if($forums != '' || $galleries != '')
{
$array['galleryad'] = '<div class="space30"></div>'.$short->contentad($mobilemod);
}



//// MEMBERS
$query = $db->query("SELECT members.id FROM groupfollows INNER JOIN members ON groupfollows.owner = members.id WHERE members.image != '' AND groupfollows.groupid = :id ORDER BY members.lastonline DESC LIMIT 14",array("id"=>$id),PDO::FETCH_ASSOC,"n");
$x = 0;
$rx = 0;
$gmems .= '<tr>';
foreach($query as $data)
{
$rx++;
$x++;
if($rx == 8)
{
$gmems .= '</tr><tr>';
$rx=1;
}
$gmems .= '<td class="one7th">
      '.$short->user($data['id'],'image','y').'
      </td>';
}
if($x < 14)
{
$lim = 14- $x;
$query = $db->query("SELECT members.id FROM groupfollows INNER JOIN members ON groupfollows.owner = members.id WHERE members.image = '' AND groupfollows.groupid = $id ORDER BY members.lastonline DESC LIMIT $lim",array("id"=>$id),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$x++;
if($rx == 8)
{
$gmems .= '</tr><tr>';
$rx=1;
}
$gmems .= '<td class="one7th">
      '.$short->user($data['id'],'image','y').'
      </td>';
}
}
/// CLOSE CURRENT ROW
$array['images'] .= '</tr>';



////  SHOW IF SOME MEMBERS
if($x > 0)
{
//// AD
$ad = '<div class="space30"></div>'.$short->contentad($mobilemod);

//
$more = ($group['members'] > 21) ? ' <a href="../?g='.$id.'&view=members">View All</a>': '';
$array['gmembers'] .= '<div class="space30"></div><h2>Group Members <span class="lightgrey">('.number_format($group['members']).')</span>'.$more.'</h2>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tbody>
      '.$gmems.'
  </tbody>
</table>'.$ad.'';
}




}////////////////// END IFO MAIN VIEW




$array['moderator'] = '';

if($_SESSION['userid'] == '100')
{
////////////////// GET CATS
$array['moderator'] = '
<div class="space30"></div>
<h2>Add Blacklist Word</h2>
<form action="" method="post">
<input name="phrase" type="text" class="formfield width100" id="phrase" value="" placeholder="Phrase" autocorrect="off"  autocomplete="off"   />
<input id="blword" name="blword" type="submit" value="Add" class="button ib">
</form>
';
}
