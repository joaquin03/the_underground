<?php
if(!isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']."/?mod=register");
exit;
}

$page->page .= $page->get_temp('templates/stories/new.htm');



$array['pagetitle'] = 'Add a Story';





$array['title'] = '';
$array['textarea1'] = '';

$array['c1'] = '';
$array['c2'] = '';
$array['t1'] = '';
$array['t2'] = '';
$array['d1'] = '';
$array['d2'] = '';


//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=stories','Sex Stories',2);


////////////////// GET CATS
$catdata = $db->row("SELECT * FROM storycategories WHERE id = :id",array("id"=>$_GET['cat']));
if($catdata['id'] > 0)
{
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=stories&file=category&id='.$catdata['id'],$short->clean($catdata['title']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'New Story',4);

}
else
{
if(isset($_GET['cat']))
{
header("Location:".$array['rooturl']."/?mod=forum&file=add");
exit;
}
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'New Story',4);

$catdata['id'] = '27';
}









////////////////  POST FORM
if(isset($_POST['button']))
{
$array['title'] = $_POST['title'];
$array['body'] = $_POST['body'];
$catdata['id'] = $_POST['cat'];
///
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}
$ok = 'y';

////   CHECK POST HAS A BODY
if($post['body'] == '')
{
$ok = 'n';$array['d1'] = 'error';	$array['d2'] = 'is Required';
}
////   CHECK POST HAS A TITLE
if($post['title'] == '')
{
$ok = 'n';$array['t1'] = 'error';	$array['t2'] = 'is Required';
}
////   CHECK POST HAS A CATEGORY
if($post['cat'] == '')
{
$ok = 'n';$array['c1'] = 'error';	$array['c2'] = 'is Required';
}


///
if($ok == 'y')
{
/// INSERT
$db->query("INSERT INTO stories(body,catid,stamp,owner,title) VALUES(:b,:c,:s,:o,:t)",
array("b"=>$post['body'],"c"=>$post['cat'],"s"=>$time,"o"=>$_SESSION['userid'],"t"=>$post['title']),PDO::FETCH_ASSOC,"n");
$newid = $db->lastInsertId();

/////  ADD NEWS FEED
$db->query("INSERT INTO news(owner,itemid,type,stamp) VALUES(:u,:item,:type,:st)",
array("u"=>$_SESSION['userid'],"item"=>$newid,"type"=>'story',"st"=>$time),PDO::FETCH_ASSOC,"n");

////////////////  UPDATE STORY CATEGORY INFO
$count = $db->query("SELECT id FROM stories WHERE catid = :c",array("c"=>$post['cat']),PDO::FETCH_NUM,'y');
$db->query("UPDATE storycategories SET stories = :s, laststamp =:t WHERE id = :id", array("s"=>$count,"t"=>$time,"id"=>$post['cat']),PDO::FETCH_ASSOC,"n");


$_SESSION['gmessage'] = 'Story Added Successfully';
header("Location:".$array['rooturl']."/?s=".$newid);
exit;
}
}









///////////////   GET FORUMS MAIN CATS
$query = $db->query("SELECT * FROM storycategories ORDER BY title ASC",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$array['cats'] .= ($data['id'] == $catdata['id']) ? '<option value="'.$data['id'].'" selected="selected">'.$data['title'].'</option>': '<option value="'.$data['id'].'">'.$data['title'].'</option>';
}
