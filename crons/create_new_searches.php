<?php




/////////////// STANDARD DOCUMENT PAGES
$setting = $db->row("SELECT value FROM site_settings WHERE `name` = 'insert search pages'");
$todo = $setting['value'];
if($todo == 'y')
{
$db->query("DELETE FROM search WHERE rootpage = :id", array("id"=>'y'),PDO::FETCH_ASSOC,"n");
$list = '
1|Contact Us|/?mod=contact|document|Contact details and online contact form for the Underground Sex Club.#
2|Privacy Policy|/?mod=legal&file=privacy|document|Privacy policy for the Underground Sex Club, and what we do with your information.#
3|Site Terms|/?mod=legal&file=terms|document|Terms and conditions for using the Underground Sex Club Website.#
4|Login|/?mod=login|document|Member login to the Underground Sex Club.#
5|Registration|/?mod=register|document|Register to be a member of the Underground Sex Club.#
6|Members|/?mod=members|document|Search and view members on the Underground Sex Club.#
7|Galleries|/?mod=galleries|document|Browse member photos and galleries on the Underground Sex Club.#
8|Sex Groups|/?mod=groups|document|Browse and join sex groups on the Underground Sex Club.#
9|Sex Stories|/?mod=stories|document|Read members sex stories on the Underground Sex Club.#
10|Sex Forum|/?mod=forum|document|Sex forum for the Underground Sex Club.#
11|Personal Ads|/?mod=personals|document|Browse and submit personal ads on the Underground Sex Club.#
12|Sex News|/?mod=sexnews|document|Sex news from around the world on the Underground Sex Club.#
';
$lines = explode("#", $list);
foreach($lines as $key => $line)
{
if($line != '')
{
list($id,$title,$url,$type,$desc) = explode("|", $line);
///////////
if($title != '')
{
$insert = $db->query("INSERT INTO search(rootpage,url,type,title,description,crawled) VALUES(:id,:url,:type,:title,:desc,:ct)", array("id"=>'y',"url"=>$url,"type"=>$type,"title"=>$title,"desc"=>$desc,"ct"=>$time),PDO::FETCH_ASSOC,"n");
}
}
}
$db->query("UPDATE site_settings SET value = :v WHERE name = 'insert search pages'", array("v"=>'n'),PDO::FETCH_ASSOC,"n");
}












