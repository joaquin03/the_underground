<?php
if(!isset($_SESSION['userid']))
{
header("Location: ".$rooturl);
exit;
}





$id = $_GET['id'];
$adpost = $db->row("SELECT * FROM classifieds WHERE id = :id AND owner = :o",array("id"=>$id,"o"=>$_SESSION['userid']));
if($adpost['id'] == 0)
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$array['rooturl']."/?mod=personals");
exit;
}
if($adpost['title'] != '')
{
header("Location:".$array['rooturl']."/?a=".$id);
exit;
}






$array['pagetitle'] = 'Post a New Ad';
$array['pagedescription'] = '';
$page->page .= $page->get_temp('templates/personals/step2.htm');

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=personals','Personals',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'New &middot; Step 2',3);
$array['c1'] = '';
$array['c2'] = '';



///////////////////////// POST
if(isset($_POST['button']))
{
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}

$ok = 'y';

if($post['state'] == '')
{
$ok = 'n';$array['c1'] = 'error';	$array['c2'] = 'must be selected';
}
if($ok == 'y')
{
$db->query("UPDATE classifieds SET state = :s WHERE id = :id", array("s"=>$post['state'],"id"=>$adpost['id']),PDO::FETCH_ASSOC,"n");
header("Location:".$array['rooturl']."/?mod=personals&file=step3&id=".$id);
exit;
}
}



////////////////// GET COUNTRIES
$array['droplist'] .= '<option value="" selected="selected">-- Select --</option>';
$query = $db->query("SELECT * FROM loc_states WHERE country = :c",array("c"=>$adpost['country']),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$array['droplist'] .= '<option value="'.$data['id'].'">'.$data['title'].'</option>';
}
