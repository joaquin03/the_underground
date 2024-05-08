<?php


$id = $_GET['id'];
$array['id'] = $id;

$group = $db->row("SELECT * FROM groups WHERE id = :id",array("id"=>$id));

if($group['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=groups");
exit;
}

/// BOOT IF NOT OWNED OR NOT ADMIN
if($_SESSION['userid'] == '100' || $_SESSION['userid'] == $group['owner'])
{
}
else
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=groups");
exit;
}



$array['pagetitle'] = 'Edit Group Info';
$array['pagedescription'] = '';
$page->page .= $page->get_temp('templates/groups/edit.htm');

//////////////// DATA
$array['title'] = $short->clean($group['title']);
$array['website'] = $short->clean($group['website']);
$array['slogan'] = $short->clean($group['slogan']);
$array['description'] = $short->clean($group['description']);
$array['catid'] = $group['catid'];

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=groups','Groups',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?g='.$id,$short->clean($group['title']),3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Edit Group',4);





///////////////////////// POST
if(isset($_POST['button']))
{
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}
$n = 'http';
$pos = strpos($post['website'], $n);
if ($pos === false)
{
if($post['website'] != '')
{
$post['website'] = 'http://'.$post['website'];
}
}
$db->query("UPDATE groups SET description = :d, slogan = :s, website = :w, catid = :c WHERE id = :id", array("d"=>$post['description'],"s"=>$post['slogan'],"w"=>$post['website'],"c"=>$post['cat'],"id"=>$id),PDO::FETCH_ASSOC,"n");



$_SESSION['gmessage'] = 'Info Edited Successfully';
header("Location: ".$rooturl."/?g=".$id);
exit;
}






////////////////// GET CATS
$query = $db->query("SELECT * FROM groupcategories",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
if($data['id'] == $group['catid'])
{
$array['cats'] .= '<option value="'.$data['id'].'" selected="selected">'.$data['title'].'</option>';
}
else
{
$array['cats'] .= '<option value="'.$data['id'].'">'.$data['title'].'</option>';
}
}
