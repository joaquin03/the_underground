<?php



  $db->query(
    "UPDATE members SET tos_at = :tos_at WHERE id = :id",
    array("tos_at"=>date("Y-m-d H:i:s"), "id"=>$_SESSION['userid']),
    PDO::FETCH_ASSOC,"n");

  header("Location: /");
