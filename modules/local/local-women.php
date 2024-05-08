<?php

$pagename = 'local-women';
$array['leftcol'] = '';
$array['rightcol'] = '';
$canonical = $rooturl.'/?local='.$pagename;
$page->page .= $page->get_temp('templates/local/template.htm');


/// GET TOWN FROM DATABASE
$t = $db->row("SELECT * FROM towns WHERE url = :u",array("u"=>$_GET[''.$pagename.'']));
//// BOOT IF NOT THERE
if($t['url'] == '')
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']);
exit;
}

/////  KEYWORDS FOR USE
$location_full = $t['suburb'].' '.$t['state'].', '.$t['country'];
$location_suburb = $t['suburb'];
$location_state = $t['state'];
$location_country = $t['country'];
/// COUNTRY TEXT
$cname = array(
'AU' => 'Australia',
'USA' => 'United States of America',
'NZ' => 'New Zealand',
'UK' => 'United Kingdom'
);
$location_country_full = $cname[$location_country];
/// TEXT FOR FIND SEARCHES
$find_town = str_replace("'","",$location_suburb);
$find = str_replace("'","",$location_full.' '.$location_country_full);
$find_keyword = str_replace("'","",$location_full.' Meet Women');



//// PAGE DETAILS
$array['pagetitle'] = 'Meet Local Women in '.$location_full.' &middot; Local Pages';
$array['pagedescription'] = 'Meet Local '.$location_suburb.' Women on the Underground Sex Club. Totally free site where you can contact local women in '.$location_full.' for free.';
$array['title'] = 'Local '.$t['suburb'].' Women';



//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=local','Local Pages',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=local&area='.$t['state'].'-'.$t['country'].'','Local Pages '.$t['state'].', '.$t['country'].'',3);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?local='.$t['url'],$t['suburb'].' Local Pages',4);
$array['breadcrumbs'] .= $short->bcitem($canonical,'Local Women in '.$location_suburb,5);


/// TOP PARAGRAPH
$array['leftcol'] .= '<h2>Meeting Local Women in '.$location_suburb.'</h2>
<p>Welcome to the Underground Sex Club '.$location_suburb.'.</p>
<p>If you are looking to meet local '.$location_suburb.' women, this page is certainly going to help you. We have all the information guys need to meet '.$location_suburb.' ('.$location_state.') women. We are a free site with over 200k members, plenty of which are girls looking to meet guys. We have been helping guys meet local '.$location_country.' girls for over 10 years.</p>
<p>We have broken our site up in sections below, and have listed all of our local members near the top. We are one of the few totally free sites and we have no add ons or upgrades for sale. You will need to <a href="https://www.theundergroundsexclub.com/?mod=register">register</a> to contact the '.$location_suburb.' women below.</p>
<div class="space20"></div>
<a href="https://www.theundergroundsexclub.com/?sex=Female&from=From+Age&to=To+Age&sex_pref=Any&sex_relstatus=Any&country='.$location_country_full.'&keywords='.$t['url'].'&file=search&mod=members&button=Filter"><span class="button">View All '.$location_suburb.' Women &nbsp; &middot;&middot;></span></a>
<div class="space30"></div>';



///  MEMBERS
$array['leftcol'] .= '<h2>Women in '.$location_suburb.'</h2>
<p>Here are some of our local or nearby members. Most members below are from '.$location_suburb.', however, if we can\'t find enough local women, we have included other women nearby that you may also be interested in meeting up with. Just remember, it is free to contact any women on our site, you just need to be registered.</p>
<div class="space20"></div>';




//// DO THE QUERY FOR MEMBERS
$sexsql = "AND sex = 'Female' ";
$o1 = array('1' => 'id','2' => 'username','3' => 'email','4' => 'dob_date','5' => 'password','6' => 'usercode','7' => 'regdate'
);
$r1 = rand(1,7);
$o2 = array(
'1' => 'ASC',
'2' => 'DESC'
);
$r2 = rand(1,2);
$orderby = $o1[$r1].' '.$o2[$r2];

