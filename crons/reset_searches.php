<?php


///   GALLERIES
$db->query("DELETE FROM search WHERE type = 'gallery'", null,PDO::FETCH_ASSOC,"n");
$db->query("UPDATE galleries SET searchable = 'n'", null,PDO::FETCH_ASSOC,"n");

///   POSTS
$db->query("DELETE FROM search WHERE type = 'feed'", null,PDO::FETCH_ASSOC,"n");
$db->query("UPDATE feed SET searchable = 'n'", null,PDO::FETCH_ASSOC,"n");