/////////// MEMBER SEARCHES
$query = $db->query("SELECT id,username,town,country,about,sex FROM members WHERE validated = 'y' AND searchable = 'n' ORDER BY id DESC LIMIT 1000",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
/////////// USER PROFILE
$url = '/?u='.$data['id'];
$type = 'member';
$title = $data['username'];
$lastbit = ($data['about'] == '') ? 'This is the member profile for '.$data['username'].'': $data['about'];
$location = ($data['town'] == '') ? $data['country'] : $data['town'].', '.$data['country'];

$desc = ''.$data['sex'].' &middot; '.$location.'. '.$lastbit;

$insert = $db->query("INSERT INTO search(url,type,title,description,crawled) VALUES(:url,:type,:title,:desc,:ct)", array("url"=>$url,"type"=>$type,"title"=>$title,"desc"=>$desc,"ct"=>$time),PDO::FETCH_ASSOC,"n");

$db->query("UPDATE members SET searchable = 'y' WHERE id = :id", array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}








/////////// GROUP SEARCHES
$query = $db->query("SELECT * FROM groups WHERE searchable = 'n' ORDER BY id ASC LIMIT 1000",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
///////////
$url = '/?g='.$data['id'];
$type = 'group';
$title = $data['title'];
$desc = ($data['description'] == '') ? 'Sex group with the title: '.$data['title']: ''.$data['description'].' - '.$data['title'].'';

$insert = $db->query("INSERT INTO search(url,type,title,description,crawled) VALUES(:url,:type,:title,:desc,:ct)", array("url"=>$url,"type"=>$type,"title"=>$title,"desc"=>$desc,"ct"=>$time),PDO::FETCH_ASSOC,"n");

$db->query("UPDATE groups SET searchable = 'y' WHERE id = :id", array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}









/////////// STORY SEARCHES
$query = $db->query("SELECT * FROM stories WHERE searchable = 'n' ORDER BY id ASC LIMIT 100",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$cat = $db->row("SELECT * FROM storycategories WHERE id = :id",array("id"=>$data['catid']));
///////////
$url = '/?s='.$data['id'];
$type = 'story';
$title = $data['title'];
$desc = ''.$data['title'].' &middot; '.$cat['title'].' &middot; '.$data['body'];

$insert = $db->query("INSERT INTO search(url,type,title,description,crawled) VALUES(:url,:type,:title,:desc,:ct)", array("url"=>$url,"type"=>$type,"title"=>$title,"desc"=>$desc,"ct"=>$time),PDO::FETCH_ASSOC,"n");

$db->query("UPDATE stories SET searchable = 'y' WHERE id = :id", array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}










/////////// Forum Topic SEARCHES
$query = $db->query("SELECT * FROM forumtopics WHERE searchable = 'n' ORDER BY id ASC LIMIT 1000",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$cat = $db->row("SELECT * FROM forumcategories WHERE id = :id",array("id"=>$data['category']));
$post = $db->row("SELECT * FROM forumposts WHERE topic = :id AND original = 'y' LIMIT 1",array("id"=>$data['id']));
///////////
$url = '/?f='.$data['id'];
$type = 'forumtopic';
$title = $data['title'];
$body = ($post['body'] == '') ? 'Forum Post titled: '.$data['title'].'': $post['body'];
$desc = ''.$cat['title'].' &middot; '.$data['title'].' &middot; '.$body;

$insert = $db->query("INSERT INTO search(url,type,title,description,crawled) VALUES(:url,:type,:title,:desc,:ct)", array("url"=>$url,"type"=>$type,"title"=>$title,"desc"=>$desc,"ct"=>$time),PDO::FETCH_ASSOC,"n");

$db->query("UPDATE forumtopics SET searchable = 'y' WHERE id = :id", array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}










/////////// PERSONAL SEARCHES
$query = $db->query("SELECT * FROM classifieds WHERE title != '' AND delstamp = 0 AND searchable = 'n' ORDER BY id ASC LIMIT 100",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$cat = $db->row("SELECT * FROM classifieds_categories WHERE id = :id",array("id"=>$data['category']));
$country = $db->row("SELECT * FROM loc_countries WHERE id = :id",array("id"=>$data['country']));
$state = $db->row("SELECT * FROM loc_states WHERE id = :id",array("id"=>$data['state']));
$area = $db->row("SELECT * FROM loc_areas WHERE id = :id",array("id"=>$data['area']));
///////////
$url = '/?a='.$data['id'];
$type = 'personal';
$title = $data['title'];
$desc = ''.$area['title'].', '.$state['code'].' ('.$country['code'].') &middot; '.$cat['title'].' &middot; '.$data['description'];

$insert = $db->query("INSERT INTO search(url,type,title,description,crawled) VALUES(:url,:type,:title,:desc,:ct)", array("url"=>$url,"type"=>$type,"title"=>$title,"desc"=>$desc,"ct"=>$time),PDO::FETCH_ASSOC,"n");

$db->query("UPDATE classifieds SET searchable = 'y' WHERE id = :id", array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}














/////////// GALLERY SEARCHES
$query = $db->query("SELECT * FROM galleries WHERE searchable = 'n' AND completed = 'y' AND title != '' ORDER BY id ASC LIMIT 100",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
///////////
$url = '/?p='.$data['id'];
$type = 'gallery';
$title = $data['title'];
$d = ($data['description'] == '') ? 'Photo gallery: '.$data['title']: $data['description'];
$t = ($data['tags'] == '') ? '': ' &middot; '.$data['tags'];

$desc = $d.$t;

$insert = $db->query("INSERT INTO search(url,type,title,description,crawled) VALUES(:url,:type,:title,:desc,:ct)", array("url"=>$url,"type"=>$type,"title"=>$title,"desc"=>$desc,"ct"=>$time),PDO::FETCH_ASSOC,"n");

$db->query("UPDATE galleries SET searchable = 'y' WHERE id = :id", array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}


















/////////// FEED SEARCHES
$query = $db->query("SELECT * FROM feed WHERE searchable = 'n' AND body != '' ORDER BY id ASC LIMIT 100",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$user = $db->row("SELECT username FROM members WHERE id = :id LIMIT 1",array("id"=>$data['owner']));
///////////
$url = '/?item='.$data['id'];
$type = 'feed';
$title = 'Post by '.$user['username'].'';
$desc = $data['body'];

$insert = $db->query("INSERT INTO search(url,type,title,description,crawled) VALUES(:url,:type,:title,:desc,:ct)", array("url"=>$url,"type"=>$type,"title"=>$title,"desc"=>$desc,"ct"=>$time),PDO::FETCH_ASSOC,"n");

$db->query("UPDATE feed SET searchable = 'y' WHERE id = :id", array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}
