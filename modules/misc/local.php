<?php


$page->page .= $page->get_temp('templates/misc/local.htm');

/// GET TOWN FROM DATABASE
$t = $db->row("SELECT * FROM towns WHERE url = :u",array("u"=>$_GET['local']));
//// BOOT IF NOT THERE
if($t['url'] == '')
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']);
exit;
}



















$array['test'] = $t['url'];


/*















$p = $db->row("SELECT * FROM pages WHERE phrase = :p",array("p"=>$phrase));
if($p['id'] == 0)
{
$k = $db->row("SELECT * FROM keywords ORDER BY rand() LIMIT 1");
$t = $db->row("SELECT * FROM towns ORDER BY rand() LIMIT 1");
$p['phrase'] = $short->clean($phrase);
/// GET DATA FOR PAGE PAGE
$array['pagetitle'] = $p['phrase'].'';
$array['pagedescription'] = $p['phrase'].' on the underground sex club. Join our free site. Members, Sex Groups, Forum, Photo Galleries and More. '.$p['phrase'].'';
$array['breadcrumb'] = $short->breadcrumb($p['phrase'],$array['ogurl']);
//////
$array['title'] = $p['phrase'].'';
$array['town'] = '';
$array['town2'] = '';
$array['theclub'] = '';
}





else
{
$k = $db->row("SELECT * FROM keywords WHERE id = :id",array("id"=>$p['keyword']));
$t = $db->row("SELECT * FROM towns WHERE id = :id",array("id"=>$p['town']));
$array['extrameta'] .= '<link rel="canonical" href="'.$array['rooturl']."/?page=".urlencode($p['phrase']).'"/>';

/////////////   INCREASE VIEWS
$views = $p['views']+1;
$db->query("UPDATE pages SET views = :t WHERE id = :id", array("t"=>$views,"id"=>$p['id']),PDO::FETCH_ASSOC,"n");

$phrase = str_replace(' '.$t['state'].' '.$t['country'].'','' ,$p['phrase']);


$p['phrase'] = $phrase;

//
$array['pagetitle'] = $p['phrase'].' &middot; '.$t['state'].', '.$t['country'].'';
$array['pagedescription'] = $p['phrase'].'. Join our free site in '.$t['suburb'].' - '.$t['state'].', '.$t['country'].'. '.$t['suburb'].' Members, Sex Groups, '.$t['suburb'].' Forum, Photo Galleries and More. '.$p['phrase'].'';
$array['breadcrumb'] = $short->breadcrumb($p['phrase'],$array['ogurl']);
//////
$array['title'] = $p['phrase'].' <span class="lightgrey">&middot;</span> '.$t['state'].', '.$t['country'].'';
$array['town'] = $t['suburb'];
$array['town2'] = ' in '.$t['suburb'];
$array['theclub'] = '<div class="space20"></div><h2>The Local Club</h2>Looking for a sex club in '.$t['suburb'].'?<div class="space10"></div><a href="../?club='.urlencode($t['suburb']).'&loc='.urlencode($t['state']).'-'.urlencode($t['country']).'"><span class="button width100">'.$t['suburb'].' Sex Club</span></a><div class="space10"></div>';
}












/// THE-USC REDIRECTS AND ADS
$searchtext = str_replace('USA','',$find);
$newdata = $db->row("SELECT *, MATCH (url) AGAINST (:p1) FROM x1_cities WHERE MATCH (url) AGAINST (:p2)", array("p1"=>$searchtext,"p2"=>$searchtext));
if($newdata['id'] > 0 && $newdata['name'] != 'Usa')
{
$nregion = $db->row("SELECT * FROM x1_regions WHERE id = :id",array("id"=>$newdata['region']));
$ncountry = $db->row("SELECT * FROM x1_countries WHERE id = :id",array("id"=>$newdata['country']));
/// REDIRECT IF CERTAIN TYPE OF PAGE
if (strpos(' '.$find, 'Sex Classifieds') !== false) {
header("HTTP/1.1 301 Moved Permanently");
header("Location: https://www.the-usc.com/r/".$nregion['url']."/personals/");
exit;
}
/// REDIRECT IF CERTAIN TYPE OF PAGE
if (strpos(' '.$find, 'Personal Ads') !== false) {
header("HTTP/1.1 301 Moved Permanently");
header("Location: https://www.the-usc.com/r/".$nregion['url']."/personals/");
exit;
}
/// REDIRECT IF CERTAIN TYPE OF PAGE
if (strpos(' '.$find, 'Backpage') !== false) {
header("HTTP/1.1 301 Moved Permanently");
header("Location: https://www.the-usc.com/r/".$nregion['url']."/personals/");
exit;
}
/// REDIRECT IF CERTAIN TYPE OF PAGE
if (strpos(' '.$find, 'Sex Groups') !== false) {
header("HTTP/1.1 301 Moved Permanently");
header("Location: https://www.the-usc.com/r/".$nregion['url']."/groups/");
exit;
}
/// REDIRECT IF CERTAIN TYPE OF PAGE
if (strpos(' '.$find, 'Meet Women') !== false) {
header("HTTP/1.1 301 Moved Permanently");
header("Location: https://www.the-usc.com/c/".strtolower($ncountry['code'])."/members/?gender=f&photo=y");
exit;
}
/// REDIRECT IF CERTAIN TYPE OF PAGE
if (strpos(' '.$find, 'Fuck Women') !== false) {
header("HTTP/1.1 301 Moved Permanently");
header("Location: https://www.the-usc.com/c/".strtolower($ncountry['code'])."/members/?gender=f&photo=y");
exit;
}
//  JUST SHOW LINK
$array['theclub'] .= '<a href="https://www.the-usc.com/t/'.$newdata['url'].'/"><span class="button width100">'.$newdata['name'].' Underground Club</span></a><div class="space10"></div>';
}




































///////// MORE IN THE SAME TOWN
$query = $db->query("SELECT * FROM keywords ORDER BY rand() LIMIT 5",null,PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
$x++;
$spacer = ($x!=1) ? '<div class="space-5"></div>': '';
$newp = ($data['position'] == 'f') ? ''.$data['keyword'].' '.$t['suburb'].' '.$t['state'].' '.$t['country'].'': ''.$t['suburb'].' '.$data['keyword'].' '.$t['state'].' '.$t['country'].'';
$newptext = ($data['position'] == 'f') ? ''.$data['keyword'].' '.$t['suburb'].'': ''.$t['suburb'].' '.$data['keyword'].'';
$array['moretown'] .= $spacer.'<span class="onelinetext"><span class="red fstyle1 fs22 m0">&rsaquo;</span> &nbsp; <a href="../?page='.urlencode($newp).'">'.$newptext.'</a></span>';
}


//////   PREV AND NEXT TOWNS
$prev = $db->row("SELECT * FROM towns WHERE id < :id ORDER BY id DESC LIMIT 1",array("id"=>$t['id']));
if($prev['id'] == 0)
{
$prev = $db->row("SELECT * FROM towns WHERE id != :id ORDER BY id DESC LIMIT 1",array("id"=>$t['id']));
}
$pr = ($k['position'] == 'f') ? ''.$k['keyword'].' '.$prev['suburb'].' '.$prev['state'].' '.$prev['country'].'': ''.$prev['suburb'].' '.$k['keyword'].' '.$prev['state'].' '.$prev['country'].'';
$prtext = ($k['position'] == 'f') ? ''.$k['keyword'].' '.$prev['suburb'].'': ''.$prev['suburb'].' '.$k['keyword'].'';
$array['prevt'] = '<span class="onelinetext"><span class="red fstyle1 fs22 m0">&rsaquo;</span> &nbsp; <a href="../?page='.urlencode($pr).'">'.$prtext.'</a></span>
';


$next = $db->row("SELECT * FROM towns WHERE id > :id ORDER BY id ASC LIMIT 1",array("id"=>$t['id']));
if($next['id'] == 0)
{
$next = $db->row("SELECT * FROM towns WHERE id != :id ORDER BY id ASC LIMIT 1",array("id"=>$t['id']));
}
$pr = ($k['position'] == 'f') ? ''.$k['keyword'].' '.$next['suburb'].' '.$next['state'].' '.$next['country'].'': ''.$next['suburb'].' '.$k['keyword'].' '.$next['state'].' '.$next['country'].'';
$prtext = ($k['position'] == 'f') ? ''.$k['keyword'].' '.$next['suburb'].'': ''.$next['suburb'].' '.$k['keyword'].'';
$array['nextt'] = '<span class="onelinetext"><span class="red fstyle1 fs22 m0">&rsaquo;</span> &nbsp; <a href="../?page='.urlencode($pr).'">'.$prtext.'</a></span>
';








///////////  ADDITIONAL PROFILES AT TOP
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
      <span class="minus2around"><a href="../?userinfo='.$data['id'].'"><img src="'.$staticurl.'/images/users/'.$data['id'].'.jpg" class="width100" alt=""/></a>
	  <div class="space5"></div>
	  '.$infobox.'
	  </span>
      </td>';
}
/// CLOSE CURRENT ROW
$array['fake'] .= '<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tbody>'.$fake.'</tr>
  </tbody>
</table><div class="space30"></div>';

}

//$array['fake'] = '';





/////////////////////////////////////////////   MAKE GIRLS RETURN GIRLS
$sexsql = "AND sex = 'Female' ";

if (strpos(' '.$findlower.' ', ' whores ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}
if (strpos(' '.$findlower.' ', ' girls ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}
if (strpos(' '.$findlower.' ', ' girl ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}
if (strpos(' '.$findlower.' ', ' women ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}
if (strpos(' '.$findlower.' ', ' woman ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}
if (strpos(' '.$findlower.' ', ' whore ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}
if (strpos(' '.$findlower.' ', ' bitch ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}
if (strpos(' '.$findlower.' ', ' tits ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}
if (strpos(' '.$findlower.' ', ' pussy ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}
if (strpos(' '.$findlower.' ', ' ladies ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}
if (strpos(' '.$findlower.' ', ' sluts ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}
if (strpos(' '.$findlower.' ', ' babes ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}
if (strpos(' '.$findlower.' ', ' prostitute ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}
if (strpos(' '.$findlower.' ', ' prostitutes ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}
if (strpos(' '.$findlower.' ', ' escorts ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}
if (strpos(' '.$findlower.' ', ' escort ') !== FALSE) {$girlsql = "AND sex = 'Female' ";}


if (strpos(' '.$findlower.' ', ' boys ') !== FALSE) {$sexsql = "AND sex = 'Male' ";}
if (strpos(' '.$findlower.' ', ' boy ') !== FALSE) {$sexsql = "AND sex = 'Male' ";}
if (strpos(' '.$findlower.' ', ' guy ') !== FALSE) {$sexsql = "AND sex = 'Male' ";}
if (strpos(' '.$findlower.' ', ' guys ') !== FALSE) {$sexsql = "AND sex = 'Male' ";}
if (strpos(' '.$findlower.' ', ' fellas ') !== FALSE) {$sexsql = "AND sex = 'Male' ";}
if (strpos(' '.$findlower.' ', ' fella ') !== FALSE) {$sexsql = "AND sex = 'Male' ";}
if (strpos(' '.$findlower.' ', ' male ') !== FALSE) {$sexsql = "AND sex = 'Male' ";}
////////  TRICK FOR MEN INSidE WOMEN
if (!strpos(' '.$findlower.' ', ' women ') !== FALSE && strpos(' '.$findlower.' ', ' men ') !== FALSE) {$sexsql = "AND sex = 'Male' ";}
if (!strpos(' '.$findlower.' ', ' woman ') !== FALSE && strpos(' '.$findlower.' ', ' man ') !== FALSE) {$sexsql = "AND sex = 'Male' ";}



///////////////////////////////////////////  DO THE QUERY FOR MEMBERS
$o1 = array(
'1' => 'id',
'2' => 'username',
'3' => 'email',
'4' => 'dob_date',
'5' => 'password',
'6' => 'usercode',
'7' => 'regdate'
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
$array['members'] .= '<tr>';
$rx = 0;
$query = $db->query("SELECT id,username,image, MATCH (country, town, sex,username,sex_relstatus, sex_pref) AGAINST ('".$find."') FROM members WHERE validated = 'y' AND image != '' $sexsql AND MATCH (country, town, sex,username,sex_relstatus, sex_pref) AGAINST ('".$find."') LIMIT $showing",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$tm++;
$rx++;
if($rx == 8)
{
$array['members'] .= '</tr><tr>';
$rx=1;
}
$array['members'] .= '<td class="one7th">
      <span class="minus2around"><a href="../?u='.$data['id'].'"><img src="'.$staticurl.'/images/members/'.$data['image'].'-thumb.jpg" class="width100" alt=""/></a></span>
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
$array['members'] .= '</tr><tr>';
$rx=1;
}
$array['members'] .= '<td class="one7th">
      <span class="minus2around"><a href="../?u='.$data['id'].'"><img src="'.$staticurl.'/images/members/'.$data['image'].'-thumb.jpg" class="width100" alt=""/></a></span>
      </td>';
}
}
/// CLOSE CURRENT ROW
$array['members'] .= '</tr>';







//// AD
$a = $db->row("SELECT * FROM ads WHERE type = :t AND active = 'y' ORDER BY rand() LIMIT 1",array("t"=>'content'));
$array['ad1'] = '<a href="'.$a['link'].$array['admobilelink'].'"><img src="'.$staticads.'/images/ads/'.$a['id'].'.jpg" width="100%" style="max-width:'.$a['display_x'].'" border="0"alt=""/></a><div class="space30"></div>
';












////////////////////////////  MATCH GROUPS
$tm = 0;
$query = $db->query("SELECT id, MATCH (title,slogan,description) AGAINST ('".$find."') FROM groups WHERE MATCH (title,slogan,description) AGAINST ('".$find."') LIMIT 12",null,PDO::FETCH_ASSOC,"n");
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
$query = $db->query("SELECT *, MATCH (title) AGAINST ('".$find."') FROM forumtopics WHERE MATCH (title) AGAINST ('".$find."') LIMIT 20",null,PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
$x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$forums .= $spacer.$short->forumtopic($data['id'],'result','n');
}

$array['forums'] = ($forums == '') ? '': '<div class="space30"></div><h2>'.$t['suburb'].' Forum Topics</h2><div class="space-10"></div>'.$forums.'';







//////////////////////////////////////  MATCH FEEDS
$x=0;
$query = $db->query("SELECT *, MATCH (body) AGAINST ('".$find."') FROM feed WHERE MATCH (body) AGAINST ('".$find."') LIMIT 20",null,PDO::FETCH_ASSOC,"n");
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















$x=0;
$phrase = $find;
$query = $db->query("SELECT url,type,title,description, MATCH (title, description) AGAINST ('".$phrase."') FROM search WHERE MATCH (title,description) AGAINST ('".$phrase."') LIMIT 20",array("ph"=>$phrase),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
if($data['type'] == 'member')
{
$id = str_replace('/?u=','',$data['url']);
$memberdata = $db->row("SELECT image FROM members WHERE id = :u",array("u"=>$id));
$icon = ($memberdata['image'] != '') ? '<img class="fleft" border="0" height="40" width="40" src="'.$staticurl.'/images/members/'.$memberdata['image'].'-thumb.jpg">': '<img class="fleft" border="0" height="40" width="40" src="'.$staticurl.'/images/default/'.$data['type'].'-thumb.jpg">';
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

$results .= $spacer.'
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




$array['other'] = ($results == '') ? '': '<div class="space30"></div><h2>'.$phrase.'</h2>'.$results.'';
