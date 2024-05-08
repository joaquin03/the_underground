<?php
if(!isset($_SESSION['userid']))
{
header("Location: ".$rooturl."/?mod=register");
exit;
}
$array['pagetitle'] = 'Post a New Ad';
$array['pagedescription'] = '';
$page->page .= $page->get_temp('templates/personals/new.htm');

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=personals','Personals',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Step 1',3);

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

if($post['country'] == '')
{
$ok = 'n';$array['c1'] = 'error';	$array['c2'] = 'must be selected';
}
if($ok == 'y')
{
$db->query("INSERT INTO classifieds(owner,country,stamp) VALUES(:o,:c,:s)",
array("o"=>$_SESSION['userid'],"c"=>$post['country'],"s"=>$time),PDO::FETCH_ASSOC,"n");
$aid = $db->lastInsertId();
header("Location:".$array['rooturl']."/?mod=personals&file=step2&id=".$aid);
exit;
}
}



////////////////// GET COUNTRIES
$array['droplist'] .= '<option value="" selected="selected">-- Select --</option>';
$query = $db->query("SELECT * FROM loc_countries",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$array['droplist'] .= '<option value="'.$data['id'].'">'.$data['title'].'</option>';
}
