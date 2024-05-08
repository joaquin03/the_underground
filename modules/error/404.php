<?php
if(!isset($_GET['mod']))
{
die();
}
$page->page .= $page->get_temp('templates/error/404.tpl');
$array['pagetitle'] = 'Page Missing';


//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'404 Page Missing',2);

header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
