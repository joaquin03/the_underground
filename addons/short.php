<?php
class short
{
var $page,$db;


function contentad($mobile)
{
$ctype = ($mobile == '-mobile') ? 'content-m': 'content';
$mtype = ($mobile == '-mobile') ? '&m=y': '';
$a = $this->db->row("SELECT * FROM ads WHERE type = :t AND active = 'y' ORDER BY rand() LIMIT 1",array("t"=>$ctype));
$ad = '<a href="'.$a['link'].$mtype.'"><img src="'.$staticads.'/images/ads/'.$a['id'].'.'.$a['ext'].'" width="100%" style="max-width:'.$a['display_x'].'px" border="0"alt=""/></a>
';
return $ad;
}


function removepagenum($url)
{
$mainurl = parse_url($url);
$pageurl = $mainurl['scheme'].'://'.$mainurl['host'].$mainurl['path'];
parse_str($mainurl['query'], $params);
unset($params['pagenum']);
$par = (http_build_query($params) != '') ? '?'.http_build_query($params): '';
return $pageurl.$par;
}


function pagination($resultcount,$perpage,$currentpage,$url,$adj)
{
///  URL
$mainurl = parse_url($url);
$pageurl = $mainurl['scheme'].'://'.$mainurl['host'].$mainurl['path'];
parse_str($mainurl['query'], $params);
unset($params['pagenum']);
$nopageurl = (http_build_query($params) != '') ? $pageurl.'?'.http_build_query($params): $pageurl;
$params['pagenum'] = $currentpage;
$withpageurl = $pageurl.'?'.http_build_query($params);
$parname = 'pagenum';
$totalpages = ceil($resultcount/$perpage);
////
$plink = ($currentpage == 2) ? $nopageurl : str_replace('pagenum='.$currentpage,'pagenum='.($currentpage-1),$withpageurl) ;
$prev = ($currentpage > 1) ? '<a href="'.$plink.'"><span class="paginate inline rounded"><</span></a>': '';
$nlink = str_replace('pagenum='.$currentpage,'pagenum='.($currentpage+1),$withpageurl);
$next = ($currentpage < $totalpages) ? '<a href="'.$nlink.'"><span class="paginate inline">></span></a>': '';
/// CALC FIRST START PAGE
$pagebegin = ($currentpage > $adj) ? $currentpage-$adj: '1';
$pagebegin = ($totalpages > $currentpage+$adj) ? $pagebegin: $totalpages-$adj-$adj;
$pagebegin = ($pagebegin < 1) ? 1: $pagebegin;
/// CALC LAST PAGE TO GO TO
$pageend = ($totalpages > ($currentpage+$adj)) ? $currentpage+$adj : $totalpages;
$pageend = ($currentpage <= $adj) ? $adj+$adj+1 : $pageend;
$pageend = ($pageend > $totalpages) ? $totalpages : $pageend;
/// LOOP PAGES
for($i = $pagebegin; $i <= $pageend; $i++)
{
$plink = ($i==1) ? $nopageurl: str_replace('pagenum='.$currentpage,'pagenum='.$i,$withpageurl);
$pagelist .= ($i == $currentpage) ? '<span class="paginateactive inline">'.$i.'</span>': '<a href="'.$plink.'"><span class="paginate inline">'.$i.'</span></a>';
}

$data .= ($totalpages > 1) ? '<div class="paginatediv">'.$prev.$pagelist.$next.'</div>' : '';
return $data;
}







//////////   ELAPSED TIME
function zipfromip($ip)
{
$tags = json_decode(file_get_contents("http://api.ipinfodb.com/v3/ip-city/?key=f8ce120d2fd80ef6308947494ad80a2f1131344c944ba7ccda1f57327525137b&format=json&ip=".$ip),true);
$tags['zipCode'] = ($tags['zipCode'] == '-') ? '': $tags['zipCode'];
$zip = $tags['zipCode'].'|'.$tags['cityName'];
return $zip;
}








function replace_accents($var){
$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
$var= str_replace($a, $b,$var);
return $var;
}




//////   USERCODE FROM ID
function usercodefromid($id) {
$user = $this->db->row("SELECT usercode FROM members WHERE id = :u",array("u"=>$id));
return $user['usercode'];
}




//////////////////////////////////////////////////////////////////////////////////////////////////   ELAPSED TIME
function timeago($time) {
    $etime = time() - $time;

    if ($etime < 1) {
        return '0 seconds';
    }

    $a = array( 12 * 30 * 24 * 60 * 60  =>  'Year',
                30 * 24 * 60 * 60       =>  'Month',
                24 * 60 * 60            =>  'Day',
                60 * 60                 =>  'Hour',
                60                      =>  'Minute',
                1                       =>  'Second'
                );

    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . ' ' . $str . ($r > 1 ? 's' : '').' Ago';
        }
    }
}





//////////////////////////////////////////////////////////////////////////////////////////////////   ELAPSED TIME
function createusercode()
{
$success = 'n';
do {
/////////////////  CREATE A CODE
$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
$code = '';
for ($i = 0; $i < 10; $i++) {
    $code .= $characters[mt_rand(0, strlen($characters) - 1)];
}
$check = $this->db->query("SELECT usercode FROM members WHERE usercode = '{$code}' limit 1",null,PDO::FETCH_NUM,'y');
if($check == 0)
{
$success = 'y';
}
}
while ($success == 'n');
return $code;
}




/// NEW CODE
function createcode($length)
{
$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
$code = '';
for ($i = 0; $i < $length; $i++) {
    $code .= $characters[mt_rand(0, strlen($characters) - 1)];
}
return $code;
}






