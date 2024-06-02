<?php



  $db->query(
    "UPDATE members SET tos_at = :tos_at, tos_accepted = :tos_accepted WHERE id = :id",
    [
      "tos_at"=>date("Y-m-d H:i:s"),
      "id"=>$_SESSION['userid'],
      "tos_accepted"=>1
    ],
    PDO::FETCH_ASSOC,"n");

  header("Location: /");
