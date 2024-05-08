<?php
header("Content-type: text/css");

$sufix = '-min';

//////////  GET LIST OF STYLE SHEETS TO RENDER
$list = urldecode($_GET['l']);
$pages = explode(",", $list);
foreach($pages as $key => $page)
{
$styles .= file_get_contents('/var/www/vhosts/theundergroundsexclub.com/httpdocs/styles'.$sufix.'/'.$page.'.css', FILE_USE_INCLUDE_PATH);
}


echo $styles;

//  /var/www/vhosts/theundergroundsexclub.com/httpdocs/
?>