/////////////////////////////////////////////////////////////////////////////// ERROR MESSAGES
function message($message,$col)
{
if($col == 'g')
{
$pre = '&#10003;&nbsp;&nbsp;';
}
else if($col == 'r')
{
$pre = '&#10005;&nbsp;&nbsp;';
}
else
{
$pre = 'Warning:&nbsp;&nbsp;';
}
$data = '<script type="text/javascript">
setTimeout(function(){
            $(\'#messagediv\').slideUp();
        }, 5000);
</script><div id="messagediv" class="message-'.$col.'">'.$pre.$message.'</div>';
return $data;
}












function clean($string)
{
$string = trim($string);
$data = htmlspecialchars($string);
$data = filter_var($string, FILTER_SANITIZE_STRING);
return $data;
}








function bcitem($url,$name,$pos)
{
$data = '<span class="bcdiv">&rsaquo;</span><span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="inline">
<a href="'.$url.'" itemprop="item"><span itemprop="name">'.$name.'</span></a>
<meta itemprop="position" content="'.$pos.'" />
</span>
';
return $data;
}

















////////////////////////////////////  USER AGE
function age($date)
{
//date in yyyy-mm-dd for this function
//explode the date to get month, day and year
$birthDate = explode("-", $date);
//get age from date or birthdate
$age = (date("md", date("U", mktime(0, 0, 0, intval($birthDate[1]), intval($birthDate[2]), intval($birthDate[0])))) > date("md")
? ((date("Y") - intval($birthDate[0])) - 1)
: (date("Y") - intval($birthDate[0])));
return $age;
}









///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// PASSWORD
function password($password)
{
$sc = 'a45d678sgt4dop2';
$a = substr($sc, 0, 5);
$b = substr($sc, 5, 5);
$c = substr($sc, 9, 2);
$data = md5($a.$password,$b);
$data = sha1($data);
$data = $data.$c;
return $data;
}












//////////////////////////////////////////////////////////////////////////  SEND STANDARD EMAIL
function privatemessageemail($id)
{
$to = $this->db->row("SELECT username,usercode,email,email_pm FROM members WHERE id = :u",array("u"=>$id));
//
if($to['email_pm'] == 'y')
{
$email = $this->page->get_temp('/var/www/vhosts/theundergroundsexclub.com/httpdocs/templates/main/email_privatemessage_new-min.htm');
$email = str_replace("{username}", $to['username'], $email);
$email = str_replace("{usercode}", $to['usercode'], $email);
$email = str_replace("{email}", $to['email'], $email);
$lwomen .= file_get_contents('/var/www/vhosts/theundergroundsexclub.com/httpdocs/cachefiles/members/email-women.txt', FILE_USE_INCLUDE_PATH);
$lguys .= file_get_contents('/var/www/vhosts/theundergroundsexclub.com/httpdocs/cachefiles/members/email-men.txt', FILE_USE_INCLUDE_PATH);
$email = str_replace("{lwomen}", $lwomen, $email);
$email = str_replace("{lguys}", $lguys, $email);
$subject = 'New Private Message for '.$to['username'].'';
// TEXT ONLY EMAIL
$textonly = "Hello ".$to['username'].",\r\n\r\nYou have received a new private message.\r\nView Message: https://www.theundergroundsexclub.com/?mod=myhome&file=mail\r\n\r\nThankyou\r\n\r\nThe Underground Sex Club\r\n\r\n----------------------------------\r\n\r\nLogin: https://www.theundergroundsexclub.com/?mod=login\r\nRetrieve Login Details: https://www.theundergroundsexclub.com/?mod=login&file=forgot\r\n\r\n----------------------------------\r\n\r\nYou are receiving this because you are a registered member.\r\nDon't want to be notified of new message via email?\r\nUnsubscribe: https://www.theundergroundsexclub.com/?mod=unsubscribe&email=".$to['email']."&uc=".$to['usercode']."&type=privatemessage";
//
$this->db->query("INSERT INTO emailing(user,subject,body,textonly,priority) VALUES(:u,:s,:b,:t,1)", array("u"=>$id,"s"=>$subject,"b"=>$email,"t"=>$textonly),PDO::FETCH_ASSOC,"n");
$mailid = $this->db->lastInsertId();
return $mailid;
}

}











//////////////////////////////////////////////////////////////////////////  SEND STANDARD EMAIL
function forumemail($id,$byid)
{
/// GET ALL BOOKMARKS/SUBSCRIPTIONS
$query = $this->db->query("SELECT owner FROM forumbookmarks WHERE topic = :id",array("id"=>$id),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
//// CHECK PERMISSIONS
$check = $this->db->query("SELECT id FROM members WHERE email_forum = 'y' AND id = :id LIMIT 1",array("id"=>$data['owner']),PDO::FETCH_NUM,'y');
/// SEND IF PERMISSION ALLOWED AND POSTER ISNT THE RECEIVER
if($check == 1 && $byid != $data['owner'])
{
$topic = $this->db->row("SELECT title,id FROM forumtopics WHERE id = :id",array("id"=>$id));
$to = $this->db->row("SELECT username,usercode,email FROM members WHERE id = :u",array("u"=>$data['owner']));
$by = $this->db->row("SELECT username FROM members WHERE id = :u",array("u"=>$byid));
$email = $this->page->get_temp('/var/www/vhosts/theundergroundsexclub.com/httpdocs/templates/main/email_forum.htm');
$email = str_replace("{topicid}", $topic['id'], $email);
$email = str_replace("{topicname}", $topic['title'], $email);
$email = str_replace("{person}", $by['username'], $email);
$email = str_replace("{username}", $to['username'], $email);
$email = str_replace("{usercode}", $to['usercode'], $email);
$email = str_replace("{email}", $to['email'], $email);
$lwomen .= file_get_contents('/var/www/vhosts/theundergroundsexclub.com/httpdocs/cachefiles/members/email-women.txt', FILE_USE_INCLUDE_PATH);
$lguys .= file_get_contents('/var/www/vhosts/theundergroundsexclub.com/httpdocs/cachefiles/members/email-men.txt', FILE_USE_INCLUDE_PATH);
$email = str_replace("{lwomen}", $lwomen, $email);
$email = str_replace("{lguys}", $lguys, $email);
$subject = 'New Topic Post: '.$topic['title'].'';
// TEXT ONLY EMAIL
$textonly = "Hello ".$to['username'].",\r\n\r\n".$by['username']." posted to a forum topic you are subscribed to.\r\nTopic: ".$topic['title']."\r\n\r\nView: https://www.theundergroundsexclub.com/?f=".$topic['id']."\r\n\r\nThankyou\r\n\r\nThe Underground Sex Club\r\n\r\n----------------------------------\r\n\r\nLogin: https://www.theundergroundsexclub.com/?mod=login\r\nRetrieve Login Details: https://www.theundergroundsexclub.com/?mod=login&file=forgot\r\n\r\n----------------------------------\r\n\r\nYou are receiving this because you are a registered member, subscribed to this topic.\r\nDon't want to be notified of new posts for this topic?\r\nUnsubscribe from Topic: https://www.theundergroundsexclub.com/?mod=forum&file=unsubscribe&email=".$to['email']."&uc=".$to['usercode']."&topic=".$topic['id']."";
//
$this->db->query("INSERT INTO emailing(user,subject,body,textonly,priority) VALUES(:u,:s,:b,:t,1)", array("u"=>$data['owner'],"s"=>$subject,"b"=>$email,"t"=>$textonly),PDO::FETCH_ASSOC,"n");
}
}

}






////////////////////////////////////////////////////////////////////////////////////////////////////////////// USER DATA
function user($id,$display,$fullwidth)
{
$mdata = $this->db->row("SELECT username,id,image,sex,dob_date,country,count_galleries,count_groups,forumposts FROM members WHERE id = :u",array("u"=>$id));
$age = $this->age($mdata['dob_date']);
$shortsex = ($mdata['sex'] == 'Female') ? 'F': 'M';
/////////  PROCESS IMAGE
if($display == 'image')
{
$full = ($fullwidth == 'y') ? ' class="width100 mw103"': ' height="60" width="60"';
$image = ($mdata['image'] != '') ? '<img'.$full.' border="0" src="https://static.theundergroundsexclub.com/images/members/'.$mdata['image'].'-thumb.jpg">': '<img'.$full.' border="0" src="https://static.theundergroundsexclub.com/images/default/member-thumb.jpg">';
$data = '<a href="../?u='.$mdata['id'].'" title="'.$age.' '.$shortsex.'">'.$image.'</a>';
}

/////////  PROCESS FORUM
else if($display == 'forum')
{
$pplur = ($mdata['forumposts'] == 1) ? '' : 's';
$full = ($fullwidth == 'y') ? ' class="width100 mw103"': ' height="60" width="60"';
$image = ($mdata['image'] != '') ? '<img'.$full.' border="0" src="https://static.theundergroundsexclub.com/images/members/'.$mdata['image'].'-thumb.jpg">': '<img'.$full.' border="0" src="https://static.theundergroundsexclub.com/images/default/member-thumb.jpg">';
$data = '<div class="tright">
<a href="../?u='.$mdata['id'].'" title="'.$age.' '.$shortsex.'">'.$image.'</a>
<div class="space5"></div>
<span class="onelinetext"><a href="../?u='.$mdata['id'].'">'.$this->clean($mdata['username']).'</a></span>
<div class="space5"></div>
<span class="onelinetext">'.$mdata['forumposts'].' <span class="grey">Post'.$pplur.'</span></span>
</div>';
/// NO USER
$data = ($mdata['id'] == 0) ? '<span class="grey">Removed<br>User</span>': $data;
}


///// PROCESS RESULT
else if($display == 'result')
{
//// GALLERY
$plur = ($mdata['count_galleries'] == 1) ? 'y' : 'ies';
$galleryicon = ($mdata['count_galleries'] == 0) ? '' : '<a href="../?u='.$id.'&view=galleries" title="'.$mdata['count_galleries'].' Photo Galler'.$plur.'"><span class="is miniphoto"></span>'.$mdata['count_galleries'].'</a> &nbsp;&nbsp;';
//// GROUPS
$plur = ($mdata['count_groups'] == 1) ? '' : 's';
$groupicon = ($mdata['count_groups'] <= 0) ? '' : '<a href="../?u='.$id.'&view=groups" title="Member of '.$mdata['count_groups'].' Group'.$plur.'"><span class="is minigroup"></span>'.$mdata['count_groups'].'</a> &nbsp;&nbsp;';
/// FORUM POSTS
$forumtopics = $this->db->query("SELECT id FROM forumtopics WHERE addedby = $id",null,PDO::FETCH_NUM,'y');
$plur = ($forumtopics == 1) ? '' : 's';
$forumicon = ($forumtopics == 0) ? '' : '<a href="../?mod=forum&file=category&uid='.$id.'" title="'.$forumtopics.' Forum Topic'.$plur.'"><span class="is miniforum"></span>'.$forumtopics.'</a> &nbsp;&nbsp;';
/// STORIES
$stories = $this->db->query("SELECT id FROM stories WHERE owner = $id",null,PDO::FETCH_NUM,'y');
$plur = ($stories == 1) ? 'y' : 'ies';
$storyicon = ($stories == 0) ? '' : '<a href="../?mod=stories&file=category&uid='.$id.'" title="'.$stories.' Sex Stor'.$plur.'"><span class="is ministory"></span>'.$stories.'</a> &nbsp;&nbsp;';
//  PERSONALS
$personals = $this->db->query("SELECT id FROM classifieds WHERE owner = $id AND title != '' AND delstamp = 0",null,PDO::FETCH_NUM,'y');
$plur = ($personals == 1) ? '' : 's';
$adicon = ($personals == 0) ? '' : '<a href="../?u='.$id.'&view=personals" title="'.$personals.' Personal Ad'.$plur.'"><span class="is miniad"></span>'.$personals.'</a> &nbsp;&nbsp;';

//
$textline = ($mdata['id'] == '100') ? 'Admin Account': ''.$mdata['sex'].' &middot; '.$age.' &middot; '.$mdata['country'].'';
//
$image = ($mdata['image'] != '') ? '<img class="fleft" border="0" height="60" width="60" src="https://static.theundergroundsexclub.com/images/members/'.$mdata['image'].'-thumb.jpg">': '<img class="fleft" border="0" height="60" width="60" src="https://static.theundergroundsexclub.com/images/default/member-thumb.jpg">';
$data = '<a href="../?u='.$mdata['id'].'" title="'.$age.' '.$shortsex.'">'.$image.'</a>
<div class="disp70">
<a href="../?u='.$mdata['id'].'" title="'.$age.' '.$shortsex.'">'.$this->clean($mdata['username']).'</a>
<div class="space1"></div>
'.$textline.'
<div class="space5"></div>
'.$galleryicon.$groupicon.$forumicon.$storyicon.$adicon.'
</div>
<div class="clear"></div>';
}




/////  PROCESS TEXT ONLY
else
{
$data = '<a href="../?u='.$mdata['id'].'" title="'.$age.' '.$shortsex.'">'.$this->clean($mdata['username']).'</a>';
/// NO USER
$data = ($mdata['id'] == 0) ? '<span class="grey">Removed User</span>': $data;
}

return $data;
}














////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// GROUP DATA
function group($id,$display)
{
$group = $this->db->row("SELECT * FROM `groups` WHERE id = :u",array("u"=>$id));
$cat = $this->db->row("SELECT * FROM groupcategories WHERE id = :u",array("u"=>$group['catid']));

$gplur = ($group['members'] == 1) ? '': 's';

$image = ($group['image'] != '') ? 'https://static.theundergroundsexclub.com/images/groups/'.$group['image'].'-thumb.jpg': 'https://static.theundergroundsexclub.com/images/default/group-thumb.jpg';

///// PROCESS RESULT
if($display == 'result')
{
/// FORUM POSTS
$forumtopics = $this->db->query("SELECT id FROM forumtopics WHERE `group` = $id",null,PDO::FETCH_NUM,'y');
$plur = ($forumtopics == 1) ? '' : 's';
$forumicon = ($forumtopics == 0) ? '' : '<a href="../?mod=forum&file=category&gid='.$id.'" title="'.$forumtopics.' Forum Topic'.$plur.'"><span class="is miniforum"></span> '.$forumtopics.'</a> &nbsp;&nbsp;&nbsp;&nbsp;';
/// GALLERIES
$galleries = $this->db->query("SELECT id FROM galleries WHERE `group` = $id",null,PDO::FETCH_NUM,'y');
$plur = ($galleries == 1) ? 'y' : 'ies';
$galleryicon = ($galleries == 0) ? '' : '<a href="../?g='.$id.'&view=galleries" title="'.$galleries.' Photo Galler'.$plur.'"><span class="is miniphoto"></span> '.$galleries.'</a> &nbsp;&nbsp;&nbsp;&nbsp;';


$data = '<a href="../?g='.$group['id'].'" title="'.$this->clean($group['title']).'"><img class="fleft" border="0" height="60" width="60" src="'.$image.'"></a>
<div class="disp70">
<span class="onelinetext"><a href="../?g='.$group['id'].'" title="'.$this->clean($group['title']).'">'.$this->clean($group['title']).'</a></span>
<div class="space1"></div>
<span class="onelinetext">Category: <a href="../?mod=groups&cat='.$cat['id'].'">'.$cat['title'].'</a></span>
<div class="space5"></div>
<a href="../?g='.$id.'&view=members" title="'.$group['members'].' Member'.$gplur.'"><span class="is minigroup"></span>'.$group['members'].'</a>&nbsp;&nbsp;&nbsp;&nbsp;'.$forumicon.$galleryicon.'
</div>
<div class="clear"></div>';
}


/////  PROCESS TEXT ONLY
else
{
$data = '<a href="../?g='.$group['id'].'">'.$this->clean($group['title']).'</a>';
/// NO USER
$data = ($group['id'] == 0) ? '<span class="grey">Group Unknown</span>': $data;
}
return $data;
}















////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// GROUP DATA
function gallery($id,$type,$showtitle)
{
$gall = $this->db->row("SELECT * FROM galleries WHERE id = :id",array("id"=>$id));


if($type == 'text')
{
$data = '<a href="../?p='.$gall['id'].'">'.$gall['title'].'</a>';
}
else
{
///  NUMBER
$num = ($type == 'small') ? '4': '7';

$user = $this->db->row("SELECT id FROM members WHERE id = :id",array("id"=>$gall['owner']));
$by = $this->user($gall['owner'],'text','n');

////
$query = $this->db->query("SELECT id,image FROM galleryimages WHERE gallery = :id",array("id"=>$id),PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
if($x < ($num+1))
{
$p = 'https://www.theundergroundsexclub.com/images/galleries/'.$id.'/'.$data['image'].'-thumb.jpg';
$photos .= '<td class="one'.$num.'th">
      <a href="../?i='.$data['id'].'"><img src="'.$p.'" class="maxwidth100 mw103" alt=""/></a>
      </td>';
}
}

$plur = ($x==1) ? '': 's';
//// TITLE
$title = ($showtitle == 'n') ? '': '<a href="../?p='.$gall['id'].'"><span class="onelinetext">'.$gall['title'].'</span></a>
<div class="space5"></div>
<span class="onelinetext">'.$x.' Photo'.$plur.' by '.$by.'</span></span>
<div class="space10"></div>';

$data = ''.$title.'
<div class="m-1">
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tbody>
    <tr>
      '.$photos.'
    </tr>
  </tbody>
</table>
</div>
';
}
return $data;
}











////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// PHOTO
function photo($id,$size)
{
$photo = $this->db->row("SELECT * FROM galleryimages WHERE id = :id",array("id"=>$id));
if($photo['id'] > 0)
{
$data = '<a href="../?i='.$id.'"><img src="https://www.theundergroundsexclub.com/images/galleries/'.$photo['gallery'].'/'.$photo['image'].'-thumb.jpg" width="'.$size.'" alt=""/></a>';
}
return $data;
}







function allowedemail($email)
{
$not1 = 'yopmail';
$not2 = 'guerrillamail';
$not3 = 'thraml';
$not4 = 'mailinator';
$not5 = 'mvrht';
$not6 = '20email';
$not7 = '20mail';
$not8 = 'fakeinbox';
$not9 = 'meltmail';
$not10 = '10minutemail';
$not11 = 'anonbox';
$not12 = 'dispostable';
$not13 = 'superrito';
$not14 = 'armyspy';
$not15 = 'cuvox';
$not16 = 'dayrep';
$not17 = 'einrot';
$not18 = 'fleckens';
$not19 = 'gustr.com';
$not20 = 'jourrapide';
$not21 = 'rhyta.com';
$not22 = 'teleworm.us';
$not23 = 'lazyinbox';
$not24 = 'mailcatch';
$not25 = 'mailforspam';
$not26 = 'squizzy.net';
$not27 = 'emarketeerz';
$data = (strpos($email, $not1) !== false ||
strpos($email, $not2) !== false ||
strpos($email, $not3) !== false ||
strpos($email, $not4) !== false ||
strpos($email, $not5) !== false ||
strpos($email, $not6) !== false ||
strpos($email, $not7) !== false ||
strpos($email, $not8) !== false ||
strpos($email, $not9) !== false ||
strpos($email, $not10) !== false ||
strpos($email, $not11) !== false ||
strpos($email, $not12) !== false ||
strpos($email, $not13) !== false ||
strpos($email, $not14) !== false ||
strpos($email, $not15) !== false ||
strpos($email, $not16) !== false ||
strpos($email, $not17) !== false ||
strpos($email, $not18) !== false ||
strpos($email, $not19) !== false ||
strpos($email, $not20) !== false ||
strpos($email, $not21) !== false ||
strpos($email, $not22) !== false ||
strpos($email, $not23) !== false ||
strpos($email, $not24) !== false ||
strpos($email, $not25) !== false ||
strpos($email, $not26) !== false ||
strpos($email, $not27) !== false) ? 'n': 'y';

return $data;
}




////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// STORY
function story($id,$display,$showdate)
{
$story = $this->db->row("SELECT * FROM stories WHERE id = :id",array("id"=>$id));
if($story['id'] > 0)
{
if($display == 'result')
{
$date = ($showdate == 'n') ? '': ' &nbsp; <span class="lightgrey">'.$this->timeago($story['stamp']).'</span>';
$likeplur = ($story['votesup'] == '1') ? '': 's';
$viewplur = ($story['views'] == '1') ? '': 's';
$data = '<div class="forumright fright">
'.number_format($story['views']).' <span class="grey">View'.$viesplur.'</span>
<div class="space5"></div>
'.number_format($story['votesup']).' <span class="grey">Like'.$likeplur.'</span>
</div>
<div class="forumleft">
<span class="onelinetext"><a href="../?s='.$story['id'].'">'.$this->clean($story['title']).'</a></span>
<div class="space5"></div>
<span class="onelinetext">By: '.$this->user($story['owner'],'text','n').''.$date.'</span>
</div>
<div class="clear"></div>';
}
else if($display == 'text')
{
$data = '<a href="../?s='.$id.'">'.$this->clean($story['title']).'</a>';
}
}
return $data;
}



















////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// USER DATA
function forumtopic($id,$display,$showdate)
{
$data = $this->db->row("SELECT * FROM forumtopics WHERE id = :id",array("id"=>$id));

if($display == 'text')
{
$data = '<a href="../?f='.$id.'">'.$this->clean($data['title']).'</a>';
}
else
{
$postplur = ($data['posts'] == '1') ? '': 's';
$viewplur = ($data['views'] == '1') ? '': 's';
$date = ($showdate == 'n') ? '': ' &nbsp; <span class="lightgrey">Active '.$this->timeago($data['lastpost']).'</span>';

$data = '<div class="forumright fright">
'.number_format($data['posts']).'&nbsp;<span class="grey">Post'.$postplur.'</span>
<div class="space5"></div>
'.number_format($data['views']).'&nbsp;<span class="grey">View'.$viewplur.'</span>
</div>
<div class="forumleft">
<span class="onelinetext"><a href="../?f='.$id.'">'.$this->clean($data['title']).'</a></span>
<div class="space5"></div>
<span class="onelinetext">by: '.$this->user($data['addedby'],'text','n').$date.'</span>
</div>
<div class="clear"></div>';
}

return $data;
}









////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// USER DATA
function forumpost($id,$showdate)
{
$data = $this->db->row("SELECT * FROM forumposts WHERE id = :id",array("id"=>$id));
$topic = $this->db->row("SELECT * FROM forumtopics WHERE id = :id",array("id"=>$data['topic']));

$postplur = ($data['posts'] == '1') ? '': 's';
$viewplur = ($data['views'] == '1') ? '': 's';
$buttonclass = ($data['original'] == 'y') ? 'tagsbutton': 'smallgreybutton';
//// IMAGE
$image = ($data['image'] != '') ? '<div class="space20"></div><img style="max-width:100%;" src="https://www.theundergroundsexclub.com/images/forum/'.$data['image'].'.jpg"/>': '';

////  QUOTE
if($data['quoteid'] != 0)
{
$qdata = $this->db->row("SELECT image,body,addedby,id FROM forumposts WHERE id = :id",array("id"=>$data['quoteid']));
$qimage = ($qdata['image'] != '') ? '<div class="space10"></div><img height="100" src="https://www.theundergroundsexclub.com/images/forum/'.$qdata['image'].'-thumb.jpg"/>': '';
$quote = '<div class="quotebox"><span class="lightgrey">Quoting </span>'.$this->user($qdata['addedby'],'text','n').'<span class="lightgrey">: </span><span class="italic">'.nl2br($this->clean($qdata['body'])).'</span>'.$qimage.'</div>';
}


//// REPLY BUTTON
$replylink = (isset($_SESSION['userid'])) ? '../?mod=forum&file=reply&id='.$topic['id'].'': '..?mod=register';
$menu .= ($data['original'] == 'y') ? '<a href="'.$replylink.'" alt="Reply" title="Reply"><span class="'.$buttonclass.'">Reply</span></a>': '';

//////   SUBSCRIBE BUTTON
if(isset($_SESSION['userid']) && $data['original'] == 'y')
{
$check = $this->db->query("SELECT id FROM forumbookmarks WHERE owner = :u AND topic = :t limit 1",array("u"=>$_SESSION['userid'],"t"=>$topic['id']),PDO::FETCH_NUM,'y');
$menu .= ($check == 1) ? '<a href="../phpfiles/forumactions.php?unsubscribe='.$topic['id'].'" alt="Delete Post" title="Delete Post"><span class="'.$buttonclass.'">Unsubscribe</span></a>': '<a href="../phpfiles/forumactions.php?subscribe='.$topic['id'].'" alt="Delete Post" title="Delete Post"><span class="'.$buttonclass.'">Subscribe</span></a>';
}

////  QUOTE BUTTON
$quotlink = (isset($_SESSION['userid'])) ? '../?mod=forum&file=reply&id='.$topic['id'].'&quote='.$data['id'].'': '..?mod=register';
$menu .= '<a href="'.$quotlink.'" alt="Reply With Quote" title="Reply With Quote"><span class="'.$buttonclass.'">Quote</span></a>';

/// DELETE BUTTON
$menu .= (($data['addedby'] == $_SESSION['userid'] || $_SESSION['userid'] == '100') && isset($_SESSION['userid'])) ? '<a href="../phpfiles/forumactions.php?delpost='.$data['id'].'" alt="Delete Post" title="Delete Post"><span class="'.$buttonclass.'">X</span></a>': '';

////  EDIT BUTTON
$menu .= ($_SESSION['userid'] == '100') ? '<a href="../?mod=forum&file=edit&id='.$data['id'].'" alt="Edit Post" title="Edit Post"><span class="'.$buttonclass.'">E</span></a>': '';

///
$data = '<div class="forumright fright">
'.$this->user($data['addedby'],'forum','n').'
</div>
<div class="forumleft b0">
'.$reply.$quote.nl2br($this->clean($data['body'])).$image.'
<div class="space10"></div>
<span class="lightgrey">Posted: '.$this->timeago($data['added']).'</span>
<div class="space10"></div>
'.$menu.'
<div class="space-5"></div>
</div>
<div class="clear"></div>';


return $data;
}











////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// STORY
function personal($id,$display,$showdate)
{
$personal = $this->db->row("SELECT * FROM classifieds WHERE id = :id",array("id"=>$id));
if($personal['id'] > 0)
{
if($display == 'result')
{
$cat = $this->db->row("SELECT * FROM classifieds_categories WHERE id = :id",array("id"=>$personal['category']));
//
$country = $this->db->row("SELECT * FROM loc_countries WHERE id = :id",array("id"=>$personal['country']));
//
$date = ($showdate == 'n') ? '': ' &nbsp; Added: <span class="lightgrey">'.$this->timeago($personal['stamp']).'</span>';
//
$image = ($personal['image'] != '') ? '<a href="../?a='.$personal['id'].'" title="'.$this->clean($personal['title']).'"><img class="fleft" border="0" height="60" width="60" src="https://www.theundergroundsexclub.com/images/personals/'.$personal['image'].'-thumb.jpg"></a>': '';

$class = ($personal['image'] == '') ? '': 'disp70';

$data = ''.$image.'
<div class="'.$class.'">
<span class="onelinetext"><a href="../?a='.$personal['id'].'" title="'.$this->clean($personal['title']).'">'.$this->clean($personal['title']).'</a></span>
<div class="space5"></div>
<span class="onelinetext">Category: <a href="../?mod=personals&cat='.$cat['id'].'">'.$cat['title'].'</a></span>
<div class="space5"></div>
<span class="onelinetext">Country: <a href="../?mod=personals&co='.$country['id'].'">'.$country['code'].'</a>'.$date.'</span>
</div>
<div class="clear"></div>';
}
else if($display == 'text')
{
$data = '<a href="../?a='.$id.'">'.$this->clean($personal['title']).'</a>';
}
}
return $data;
}









////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// USER DATA
function website($id)
{
$website = $this->db->row("SELECT * FROM websites WHERE id = :id",array("id"=>$id));
if($website['title'] != '')
{
/// VidEO
if($website['video'] != '')
{
list($brand,$vid) = explode("|", $website['video']);
/// XVidEOS
if($brand == 'xvideos' && $vid != '')
{
$video = '<div class="vidholder"><div class="vidbox"><iframe src="http://flashservice.xvideos.com/embedframe/'.$vid.'" frameborder="0" wscrolling="no" class="vidiframe"></iframe></div></div>
<div class="divline"></div>';
}

}
///
$image = ($website['image'] != '') ? '<a href="../?w='.$id.'"><img class="fleft" border="0" height="60" width="60" src="https://www.theundergroundsexclub.com/images/websites/'.$website['image'].'-thumb.jpg"></a>': '';
$class = ($website['image'] == '') ? '': 'disp70';

$data = $video.''.$image.'
<div class="'.$class.'">
<span class="onelinetext"><a href="../?w='.$id.'">'.$website['title'].'</a></span>
<div class="space5"></div>
<span class="onelinetext">'.$website['description'].'</span>
<div class="space5"></div>
<span class="onelinetext"><a href="../?w='.$id.'">'.$this->clean($website['url']).'</a></span>
</div>
<div class="clear"></div>';
}



return $data;
}



////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// USER DATA
function feed($id,$display)
{
$feed = $this->db->row("SELECT * FROM feed WHERE id = :id",array("id"=>$id));
if($display == 'linktext')
{
$trimtitle = mb_strimwidth($this->clean($feed['body']), 0, 100, "...");
$data = '<a href="../?item='.$id.'">'.$trimtitle.'</a>';
}

else
{
if($feed['websiteid'] > 0)
{
$website = $this->db->row("SELECT * FROM websites WHERE id = :id",array("id"=>$feed['websiteid']));
}
$web = ($website['title'] != '') ? '<div class="divline"></div>'.$this->website($feed['websiteid']).'' : '';
///  DELETE BUTTON
$del = ($feed['owner'] == $_SESSION['userid'] || $_SESSION['userid'] == '100') ? '<div class="space10"></div><a href="../phpfiles/actions.php?delpost='.$feed['id'].'"><span class="fs10">Delete Post</span></a>':'' ;

$data = '<a href="../?item='.$id.'"><span class="black">'.nl2br($this->clean($feed['body'])).'</span>'.$del.'</a>'.$web.'<div class="space10"></div>'.$this->interactbar('feed',$feed['id'],'y');
}

return $data;
}










////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// INTERACT BAR
function interactbar($type,$id,$showcomments)
{
$votes = $this->db->query("SELECT id FROM votes WHERE itemid = {$id} AND type = '{$type}'",null,PDO::FETCH_NUM,'y');
//// COMMENTS
$cquery = $this->db->query("SELECT * FROM comments WHERE type = '{$type}' and itemid = {$id} ORDER BY id DESC",null,PDO::FETCH_ASSOC,"n");
$cc = 0;
foreach($cquery as $cdata)
{
$delete = ($_SESSION['userid'] == 100 || $cdata['owner'] == $_SESSION['userid'] || ($cdata['itemid'] == $_SESSION['userid'] && $cdata['type'] == 'member')) ? ' &middot; <a href="../phpfiles/actions.php?delcomment='.$cdata['id'].'">Delete</a>': '';
$cc++;
$cdiv = ($cc != 1) ? '<div class="divline"></div>': '<div class="space10"></div>';
$commentlist .= ''.$cdiv.'
<div class="fleft inline">
'.$this->user($cdata['owner'],'image','n').'
</div>
<div class="disp70">
'.$this->user($cdata['owner'],'text','n').': '.$this->clean($cdata['body']).'
<div class="space5"></div>
<span class="grey fs10">'.$this->timeago($cdata['stamp']).''.$delete.'</span>
</div>
<div class="clear"></div>';
}
$commentlist .= ($commentlist == '') ? '': '<div class="space20"></div>';

if(isset($_SESSION['userid']))
{
$uservote = $this->db->row("SELECT id FROM votes WHERE type = '{$type}' and itemid = {$id} and owner = :o",array("o"=>$_SESSION['userid']));
//mail('ourteam@theundergroundsexclub.com', $id.' - '.$type, "", "From:ourteam@theundergroundsexclub.com");
if($uservote['id'] > 0)/// ALREADY SMILED
{
$starbutton = '<span title="Already Liked" class="is star2"></span>';
}
else/// LOGGED IN, BUT NO SMILE OR WINK
{
$starbutton = '<div class="pointer" onclick="vote('.$id.',\''.$type.'\');"><span class="is star"></span></div>';
}
}
else /// NOT LOGGED IN
{
$starbutton = '<a href="../?mod=register"><span class="is star"></span></a>';
}

if($showcomments != 'n')
{
$commentfield = '<div id="commentsection">
<input name="commentbutton" type="submit" class="commentbutton" id="commentbutton" value="Post"  onclick="comment('.$id.',\''.$type.'\',document.getElementById(\'comment'.$id.'\').value);ClearComment(\'comment'.$id.'\');"/>
<div class="commentarea"><input id="comment'.$id.'" name="comment'.$id.'" type="text" class="commentfield" placeholder="Write a Comment"/></div>
</div>';
}
else
{
$commentfield = '';
$commentlist = '';
}

$data .= '<div id="interact"><div id="votesection" class="oswald700 fs16"><span id="'.$type.'-'.$id.'">
<div id="votebuttonbox">'.$starbutton.number_format($votes).'</div>
</span>
<div id="votebuttonbox"><span class="is comment"></span><span class="cnum'.$id.'">'.number_format($cc).'</span></div>
<div class="clear"></div>
</div>
'.$commentfield.'
<div class="clear"></div>
</div>
<span id="'.$type.'-'.$id.'-comment">'.$commentlist.'</span>';


  return $data;
}















///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function postnotify($owner,$who,$type,$dbtype,$itemid)
{
if($owner != $who)
{
/// DATABASE SWITCHER FROM NOTIFY TYPE TO PERMISSIONS - FIRST IS NOTIFY TYPE THEN LOOKUP CODE -forumreply myforumreply storycomments
$dbcode = array(
'follow' => 'notify_follow',
'membercomments' => 'notify_commentsme',
'gallerycomments' => 'notify_commentsgallery',
'photocomments' => 'notify_commentsphoto',
'storycomments' => 'notify_commentsstory',
'feedcomments' => 'notify_commentsfeed',
'membervote' => 'notify_votesmember',
'galleryvote' => 'notify_votesgallery',
'photovote' => 'notify_votesphoto',
'storyvote' => 'notify_votesstory',
'feedvote' => 'notify_votesfeed',
'joinmygroup' => 'notify_joinmygroup',
'groupcomment' => 'notify_groupcomments'
);
//
$dbtable = $dbcode[$type];
///  MARK ENTRY OK UNTIL PROVEN OTHERWISE
$ok = 'y';

////   IF THERE IS A DATABASE MATCH ABOVE, THEN CHECK USER ALLOWS IT TO BE SENT
if($dbtable != '')
{
////  CHECKING IF USER ALLOWS NOTIFICATIONS
$check = $this->db->query("SELECT id FROM members WHERE id = :u AND $dbtable = :v",array("u"=>$owner,"v"=>'y'),PDO::FETCH_NUM,'y');
///  MARK AS NO IF NOT ALLOWED
$ok = ($check == 0) ? 'n': 'y';
}
//// POST NOTIFY IF ALL OK
if($ok == 'y')
{
$time = time();
$itemsql = ($itemid > 0) ? 'AND itemid = '.$itemid.'' : '';
$code = ($itemid == 0) ? $type: ''.$type.'-'.$itemid.'';
//
$this->db->query("INSERT INTO notifications(owner,who,type,itemtype,itemid,code,stamp) VALUES(:o,:w,:type,:d,:iid,:c,:t)",
array("o"=>$owner,"w"=>$who,"type"=>$type,"d"=>$dbtype,"iid"=>$itemid,"c"=>$code,"t"=>$time),PDO::FETCH_ASSOC,"n");
}// END OK

}// END OWNER IS NOT WHO
}














function stringencoder($string)
{

$search = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9','0');
$replacement = array('H','O','a','T','b','L','d','f','h','M','i','j','K','k','3','l','U','m','n','R','o','p','B','q','r','t','u','v','8','w','D','y','x','A','C','E','F','0','G','I','J','N','P','Q','V','W','s','2','X','Y','Z','1','4','g','5','z','6','S','7','e','9','c');

$replace = array_combine($search, $replacement);

$string = strtr($string, $replace);

$string = str_replace(' ','TiAS',$string);
$string = str_replace('*','S7ARRR',$string);
$string = str_replace('=','QQ3N',$string);
$string = str_replace('>','G8TN',$string);
$string = str_replace("'",'AP11',$string);

return $string;
}



function stringdecoder($string)
{
$string = str_replace('AP11',"'",$string);
$string = str_replace('G8TN','>',$string);
$string = str_replace('QQ3N','=',$string);
$string = str_replace('S7ARRR','*',$string);
$string = str_replace('TiAS',' ',$string);

$replacement = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9','0');
$search = array('H','O','a','T','b','L','d','f','h','M','i','j','K','k','3','l','U','m','n','R','o','p','B','q','r','t','u','v','8','w','D','y','x','A','C','E','F','0','G','I','J','N','P','Q','V','W','s','2','X','Y','Z','1','4','g','5','z','6','S','7','e','9','c');

$replace = array_combine($search, $replacement);


$string = strtr($string, $replace);


return $string;
}











function deletegalleryimage($id)
{
$theimage = $this->db->row("SELECT id,image FROM galleryimages WHERE id = :id", array("id"=>$id));
if($theimage['id'] > 0)
{
$serverpath = '/var/www/vhosts/theundergroundsexclub.com/httpdocs';
$thegallery = $this->db->row("SELECT id,owner FROM galleries WHERE id = :id",array("id"=>$theimage['gallery']));
/// REMOVE IMAGES
@unlink($serverpath.'/images/galleries/'.$thegallery['id'].'/'.$theimage['image'].'-original.jpg');
@unlink($serverpath.'/images/galleries/'.$thegallery['id'].'/'.$theimage['image'].'.jpg');
@unlink($serverpath.'/images/galleries/'.$thegallery['id'].'/'.$theimage['image'].'-thumb.jpg');

/// DELETE DATABASE ENTRY
$this->db->query("DELETE FROM galleryimages WHERE id = :id", array("id"=>$theimage['id']),PDO::FETCH_ASSOC,"n");
//// DELETE NOTIFICATIONS
$this->db->query("DELETE FROM notifications WHERE itemid = :id AND itemtype = 'photo'", array("id"=>$theimage['id']),PDO::FETCH_ASSOC,"n");
//// DELETE COMMENTS
$this->db->query("DELETE FROM comments WHERE itemid = :id AND type = 'photo'", array("id"=>$theimage['id']),PDO::FETCH_ASSOC,"n");
//// DELETE VOTES
$this->db->query("DELETE FROM votes WHERE itemid = :id AND type = 'photo'", array("id"=>$theimage['id']),PDO::FETCH_ASSOC,"n");
//// DELETE  NEWS
$this->db->query("DELETE FROM news WHERE itemid = :id AND type = 'photo'", array("id"=>$theimage['id']),PDO::FETCH_ASSOC,"n");

//// CHECK IF ONLY PIC
$count = $this->db->query("SELECT id FROM galleryimages WHERE gallery = :g",array("g"=>$theimage['gallery']),PDO::FETCH_NUM,'y');
if($count == 0)
{
///////  MARK GALLERY AS INCOMPLETED
$this->db->query("DELETE FROM galleries WHERE id = :id", array("id"=>$thegallery['id']),PDO::FETCH_ASSOC,"n");
//////////// UPDATE USER COUNT
$count=$this->db->query("SELECT id FROM galleries WHERE owner = :u AND completed = 'y'",array("u"=>$thegallery['owner']),PDO::FETCH_NUM,'y');
$this->db->query("UPDATE members SET count_galleries = :p WHERE id = :id", array("p"=>$count,"id"=>$thegallery['owner']),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY NOTIFICATIONS
$this->db->query("DELETE FROM notifications WHERE itemid = :id AND itemtype = 'gallery'", array("id"=>$thegallery['id']),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY COMMENTS
$this->db->query("DELETE FROM comments WHERE itemid = :id AND type = 'gallery'", array("id"=>$thegallery['id']),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY VOTES
$this->db->query("DELETE FROM votes WHERE itemid = :id AND type = 'gallery'", array("id"=>$thegallery['id']),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY NEWS
$this->db->query("DELETE FROM news WHERE itemid = :id AND type = 'gallery'", array("id"=>$thegallery['id']),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY SEARCH
$url = '/?g='.$thegallery['id'];
$this->db->query("DELETE FROM search WHERE url = :url", array("url"=>$url),PDO::FETCH_ASSOC,"n");
}
}
}

















function deletemember($id,$blacklisted)
{
$user = $this->db->row("SELECT * FROM members WHERE id = :id",array("id"=>$id));
/// ADD TO BLACK LIST
if($blacklisted == 'y' && $user['email'] != '')
{
/// REMOVE DOTS
list($localemail,$domainemail) = explode("@", $user['email']);
$domainemail = strtolower($domainemail);
$localemail = str_replace('.', '', $localemail);
$banemail = $localemail.'@'.$domainemail;
///  INSERT RECORD
$this->db->query("INSERT INTO s_blacklist(email) VALUES(:e)", array("e"=>$banemail),PDO::FETCH_ASSOC,"n");
}
//////  BLACKLIST IP ADDRESS
if($blacklisted == 'y' && $user['regip'] != '')
{
$check = $this->db->query("SELECT id FROM s_ipblacklist WHERE ip = :ip AND type = 'ip' limit 1",array("ip"=>$user['regip']),PDO::FETCH_NUM,'y');
if($check == 0)
{
/// INSERT THE BAN
$insert = $this->db->query("INSERT INTO s_ipblacklist(type,ip) VALUES(:t,:ip)", array("t"=>'ip',"ip"=>$user['regip']), PDO::FETCH_ASSOC,"n");
///  TELL SYSTEM TO MAKE NEW HTACCESS FILES FOR EACH SITE
$this->db->query("UPDATE s_admin SET value = 'y' WHERE name = 'create_htaccess'", null,PDO::FETCH_ASSOC,"n");
$this->db->query("DELETE FROM s_visitors WHERE ip = :ip", array("ip"=>$user['regip']),PDO::FETCH_ASSOC,"n");
}
}


//  REMOVE BLOCKS
$this->db->query("DELETE FROM blocks WHERE owner = :id OR who = :id2", array("id"=>$id,"id2"=>$id),PDO::FETCH_ASSOC,"n");


/// DELETE CLASSIFIED ADS
$aquery = $this->db->query("SELECT * FROM classifieds WHERE owner = :o",array("o"=>$id),PDO::FETCH_ASSOC,"n");
foreach($aquery as $ad)
{
@unlink('/var/www/vhosts/theundergroundsexclub.com/httpdocs/images/personals/'.$ad['image'].'-original.jpg');
@unlink('/var/www/vhosts/theundergroundsexclub.com/httpdocs/images/personals/'.$ad['image'].'-thumb.jpg');
@unlink('/var/www/vhosts/theundergroundsexclub.com/httpdocs/images/personals/'.$ad['image'].'.jpg');
//// DELETE NEWS
$this->db->query("DELETE FROM news WHERE itemid = :id AND type = 'personalad'", array("id"=>$ad['id']),PDO::FETCH_ASSOC,"n");
//// DELETE Search
$link = '/?a='.$ad['id'];
$this->db->query("DELETE FROM search WHERE url = :url", array("url"=>$link),PDO::FETCH_ASSOC,"n");
////  UPDATE PMS TO UN INCLUDE THE AD LINK
$this->db->query("UPDATE pm SET personal = :v WHERE personal = :ad", array("v"=>'0',"ad"=>$ad['id']),PDO::FETCH_ASSOC,"n");
/// DEL AD
$this->db->query("DELETE FROM classifieds WHERE id = :id", array("id"=>$ad['id']),PDO::FETCH_ASSOC,"n");
}


//////////   REMOVE MEMBER COMMENTS FOR AND FROM
$this->db->query("DELETE FROM comments WHERE owner = :id", array("id"=>$id),PDO::FETCH_ASSOC,"n");
$this->db->query("DELETE FROM comments WHERE type = 'member' AND itemid = :id", array("id"=>$id),PDO::FETCH_ASSOC,"n");


///////  REMOVE FEED POSTS BY MEMBER
$fquery = $this->db->query("SELECT * FROM feed WHERE owner = :o",array("o"=>$id),PDO::FETCH_ASSOC,"n");
foreach($fquery as $f)
{
/// DELETE COMMENTS
$this->db->query("DELETE FROM comments WHERE type = 'feed' AND itemid = :id", array("id"=>$f['id']),PDO::FETCH_ASSOC,"n");
///  DELETE VOTES
$this->db->query("DELETE FROM votes WHERE type = 'feed' AND itemid = :id", array("id"=>$f['id']),PDO::FETCH_ASSOC,"n");
/// DELETE FEED POST
$this->db->query("DELETE FROM feed WHERE id = :id", array("id"=>$f['id']),PDO::FETCH_ASSOC,"n");
}

//////   CHANGE FORUM OWNER TO REMOVED MEMBER
$this->db->query("UPDATE forumposts SET addedby = 0 WHERE addedby = :id", array("id"=>$id),PDO::FETCH_ASSOC,"n");
$this->db->query("UPDATE forumtopics SET addedby = 0 WHERE addedby = :id", array("id"=>$id),PDO::FETCH_ASSOC,"n");


////  DELETE FRIENDSHIP
$this->db->query("DELETE FROM friends WHERE owner = :id OR who = :id2", array("id"=>$id,"id2"=>$id),PDO::FETCH_ASSOC,"n");


//// REMOVE GALLERIES
$gquery = $this->db->query("SELECT * FROM galleries WHERE owner = :o",array("o"=>$id),PDO::FETCH_ASSOC,"n");
foreach($gquery as $g)
{
/// IMAGE SUB QUERY
$gquery2 = $this->db->query("SELECT * FROM galleryimages WHERE gallery = :g",array("g"=>$g['id']),PDO::FETCH_ASSOC,"n");
foreach($gquery2 as $i)
{
/// DELETE PHYSICAL IMAGE
@unlink('/var/www/vhosts/theundergroundsexclub.com/httpdocs/images/galleries/'.$g['id'].'/'.$i['id'].'-original.jpg');
@unlink('/var/www/vhosts/theundergroundsexclub.com/httpdocs/images/galleries/'.$g['id'].'/'.$i['id'].'.jpg');
@unlink('/var/www/vhosts/theundergroundsexclub.com/httpdocs/images/galleries/'.$g['id'].'/'.$i['id'].'-thumb.jpg');
/// DELETE DATABASE ENTRY
$this->db->query("DELETE FROM galleryimages WHERE id = :id", array("id"=>$i['id']),PDO::FETCH_ASSOC,"n");
//// DELETE NOTIFICATIONS
$this->db->query("DELETE FROM notifications WHERE itemid = :id AND type = 'photo'", array("id"=>$i['id']),PDO::FETCH_ASSOC,"n");
//// DELETE COMMENTS
$this->db->query("DELETE FROM comments WHERE itemid = :id AND type = 'photo'", array("id"=>$i['id']),PDO::FETCH_ASSOC,"n");
//// DELETE VOTES
$this->db->query("DELETE FROM votes WHERE itemid = :id AND type = 'photo'", array("id"=>$i['id']),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY NEWS
$this->db->query("DELETE FROM news WHERE itemid = :id AND type = 'photo'", array("id"=>$g['id']),PDO::FETCH_ASSOC,"n");
}
/// DELETE GALLERY DATABASE ENTRY
$this->db->query("DELETE FROM galleries WHERE id = :id", array("id"=>$g['id']),PDO::FETCH_ASSOC,"n");
//////////// UPDATE USER COUNT
$count=$this->db->query("SELECT id FROM galleries WHERE owner = :u AND completed = 'y'",array("u"=>$g['owner']),PDO::FETCH_NUM,'y');
$this->db->query("UPDATE members SET count_galleries = :p WHERE id = :id", array("p"=>$count,"id"=>$g['owner']),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY NOTIFICATIONS
$this->db->query("DELETE FROM notifications WHERE itemid = :id AND type = 'gallery'", array("id"=>$g['id']),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY COMMENTS
$this->db->query("DELETE FROM comments WHERE itemid = :id AND type = 'gallery'", array("id"=>$g['id']),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY VOTES
$this->db->query("DELETE FROM votes WHERE itemid = :id AND type = 'gallery'", array("id"=>$g['id']),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY NEWS
$this->db->query("DELETE FROM news WHERE itemid = :id AND type = 'gallery'", array("id"=>$g['id']),PDO::FETCH_ASSOC,"n");
//// DELETE GALLERY SEARCH
$url = '/?g='.$g['id'];
$this->db->query("DELETE FROM search WHERE url = :url", array("url"=>$url),PDO::FETCH_ASSOC,"n");
}


////   REMOVE GROUP MEMBERSHIPS
$this->db->query("DELETE FROM groupfollows WHERE owner = :id", array("id"=>$id),PDO::FETCH_ASSOC,"n");


/// UPDATE GROUP OWNER
$this->db->query("UPDATE groups SET owner = 0 WHERE owner = :id", array("id"=>$id),PDO::FETCH_ASSOC,"n");


//// DELETE NEWS
$this->db->query("DELETE FROM news WHERE owner = :id", array("id"=>$id),PDO::FETCH_ASSOC,"n");
$this->db->query("DELETE FROM news WHERE itemid = :id and type = 'follow'", array("id"=>$id),PDO::FETCH_ASSOC,"n");


////  DELETE NOTIFICATIONS
$this->db->query("DELETE FROM notifications WHERE owner = :o OR who = :o2", array("o"=>$id,"o2"=>$id),PDO::FETCH_ASSOC,"n");



////  DELETE PRIVATE MESSAGES
$this->db->query("DELETE FROM pm WHERE `to` = :id OR `from` = :id2", array("id"=>$id,"id2"=>$id),PDO::FETCH_ASSOC,"n");


//// DEL STORIES
$query = $this->db->query("SELECT * FROM stories WHERE owner = :o",array("o"=>$id),PDO::FETCH_ASSOC,"n");
foreach($query as $s)
{
/// DEL STORY
$this->db->query("DELETE FROM stories WHERE id = :id", array("id"=>$s['id']),PDO::FETCH_ASSOC,"n");
//// DELETE NEWS
$this->db->query("DELETE FROM news WHERE itemid = :id AND type = 'story'", array("id"=>$s['id']),PDO::FETCH_ASSOC,"n");
//// DELETE Search
$link = '/?s='.$s['id'];
$this->db->query("DELETE FROM search WHERE url = :url", array("url"=>$link),PDO::FETCH_ASSOC,"n");
////////////////  UPDATE STORY CATEGORY INFO
$last = $this->db->row("SELECT * FROM stories WHERE catid = :c ORDER BY id DESC",array("c"=>$s['catid']));
$lasttime = $last['stamp'];
$count = $this->db->query("SELECT id FROM stories WHERE catid = :c",array("c"=>$s['catid']),PDO::FETCH_NUM,'y');
$this->db->query("UPDATE storycategories SET stories = :s, laststamp =:t WHERE id = :id", array("s"=>$count,"t"=>$lasttime,"id"=>$s['catid']),PDO::FETCH_ASSOC,"n");
//// DELETE NOTIFICATIONS
$this->db->query("DELETE FROM notifications WHERE itemid = :id AND type = 'story'", array("id"=>$s['id']),PDO::FETCH_ASSOC,"n");
//// DELETE COMMENTS
$this->db->query("DELETE FROM comments WHERE itemid = :id AND type = 'story'", array("id"=>$s['id']),PDO::FETCH_ASSOC,"n");
//// DELETE VOTES
$this->db->query("DELETE FROM votes WHERE itemid = :id AND type = 'story'", array("id"=>$s['id']),PDO::FETCH_ASSOC,"n");
}


//// DELETE Search
$link = '/?u='.$id;
$this->db->query("DELETE FROM search WHERE url = :url", array("url"=>$link),PDO::FETCH_ASSOC,"n");
///////   AFTER THAT DELETE THE MEMBER SETTINGS
$this->db->query("DELETE FROM members WHERE id = :id", array("id"=>$id),PDO::FETCH_ASSOC,"n");

}




//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
$short = NEW short();
$short->page = &$page;
$short->db = &$db;
