<?php




$text = '<h2>Popular Pages</h2><div class="space-10"></div>';

///// PAGES FIRST
$query = $db->query("SELECT town,phrase,id FROM pages WHERE displayed = 'n' ORDER BY rand() LIMIT 15",null,PDO::FETCH_ASSOC,"n");
$x = 0;
foreach($query as $data)
{
$x++;
$spacer = ($x!=1) ? '<div class="space-5"></div>': '';
$text .= $spacer.'<span class="onelinetext"><span class="red fstyle1 fs22 m0">&rsaquo;</span> &nbsp; <a href="../?page='.urlencode($data['phrase']).'">'.$data['phrase'].'</a></span>';
/// MARK AS DISPLAYED
$db->query("UPDATE pages SET displayed = 'y' WHERE id = :id", array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
}

///  WHEN NONE TO SHOW, CLEAR DISPLAYED AND START OVER
if($x==0)
{
$db->query("UPDATE pages SET displayed = 'y'",null,PDO::FETCH_ASSOC,"n");
}





/// ADD TO DATABASE
$db->query("UPDATE site_settings SET value = :v WHERE name = 'page list'",array("v"=>$text),PDO::FETCH_ASSOC,"n");
