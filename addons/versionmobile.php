<?php


$mobilemod='-mobile';
$array['mobilemod'] = '-mobile';
$array['device'] = 'Device';
$array['lastrightad'] = '';
$array['admobilelink'] = '&m=y';
$paginate_adj = '3';


////   MOBILE BOTTOM AD
$bad = $db->row("SELECT * FROM ads WHERE type = 'mobilebottom' AND active = 'y' ORDER BY rand() LIMIT 1");
$ftext = (isset($_GET['page'])) ? '{town}': 'Local';
$text = str_replace('{geo}',$ftext,$bad['text1']);
$array['bottomad'] = ($bad['image'] != '') ? '
<div class="bottomad tcentered" style="padding:0px;">
<a href="'.$bad['link'].'&m=y" rel="nofollow">
<img src="'.$staticads.'/images/ads/'.$bad['image'].'" class="maxwidth100" border="0" alt=""/>
</a>
</div>
': '<a  href="'.$bad['link'].'&m=y" rel="nofollow">
<span class="bottomad fstyle1 fs22">
<span class="badimg fleft">
<img src="'.$staticads.'/images/ads/'.$bad['id'].'.'.$bad['ext'].'" width="30" height="30" border="0"alt=""/>
</span>
<span class="is miniright fright"></span>
<span class="space5 block"></span>'.$text.'
</span>
</a>';