/////
$showing = 21;
$tm = 0;
$memberslist = '<tr>';
$rx = 0;
$query = $db->query("SELECT id,username,image, MATCH (country, town, sex,username,sex_relstatus, sex_pref) AGAINST ('".$find_town."') FROM members WHERE validated = 'y' AND image != '' $sexsql AND MATCH (country, town, sex,username,sex_relstatus, sex_pref) AGAINST ('".$find_town."') LIMIT $showing",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$tm++;
$rx++;
if($rx == 8)
{
$memberslist .= '</tr><tr>';
$rx=1;
}
$memberslist .= '<td class="one7th">
<span class="minus2around"><a href="../?u='.$data['id'].'"><img src="'.$staticurl.'/images/members/'.$data['image'].'-thumb.jpg" class="width100" title="'.$data['username'].' &middot; '.$location_suburb.' '.$location_state.'" alt="'.$data['username'].'"/></a></span>
</td>';
}

//
if($tm < $showing)
{
$lim = $showing-$tm;
$query = $db->query("SELECT id,username,image, MATCH (country, town, sex,username,sex_relstatus, sex_pref) AGAINST ('".$find."') FROM members WHERE validated = 'y' AND image != '' $sexsql $boysql ORDER BY $orderby LIMIT $lim",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$tm++;
$rx++;
if($rx == 8)
{
$memberslist .= '</tr><tr>';
$rx=1;
}
$memberslist .= '<td class="one7th">
<span class="minus2around"><a href="../?u='.$data['id'].'"><img src="'.$staticurl.'/images/members/'.$data['image'].'-thumb.jpg" class="width100" title="'.$data['username'].' &middot; Near '.$location_suburb.' '.$location_state.'" alt="'.$data['username'].'"/></a></span>
</td>';
}
}
/// CLOSE CURRENT ROW
$memberslist .= '</tr>';


//// RENDER MEMBERS
$array['leftcol'] .= '<table width="100%" border="0" cellspacing="0" cellpadding="1">
<tbody>
'.$memberslist.'
</tbody>
</table>';






















//////////// DISCUSSIONS
$query = $db->query("SELECT *, MATCH (title) AGAINST ('".$find_keyword."') FROM forumtopics WHERE MATCH (title) AGAINST ('".$find_keyword."') LIMIT 20",null,PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
$x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$forums .= $spacer.$short->forumtopic($data['id'],'result','n');
}
/// RENDER FORUMS
$array['leftcol'] .= ($forums == '') ? '': '<div class="space30"></div>
<h2>'.$t['suburb'].' Forum Topics</h2>
<p>Lots of local members also post in our forum, and it is a great place to start meeting local women in '.$location_suburb.'. Members in other '.$location_state.' areas also use of forum to meetup with girls. Here are some forums and discussions that might be useful for you.</p>
<div class="space10"></div>
'.$forums.'';






//// AD
$a = $db->row("SELECT * FROM ads WHERE type = :t AND active = 'y' ORDER BY rand() LIMIT 1",array("t"=>'content'));
$array['leftcol'] .= '<div class="space30"></div><a href="'.$a['link'].$array['admobilelink'].'"><img src="'.$staticads.'/images/ads/'.$a['id'].'.'.$a['ext'].'" width="100%" style="max-width:'.$a['display_x'].'" border="0"alt=""/></a>
';






////////////////////////////  GROUPS
$tm = 0;
$query = $db->query("SELECT id, MATCH (title,slogan,description) AGAINST ('".$find_keyword."') FROM groups WHERE MATCH (title,slogan,description) AGAINST ('".$find_keyword."') LIMIT 12",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$tm++;
$col=($tm < 7) ? '1': '2';
$spacer = ($tm==1 || $tm==7) ? '': '<div class="divline"></div>';
$array['groups'.$col.''] .= $spacer.$short->group($data['id'], 'result');
if($col==2){$col=0;}
}


if($tm < 12)
{
$lim = 12-$tm;
$query = $db->query("SELECT id FROM groups WHERE image != '' ORDER BY id DESC LIMIT $lim",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$tm++;
$col=($tm < 7) ? '1': '2';
$spacer = ($tm==1 || $tm==7) ? '': '<div class="divline"></div>';
$array['groups'.$col.''] .= $spacer.$short->group($data['id'], 'result');
if($col==2){$col=0;}
}
}

