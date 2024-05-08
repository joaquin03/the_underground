<?php

$page->page .= $page->get_temp('templates/local/suburb.htm');


$array['extrameta'] .= ($mobilemod == '') ? '<style>
.multicol{
-webkit-column-count: 3;
-moz-column-count: 3;
column-count: 3;
}
.mb5{
margin-bottom:5px;
}
</style>' : '';


$town = $db->row("SELECT url,state,country,suburb FROM towns WHERE url = :u", array("u"=>$_GET['local']));


///
$array['pagetitle'] = ''.$town['suburb'].', '.$town['state'].' Local Pages';
$array['title'] = 'The '.$town['suburb'].' Local Pages';
$array['pagedescription'] = 'Welcome to the '.$town['suburb'].' Local Pages for sex. The '.$town['suburb'].' ('.$town['state'].') local pages help you meet people for sex. Browse the '.$town['suburb'].' directoy now.';
$array['leftcol'] .= '<h2>Local Sex Directory for '.$town['suburb'].', '.$town['state'].'</h2>';

$array['suburb'] = $town['suburb'];
$array['state'] = $town['state'];
$array['country'] = $town['country'];

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=local','Local Pages',2);
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=local&area='.$town['state'].'-'.$town['country'].'','Local Pages '.$town['state'].', '.$town['country'].'',3);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'The '.$town['suburb'].' Local Pages',4);


/// LINKS
$array['list'] = '<div class="bm5"><span class="red fstyle1 fs22 m0">&rsaquo;</span> &nbsp; <a href="'.$rooturl.'/?local-women='.$town['url'].'" class="bm5">Local '.$town['suburb'].' Women</a></div>
<div class="bm5"><span class="red fstyle1 fs22 m0">&rsaquo;</span> &nbsp; <a href="'.$rooturl.'/?local-sluts='.$town['url'].'">'.$town['suburb'].' Sluts</a></div>';



//// AD
$a = $db->row("SELECT * FROM ads WHERE type = :t AND active = 'y' ORDER BY rand() LIMIT 1",array("t"=>'content'));
$array['ad1'] = '<div class="space30"></div><a href="'.$a['link'].$array['admobilelink'].'"><img src="'.$staticads.'/images/ads/'.$a['id'].'.'.$a['ext'].'" width="100%" style="max-width:'.$a['display_x'].'" border="0"alt=""/></a>
';
