<?php


header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=register");
exit;


$town = urldecode($_GET['club']);

$country = end(explode('-', $_GET['loc']));
$state = str_replace("-".$country,"",$_GET['loc']);
$country = urldecode($country);
$state = urldecode($state);


//list($state, $country) = explode("-", $_GET['loc']);

$find = $town;
$findlower = strtolower($town);
$page->page .= $page->get_temp('templates/misc/club.htm');

//// BOOT IF NOT THERE
if($town == '')
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']);
exit;
}

/////////////   CHECK IF REAL PAGE AND REDIRECT
$t = $db->row("SELECT * FROM towns WHERE suburb = :t AND state = :s AND country = :c",array("t"=>$town,"s"=>$state,"c"=>$country));
if($t['id'] == 0)
{
$similar = $db->row("SELECT *, MATCH (suburb) AGAINST ('".$find."') FROM towns WHERE MATCH (suburb) AGAINST ('".$find."') LIMIT 1");
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?club=".urlencode($similar['suburb'])."&loc=".urlencode($similar['state'])."-".urlencode($similar['country']));
exit;
}

$array['extrameta'] .= '<link rel="canonical" href="'.$array['rooturl']."/?club=".urlencode($t['suburb']).'&loc='.urlencode($t['state']).'-'.urlencode($t['country']).'"/>';


$array['pagetitle'] = $t['suburb'].' Sex Club &middot; '.$t['state'].', '.$t['country'].'';

$array['pagedescription'] = $t['suburb'].' Sex Club . Meet people in '.$t['suburb'].' interested in sex. '.$t['suburb'].' in '.$t['state'].' '.$t['country'].' has a great sex culture and an active sex community. '.$t['suburb'].' Members, Sex Groups, Forum, Photo Galleries and More.';


//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],$t['suburb'].' Sex Club ',2);

//////
$array['title'] = $t['suburb'].' Sex Club <span class="lightgrey">&middot;</span> '.$t['state'].', '.$t['country'].'';
$array['town'] = $t['suburb'];





//////   PREV AND NEXT TOWNS
$prev = $db->row("SELECT * FROM towns WHERE id < :id ORDER BY id DESC LIMIT 1",array("id"=>$t['id']));
if($prev['id'] == 0)
{
$prev = $db->row("SELECT * FROM towns WHERE id != :id ORDER BY id DESC LIMIT 1",array("id"=>$t['id']));
}
$array['prevt'] = '<span class="onelinetext"><span class="red fstyle1 fs22 m0">&rsaquo;</span> &nbsp; <a href="../?club='.urlencode($prev['suburb']).'&loc='.urlencode($prev['state']).'-'.urlencode($prev['country']).'">'.$prev['suburb'].' Sex Club</a></span>';


$next = $db->row("SELECT * FROM towns WHERE id > :id ORDER BY id ASC LIMIT 1",array("id"=>$t['id']));
if($next['id'] == 0)
{
$next = $db->row("SELECT * FROM towns WHERE id != :id ORDER BY id ASC LIMIT 1",array("id"=>$t['id']));
}
$array['nextt'] = '<span class="onelinetext"><span class="red fstyle1 fs22 m0">&rsaquo;</span> &nbsp; <a href="../?club='.urlencode($next['suburb']).'&loc='.urlencode($next['state']).'-'.urlencode($next['country']).'">'.$next['suburb'].' Sex Club</a></span>';




///////// PAGES IN THE SAME TOWN
$query = $db->query("SELECT * FROM keywords ORDER BY rand() LIMIT 5",null,PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
$x++;
$spacer = ($x!=1) ? '<div class="space-5"></div>': '';
$newp = ($data['position'] == 'f') ? ''.$data['keyword'].' '.$t['suburb'].' '.$t['state'].' '.$t['country'].'': ''.$t['suburb'].' '.$data['keyword'].' '.$t['state'].' '.$t['country'].'';
$newptext = ($data['position'] == 'f') ? ''.$data['keyword'].' '.$t['suburb'].'': ''.$t['suburb'].' '.$data['keyword'].'';
$array['links'] .= $spacer.'<span class="onelinetext"><span class="red fstyle1 fs22 m0">&rsaquo;</span> &nbsp; <a href="../?page='.urlencode($newp).'">'.$newptext.'</a></span>';
}







//////////  ADDITIONAL PROFILES AT TOP
$array['fake'] = '';
if(!isset($_SESSION['userid']))
{
$fake .= '<tr>';
$rx = 0;
$query = $db->query("SELECT * FROM fakemembers ORDER BY rand() LIMIT 5",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$rx++;
if($rx == 8)
{
$fake .= '</tr><tr>';
$rx=1;
}
$infobox = ($mobilemod == '') ? '<span class="onelinetext"><a href="../?userinfo='.$data['id'].'">'.$data['username'].'</a></span>
<div class="space1"></div>
<span class="onelinetext">'.$data['age'].' &middot; '.$data['sex'].'</span>
<div class="space5"></div>
<a href="../?mod=register"><span class="tagsbutton">Info</span></a><a href="../?userinfo='.$data['id'].'"><span class="tagsbutton">Message</span></a>': '<div class="space1"></div>
<span class="onelinetext">'.$data['age'].' &middot; '.$data['sex'].'</span>
<div class="space5"></div>
<a href="../?userinfo='.$data['id'].'"><span class="tagsbutton">Info</span></a>';

$fake .= '<td class="one7th">
      <span class="minus2around"><a href="../?userinfo='.$data['id'].'"><img src="'.$array['rooturl'].'/images/users/'.$data['id'].'.jpg" class="width100" alt=""/></a>
	  <div class="space5"></div>
	  '.$infobox.'
	  </span>
      </td>';
}
/// CLOSE CURRENT ROW
$array['fake'] .= '<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tbody>'.$fake.'</tr>
  </tbody>
</table><div class="space20"></div>';

}





