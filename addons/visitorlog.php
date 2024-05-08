<?php




$pageloc = $_SERVER['REQUEST_URI'];

if($member['id'] != $sysadminid)
{
$insert = $db->query("INSERT INTO s_visitors(site,location,stamp,bot,ip,userid) VALUES(:s,:l,:t,:b,:i,:u)",
array("s"=>$domainonly,"l"=>$pageloc,"t"=>$time,"b"=>$useragent,"i"=>$userip,"u"=>$member['id']),PDO::FETCH_ASSOC,"n");
}

$newtime = $time-(60*30);
$db->query("DELETE FROM s_visitors WHERE stamp < :s", array("s"=>$newtime),PDO::FETCH_ASSOC,"n");
