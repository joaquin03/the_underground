<?php
$mobilemod='';
$array['mobilemod'] = '';
$array['device'] = 'Computer';
$array['admobilelink'] = '';
$paginate_adj = '6';


$setting = $db->row("SELECT value FROM site_settings WHERE `name` = 'member count'");
$array['mcount'] = number_format($setting['value']);



	/// CHAT BAR
include(''.$serverpath.'/addons/chat.php');


//// BANNER ADS
$query = $db->query("SELECT * FROM ads WHERE type = 'fullbanner' AND active = 'y' ORDER BY rand() LIMIT 2",null,PDO::FETCH_ASSOC,"n");
$x=0;
foreach($query as $data)
{
$x++;
$array['topads'] .= '<div id="topad">
<div id="col1">
<a href="'.$data['link'].'">
<img src="'.$staticads.'/images/ads/'.$data['id'].'.'.$data['ext'].'" width="'.$data['display_x'].'" height="'.$data['display_y'].'" border="0"alt=""/></a>
</div>
<a href="'.$data['link'].'"><span class="fs26 fstyle1 onelinetext red">'.$data['text1'].'</span></a>
<div class="space1"></div>
'.$data['text2'].'
<div class="space1"></div>
<a href="'.$data['link'].'"><span class="red">'.$data['text3'].' &rsaquo;</span></a>
</div>';
}
