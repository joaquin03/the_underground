<?php




$totmembers = $db->query("SELECT id FROM members WHERE validated = 'y'",null,PDO::FETCH_NUM,'y');

/// ADD TO DATABASE
$db->query("UPDATE site_settings SET value = :v WHERE name = 'member count'",array("v"=>$totmembers),PDO::FETCH_ASSOC,"n");
