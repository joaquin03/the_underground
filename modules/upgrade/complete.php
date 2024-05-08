<?php
if(!isset($_GET['mod']))
{
die();
}
$array['pagetitle'] = 'Upgrade Successful';
$page->page .= $page->get_temp('templates/upgrade/complete.htm');



$array['breadcrumb'] = $short->breadcrumb('Upgrade Account',$array['ogurl']);
