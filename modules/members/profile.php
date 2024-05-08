<?php


$array['extrameta'] .= '
<meta name="robots" content="noindex,follow">';





$id = $_GET['u'];
$array['id'] = $id;

$user = $db->row("SELECT * FROM members WHERE id = :u AND validated = 'y'",array("u"=>$id));



if($user['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=members");
exit;
}


/////////// DELETE MEMBER
if($_GET['del'] == 'y' && $_SESSION['userid'] == '100')
{
$short->deletemember($_GET['u'],$_GET['blacklist']);
$_SESSION['gmessage'] = 'Member Removed Successfully';
header("Location: ".$array['rooturl']."/?mod=members");
exit;
}



//////////////////////////////// ADMIN POST BLACKLIST DOMAIN
if(isset($_POST['bldomain']))
{
if($_SESSION['userid'] == '100' && $_POST['bldomain'] != '')
{

$db->query("INSERT INTO s_blacklist_domains(domain) VALUES(:d)",
array("d"=>$_POST['domain']),PDO::FETCH_ASSOC,"n");

$_SESSION['gmessage'] = 'Domain Added Successfully';
header("Location:".$array['rooturl']."/?u=".$user['id']);
exit;
}
}














/////////////   INCREASE VIEWS
$views = $user['views']+1;
$db->query("UPDATE members SET views = :t WHERE id = :id", array("t"=>$views,"id"=>$user['id']),PDO::FETCH_ASSOC,"n");








///// STNADARD VARIABLES
$array['image'] = ($user['image'] != '') ? '<img  style="width:100%;max-width:354px;" class="" src="'.$rooturl.'/images/members/'.$user['image'].'.jpg" alt="'.$user['username'].'" title="'.$user['username'].'"/>' : '<img border="0" class="" style="width:100%;max-width:354px;" src="'.$staticurl.'/images/default/member.jpg">';

// ONLINE
$onlineperiod = $time-(60*30);/// 20 mins
$array['onlinedot'] = ($user['currentlogin'] > $onlineperiod) ? ' <span class="green" alt="Online Now" title="Online Now">&bull;<span>' : '';
$array['username'] = $short->clean($user['username']);

////FEED ONLY FOR MAIN VIEW
$array['feed'] = '';


//////// DECLARE BUTTONS
$array['followlink'] = '?mod=register';
$array['followtext'] = 'Follow';
$array['blocklink'] = '?mod=register';
$array['blocktext'] = 'Block';
$array['messagelink'] = '?mod=register';

if(isset($_SESSION['userid']))
{
$array['messagelink'] = '?mod=myhome&file=conversation&id='.$user['usercode'].'';
//////////////////// INDIVidUAL ITEMS
// FOLLOW
$array['followlink'] = 'phpfiles/actions.php?follow='.$_GET['u'].'';
$check = $db->query("SELECT id FROM friends WHERE owner = :o and who = :w limit 1",array("o"=>$_SESSION['userid'],"w"=>$id),PDO::FETCH_NUM,'y');
if($check == 1)
{
$array['followtext'] = 'Unfollow';
$array['followlink'] = 'phpfiles/actions.php?unfollow='.$_GET['u'].'';
}
// BLOCK
$array['blocklink'] = 'phpfiles/actions.php?block='.$_GET['u'].'';
$check = $db->query("SELECT id FROM blocks WHERE owner = :o and who = :w limit 1",array("o"=>$_SESSION['userid'],"w"=>$id),PDO::FETCH_NUM,'y');
if($check == 1)
{
$array['blocklink'] = 'phpfiles/actions.php?unblock='.$_GET['u'].'';
$array['blocktext'] = 'Unblock';
}
}

///////// MENU
$array['menu'] = '<a href="../'.$array['followlink'].'"><span class="button width100">'.$array['followtext'].' &nbsp; &rsaquo;</span></a>
<div class="space10"></div>
<a href="../'.$array['blocklink'].'"><span class="button width100">'.$array['blocktext'].' &nbsp; &rsaquo;</span></a>
<div class="space10"></div>
<a href="../'.$array['messagelink'].'"><span class="button width100">Message &nbsp; &rsaquo;</span></a>
<div class="space10"></div>';


if($_SESSION['userid'] == '100')
{
//////  GET DOMAIN FROM EMAIL
list($localemail,$domainemail) = explode("@", $user['email']);
$domainemail = strtolower($domainemail);


$array['menu'] .= '<div class="space10"></div>
<h2>Moderator Menu</h2>
'.$user['regip'].'
<div class="space10"></div>
'.$user['email'].'
<div class="space10"></div>
<a href="../?u='.$_GET['u'].'&del=y"><span class="button width100">Delete Member &nbsp; &rsaquo;</span></a>
<div class="space10"></div>
<a href="../?u='.$_GET['u'].'&del=y&blacklist=y"><span class="button width100">Blacklist Member &nbsp; &rsaquo;</span></a>
<div class="space10"></div>

<span class="formt">Add Blacklist Domain</span>
<form action="" method="post">
<input name="domain" type="text" class="formfield width100" id="domain" value="'.$domainemail.'"  autocorrect="off"  autocomplete="off"   />
<input id="bldomain" name="bldomain" type="submit" value="Ban Domain" class="button ib">
</form>

<div class="space30"></div>

';
}
/// MORE SPACE AT END
$array['menu'] .= '<div class="space20"></div>';


////////////////////////////  STUFF MENU
// FOLLOWING
$tot = $db->query("SELECT id FROM friends WHERE owner = :o",array("o"=>$id),PDO::FETCH_NUM,'y');
$plur = ($tot == 1) ? '' : 's';
$following = ($tot == 0) ? '' : '<div class="space5"></div><span class="is miniuser"></span><a href="../?u='.$id.'&view=following">Following '.number_format($tot).' Member'.$plur.'</a>';

//  FOLLOWERS
$tot = $db->query("SELECT id FROM friends WHERE who = :o",array("o"=>$id),PDO::FETCH_NUM,'y');
$plur = ($tot == 1) ? '' : 's';
$followers = ($tot == 0) ? '' : '<div class="space5"></div><span class="is miniuser"></span><a href="../?u='.$id.'&view=followers">'.number_format($tot).' Follower'.$plur.'</a>';

//  GALLERIES
$tot = $db->query("SELECT id FROM galleries WHERE owner = :o AND completed = 'y' ",array("o"=>$id),PDO::FETCH_NUM,'y');
$plur = ($tot == 1) ? 'y' : 'ies';
$galleries = ($tot == 0) ? '' : '<div class="space5"></div><span class="is miniphoto"></span><a href="../?u='.$id.'&view=galleries">'.number_format($tot).' Photo Galler'.$plur.'</a>';

//  GROUPS
$tot = $db->query("SELECT id FROM groupfollows WHERE owner = :o",array("o"=>$id),PDO::FETCH_NUM,'y');
$plur = ($tot == 1) ? '' : 's';
$groups = ($tot == 0) ? '' : '<div class="space5"></div><span class="is minigroup"></span><a href="../?u='.$id.'&view=groups">Member of '.number_format($tot).' Group'.$plur.'</a>';

//  STORIES
$tot = $db->query("SELECT id FROM stories WHERE owner = :o",array("o"=>$id),PDO::FETCH_NUM,'y');
$plur = ($tot == 1) ? 'y' : 'ies';
$stories = ($tot == 0) ? '' : '<div class="space5"></div><span class="is ministory"></span><a href="../?mod=stories&file=category&uid='.$id.'&view=stories">Written '.number_format($tot).' Sex Stor'.$plur.'</a>';

//  FORUM TOPICS
$tot = $db->query("SELECT id FROM forumtopics WHERE addedby = :o",array("o"=>$id),PDO::FETCH_NUM,'y');
$plur = ($tot == 1) ? '' : 's';
$forum = ($tot == 0) ? '' : '<div class="space5"></div><span class="is miniforum"></span><a href="../?mod=forum&file=category&uid='.$id.'">Created '.number_format($tot).' Forum Topic'.$plur.'</span></a>';

//  PERSONALS
$tot = $db->query("SELECT id FROM classifieds WHERE owner = :o AND title != '' AND delstamp = 0",array("o"=>$id),PDO::FETCH_NUM,'y');
$plur = ($tot == 1) ? '' : 's';
$personals = ($tot == 0) ? '' : '<div class="space5"></div><span class="is miniad"></span><a href="../?u='.$id.'&view=personals">'.number_format($tot).' Personal Ad'.$plur.'</span></a>';



/////////////  DISPLAY STUFF
$array['backlink']  = (isset($_GET['view'])) ? '<a href="../?u='.$id.'"><span class="button width100">View Full Profile &nbsp; &rsaquo;</span></a><div class="space10"></div>': '';
$array['stuff'] = ($backlink == '' && $following == '' && $followers == '' && $galleries == '' && $groups == '' && $stories == '' && $forum == '' && $personals == '') ? '' : '<div class="space10"></div>'.$backlink.$following.$followers.$galleries.$groups.$stories.$forum.$personals.'';


//////////////////////////////// IF YOUR OWN PROFILE
$array['editinfo'] = '';
$array['editimage'] = '';
if($id == $_SESSION['userid'])
{
$array['editinfo'] = '<div class="space20"></div><a href="../?mod=myhome&file=info"><span style="" class="button low">Edit Profile</span></a>';
$array['editimage'] = '<div class="space10"></div>
<a href="..?mod=myhome&file=image"><span class="button low">Edit Image</span></a>';
}




//// AD
$array['ad1'] = '<div class="space30"></div>'.$spacer.$short->contentad($mobilemod);









if($_GET['view'] == 'followers')
{
$page->page .= $page->get_temp('templates/members/profiledetails.htm');
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=members','Members',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?u='.$id,$short->clean($user['username']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Followers',4);

$array['pagetitle'] = 'Followers &middot; '.$user['username'].'';
$array['pagedescription'] = 'Here are the folowers of '.$short->clean($user['username']).' from '.$short->clean($user['town']).' '.$data['country'].' on the Underground Sex Club.';


/// PAGINATION
$resultcount = $db->query("SELECT owner FROM friends WHERE who = {$id} ORDER BY id DESC", null,PDO::FETCH_NUM,'y');
$perpage = '15';
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
$query = $db->query("SELECT owner FROM friends WHERE who = {$id} ORDER BY id DESC LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
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
$array['title'] = number_format($resultcount).' Follower'.$plur;

}






else if($_GET['view'] == 'following')
{
$page->page .= $page->get_temp('templates/members/profiledetails.htm');
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=members','Members',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?u='.$id,$short->clean($user['username']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Following',4);

$array['pagetitle'] = 'Following &middot; '.$user['username'].'';


/// PAGINATION
$resultcount = $db->query("SELECT who FROM friends WHERE owner = {$id} ORDER BY id DESC", null,PDO::FETCH_NUM,'y');
$perpage = '15';
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
$query = $db->query("SELECT who FROM friends WHERE owner = {$id} ORDER BY id DESC LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$array['results'] .= $spacer.$short->user($data['who'],'result','n');
}

////  NO RESULTS
if($array['results'] == '')
{
$array['results'] = 'Sorry, your search returned no results.';
}




$plur = ($resultcount == 1) ? '' : 's';
$array['title'] = 'Following '.number_format($resultcount).' Member'.$plur;
}







else if($_GET['view'] == 'groups')
{
$page->page .= $page->get_temp('templates/members/profiledetails.htm');
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=members','Members',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?u='.$id,$short->clean($user['username']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Groups',4);

$array['pagetitle'] = 'Groups &middot; '.$user['username'].'';


/// PAGINATION
$resultcount = $db->query("SELECT id FROM groupfollows WHERE owner = {$id} ORDER BY id DESC", null,PDO::FETCH_NUM,'y');
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
$query = $db->query("SELECT groupid FROM groupfollows WHERE owner = {$id} ORDER BY id DESC", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$array['results'] .= $spacer.$short->group($data['groupid'],'result');
}

////  NO RESULTS
if($array['results'] == '')
{
$array['results'] = 'Sorry, your search returned no results.';
}



$plur = ($resultcount == 1) ? '' : 's';
$array['title'] = 'Member of '.number_format($resultcount).' Group'.$plur;

}







else if($_GET['view'] == 'personals')
{
$page->page .= $page->get_temp('templates/members/profiledetails.htm');
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=members','Members',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?u='.$id,$short->clean($user['username']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Personals',4);

$array['pagetitle'] = 'Personals &middot; '.$user['username'].'';


/// PAGINATION
$resultcount = $db->query("SELECT id FROM classifieds WHERE owner = {$id} AND title != '' AND delstamp = 0 ORDER BY id DESC", null,PDO::FETCH_NUM,'y');
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
$query = $db->query("SELECT id FROM classifieds WHERE owner = {$id} AND title != '' AND delstamp = 0 ORDER BY id DESC LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$array['results'] .= $spacer.$short->personal($data['id'],'result','y');
}

////  NO RESULTS
if($array['results'] == '')
{
$array['results'] = 'Sorry, your search returned no results.';
}



$plur = ($resultcount == 1) ? '' : 's';
$array['title'] = ''.number_format($resultcount).' Personal Ad'.$plur.'';

}







else if($_GET['view'] == 'galleries')
{
$page->page .= $page->get_temp('templates/members/profiledetails.htm');
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=members','Members',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?u='.$id,$short->clean($user['username']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Galleries',4);


$array['pagetitle'] = 'Galleries &middot; '.$user['username'].'';

/// PAGINATION
$resultcount = $db->query("SELECT id FROM galleries WHERE owner = {$id} AND completed = 'y' ORDER BY id DESC", null,PDO::FETCH_NUM,'y');
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
$query = $db->query("SELECT id FROM galleries WHERE owner = {$id} AND completed = 'y' ORDER BY id DESC LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
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
$array['title'] = ''.number_format($resultcount).' Photo Galler'.$plur;
}






/// PROFILE VIEW
else
{
$array['age'] = $short->age($user['dob_date']);
$array['country'] = $user['country'];
$array['sex'] = $user['sex'];
$array['town'] = ($user['town'] == '') ? '': ''.$short->clean($user['town']).' &middot; ';
$array['about'] = ($user['about'] == '') ? '' : '<div class="space20"></div>'.nl2br(stripslashes($short->clean($user['about']))).'';
//// INTERACT BAR
$array['comments'] = $short->interactbar('member',$id,'y');
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=members','Members',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?u='.$id,$short->clean($user['username']),3);


$array['sexstat'] = ($user['sex_relstatus'] == 'Not Specified') ? '' : '<span class="grey">Relationship Status:</span> '.$user['sex_relstatus'].'<div class="space1"></div>';
$array['sexpref'] = ($user['sex_pref'] == 'Not Specified') ? '' : '<span class="grey">Sexual Preference:</span> '.$user['sex_pref'].'<div class="space1"></div>';
$array['sexfreq'] = ($user['sex_freq'] == 'Not Specified') ? '' : '<span class="grey">Sex Frequency:</span> '.$user['sex_freq'].'<div class="space1"></div>';
$array['sexposition'] = ($user['sex_position'] == '') ? '' : '<span class="grey">Favourite Position:</span> '.$short->clean($user['sex_position']).'<div class="space1"></div>';
//////  ADJUST SPACE FOR NO INFO
if($array['sexstat'] == '' && $array['sexpref'] == '' && $array['sexfreq'] == '' && $array['sexposition'] == '')
{
$array['spaceamount'] = '';
}
else
{
$array['spaceamount'] = 'space10';
}

//// FEED
$newsfeed = '';
$string = 'owner = '.$user['id'].'';
include($serverpath.'/addons/news.php');
/// GET RESULTS
$x=0;
$query = $db->query("SELECT id FROM news WHERE $string ORDER BY stamp DESC LIMIT 20", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$newsfeed .= $spacer.$news->item($data['id']);
}

////  NO RESULTS
if($newsfeed == '')
{
$array['feed'] = '';
}
else
{
$array['feed'] = '<h2>Latest Activity</h2>
'.$newsfeed.'<div class="space30"></div>';
}



//////////////
if($_GET['u'] == 100)
{
$array['pagetitle'] = ''.$short->clean($user['username']).'';
$array['pagedescription'] = 'Profile for System Administration';
$page->page .= $page->get_temp('templates/members/adminprofile.htm');
}
else
{
$array['pagetitle'] = ''.$short->clean($user['username']).'';
$array['pagedescription'] = ($data['about'] != '') ? ''.substr($short->clean($user['about'], 0, 70)).' ... &middot; '.$short->clean($user['username']).' from '.$short->clean($user['town']).' '.$data['country'].'. ' : 'Profile for '.$short->clean($user['username']).' from '.$short->clean($user['town']).' '.$data['country'].'. Get all the details including, activity feed, sex preferences and more.';
$page->page .= $page->get_temp('templates/members/profile.htm');
}




}
