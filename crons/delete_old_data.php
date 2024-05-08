<?php

/// DATES
$fortydays = $time-(60*60*24*40);
$sixmonths = $time-(60*60*24*180);

//////////// REMOVE NOTIFICATIONS OLDER THAN 6 MONTHS
$db->query("DELETE FROM notifications WHERE stamp < :t", array("t"=>$sixmonths),PDO::FETCH_ASSOC,"n");



///// DELETE OLD PRIVATE MESSAGES
$query = $db->query("SELECT id,image FROM pm WHERE stamp < :t LIMIT 100",array("t"=>$sixmonths),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
/// DELETE IMAGE IF EXISTS
if($data['image'] != '')
{
@unlink($serverpath.'/images/messages/'.$data['image'].'-original.jpg');
@unlink($serverpath.'/images/messages/'.$data['image'].'.jpg');
}
/// DELETE MESSAGE
$db->query("DELETE FROM pm WHERE id = :id", array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}