//// RENDER GROUPS
$array['leftcol'] .= '<div class="space30"></div>
<h2>'.$location_suburb.' Sex Groups</h2>
<p>Meeting local women in '.$location_suburb.' might be easier if you join a local sex group. These groups are online sex groups, and are a great way to meet likeminded people.</p> <p>Here are some local sex groups we think you might like. Once again, if there aren\'t enough sex groups in '.$location_suburb.', we have included other popular groups that are nearby.</p>
<div class="space10"></div>
<a href="https://www.theundergroundsexclub.com/?mod=groups&cat=&tag='.$t['url'].'"><span class="button">View All '.$location_suburb.' Sex Groups &nbsp; &middot;&middot;></span></a>
<div class="space20"></div>
<div class="lh728">
'.$array['groups1'].'
</div>
<div class="rh728">
'.$array['groups2'].'
</div>
<div class="clear"></div>';









//// AD
$a = $db->row("SELECT * FROM ads WHERE type = :t AND active = 'y' ORDER BY rand() LIMIT 1",array("t"=>'content'));
$array['leftcol'] .= '<div class="space30"></div><a href="'.$a['link'].$array['admobilelink'].'"><img src="'.$staticads.'/images/ads/'.$a['id'].'.'.$a['ext'].'" width="100%" style="max-width:'.$a['display_x'].'" border="0"alt=""/></a>
';








//////////////////////////////////////  FEEDS
$x=0;
$query = $db->query("SELECT *, MATCH (body) AGAINST ('".$find_keyword."') FROM feed WHERE MATCH (body) AGAINST ('".$find_keyword."') LIMIT 20",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$feeds .= $spacer.'<div class="fleft">
'.$short->user($data['owner'],'image','n').'
</div>
<div class="disp70">
<span>'.$short->user($data['owner'],'text','n').': '.$short->clean($data['body']).'</span>
</div>
<div class="clear"></div>';
}

$array['leftcol'] .= ($feeds == '') ? '': '<div class="space30"></div>
<h2>Member Posts</h2>
<p>The Underground Sex Club '.$location_suburb.' allows members to post directly to the site feed. Here are some posts that members recently posted about meeting up with women in '.$location_full.'.</p>
<p>If you want to post a message directly to local members in the '.$location_suburb.' area, simply <a href="https://www.theundergroundsexclub.com/?mod=register">register</a> for a free account today.</p>
<div class="space10"></div>
'.$feeds.'';





//// AD
$a = $db->row("SELECT * FROM ads WHERE type = :t AND active = 'y' ORDER BY rand() LIMIT 1",array("t"=>'content'));
$array['leftcol'] .= '<div class="space30"></div><a href="'.$a['link'].$array['admobilelink'].'"><img src="'.$staticads.'/images/ads/'.$a['id'].'.'.$a['ext'].'" width="100%" style="max-width:'.$a['display_x'].'" border="0"alt=""/></a>
';



//// LATS BLURB
$array['leftcol'] .= '';













//////   PREV AND NEXT TOWNS
$prev = $db->row("SELECT id,url,suburb,state FROM towns WHERE id < :id AND local_active = 'y' ORDER BY id DESC LIMIT 1",array("id"=>$t['id']));
if($prev['id'] == 0)
{
$prev = $db->row("SELECT id,url,suburb,state FROM towns WHERE id != :id AND local_active = 'y' ORDER BY id DESC LIMIT 1",array("id"=>$t['id']));
}
$prevtown = '<span class="onelinetext"><span class="red fstyle1 fs22 m0">&rsaquo;</span> &nbsp; <a href="'.$rooturl.'/?local-women='.$prev['url'].'">'.$prev['suburb'].' ('.$prev['state'].') Local Women</a></span>
';


$next = $db->row("SELECT id,url,suburb,state FROM towns WHERE id > :id AND local_active = 'y' ORDER BY id ASC LIMIT 1",array("id"=>$t['id']));
if($next['id'] == 0)
{
$next = $db->row("SELECT id,url,suburb,state FROM towns WHERE id != :id AND local_active = 'y' ORDER BY id ASC LIMIT 1",array("id"=>$t['id']));
}
$nexttown = '<span class="onelinetext"><span class="red fstyle1 fs22 m0">&rsaquo;</span> &nbsp; <a href="'.$rooturl.'/?local-women='.$next['url'].'">'.$next['suburb'].' ('.$next['state'].') Local Women</a></span>
';



$array['rightcol'] .= '
<h2>Other Locations</h2>
<div class="space-10"></div>
'.$prevtown.'
<div class="space-5"></div>
'.$nexttown.'';


$array['rightcol'] .= '
<div class="space30"></div>
'.$array['lastrightad'].'';
