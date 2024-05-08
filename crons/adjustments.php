<?php






//////////// REMOVE FOLLOWS FOR NON EXISTANT MEMBERS
$query = $db->query("SELECT * FROM friends WHERE checkedoff = 'n' LIMIT 10000",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$owner = $db->query("SELECT id FROM members WHERE id = :id limit 1",array("id"=>$data['owner']),PDO::FETCH_NUM,'y');
$who = $db->query("SELECT id FROM members WHERE id = :id limit 1",array("id"=>$data['who']),PDO::FETCH_NUM,'y');
if(($owner+$who) != 2)
{
echo $data['id'].' '.$owner.'-'.$who.' ';
$db->query("DELETE FROM friends WHERE id = :id", array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}
else
{
$db->query("UPDATE friends SET checkedoff = 'y' WHERE id = :id",array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}
}