$array['buttonclass'] = ($mobilemod == '') ? '': 'width100';







//// AD
$a = $db->row("SELECT * FROM ads WHERE type = :t AND active = 'y' ORDER BY rand() LIMIT 1",array("t"=>'content'));
$array['ad1'] = '<a href="'.$a['link'].$array['admobilelink'].'"><img src="'.$staticads.'/images/ads/'.$a['id'].'.'.$a['ext'].'" width="100%" style="max-width:'.$a['display_x'].'" border="0"alt=""/></a><div class="space30"></div>
';












////////////////////////////  MATCH GROUPS
$tm = 0;
$query = $db->query("SELECT id, MATCH (title,slogan,description) AGAINST ('".$find."') FROM groups WHERE MATCH (title,slogan,description) AGAINST ('".$find."') LIMIT 6",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$tm++;
$col=($tm < 4) ? '1': '2';
$spacer = ($tm==1 || $tm==4) ? '': '<div class="divline"></div>';
$array['groups'.$col.''] .= $spacer.$short->group($data['id'], 'result');
if($col==2){$col=0;}
}


if($tm < 6)
{
$lim = 6-$tm;
$query = $db->query("SELECT id FROM groups WHERE image != '' ORDER BY id DESC LIMIT $lim",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$tm++;
$col=($tm < 4) ? '1': '2';
$spacer = ($tm==1 || $tm==4) ? '': '<div class="divline"></div>';
$array['groups'.$col.''] .= $spacer.$short->group($data['id'], 'result');
if($col==2){$col=0;}
}
}

////////////////// GET CATS
$array['cats'] = '<option value="">Any</option>';
$query = $db->query("SELECT * FROM groupcategories",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
if($data['id'] == $_GET['cat'])
{
$array['cats'] .= '<option value="'.$data['id'].'" selected="selected">'.$data['title'].'</option>';
}
else
{
$array['cats'] .= '<option value="'.$data['id'].'">'.$data['title'].'</option>';
}
}









////////////  MATCH DISCUSSIONS
$query = $db->query("SELECT *, MATCH (title) AGAINST ('".$find."') FROM forumtopics WHERE MATCH (title) AGAINST ('".$find."') LIMIT 5",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$forums .= $short->forumtopic($data['id'],'result','n');
}

$array['forums'] = ($forums == '') ? '': '<div class="space30"></div><h2>'.$t['suburb'].' Forum Topics</h2><div class="space-10"></div>'.$forums.'';







//////////////////////////////////////  MATCH FEEDS
$x=0;
$query = $db->query("SELECT *, MATCH (body) AGAINST ('".$find."') FROM feed WHERE MATCH (body) AGAINST ('".$find."') LIMIT 10",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$feeds .= $spacer.'<div class="fleft">
'.$short->user($data['owner'],'image','n').'
</div>
<div class="disp70">
<span>'.$short->user($data['owner'],'text','n').': '.$short->clean($data['body']).'</span>
<div class="space10"></div>
<span class="onelinetext lightgrey">'.$short->timeago($data['stamp']).'</span>
</div>
<div class="clear"></div>';
}

$array['feeds'] = ($feeds == '') ? '': '<div class="space30"></div><h2>Member Posts</h2>'.$feeds.'';
















$town = $find;
$query = $db->query("SELECT url,type,title,description, MATCH (title, description) AGAINST ('".$town."') FROM search WHERE MATCH (title,description) AGAINST ('".$town."') LIMIT 20",array("ph"=>$town),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
if($data['type'] == 'member')
{
$id = str_replace('/?u=','',$data['url']);
$member = $db->row("SELECT image FROM members WHERE id = :u",array("u"=>$id));
$icon = ($member['image'] != '') ? '<img class="fleft" border="0" height="40" width="40" src="'.$staticurl.'/images/members/'.$member['image'].'-thumb.jpg">': '<img class="fleft" border="0" height="40" width="40" src="'.$staticurl.'/images/default/'.$data['type'].'-thumb.jpg">';
}

else if($data['type'] == 'group')
{
$id = str_replace('/?g=','',$data['url']);
$group = $db->row("SELECT image FROM groups WHERE id = :u",array("u"=>$id));
$icon = ($group['image'] != '') ? '<img class="fleft" border="0" height="40" width="40" src="'.$staticurl.'/images/groups/'.$group['image'].'-thumb.jpg">': '<img class="fleft" border="0" height="40" width="40" src="'.$staticurl.'/images/default/'.$data['type'].'-thumb.jpg">';
}

else
{
$icon= '<img class="fleft" border="0" height="40" width="40" src="'.$staticurl.'/images/default/'.$data['type'].'-thumb.jpg">';
}

$results .= '<div class="divline"></div>
<a href="..'.$data['url'].'">
<span class="fleft">
'.$icon.'
</span>
<span class="disp50 block">
<span class="onelinetext blue">'.$short->clean($data['title']).'</span>
<span id="space5" class="block"></span>
<span class="onelinetext black">'.$short->clean($data['description']).'</span>
</span>
</a>
<div class="clear"></div>';

}




$array['other'] = ($results == '') ? '': '<div class="space30"></div><h2>Related Content</h2>'.$results.'';
