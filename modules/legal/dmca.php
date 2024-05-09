<?php
$array['pagetitle'] = 'DMCA Policy';
$array['pagedescription'] = '';
$page->page .= $page->get_temp('templates/legal/dmca.htm');
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=legal','Legal',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'DMCA Policy',3);
$array['extrameta'] .= '
<meta name="robots" content="noindex,follow">';

$array['extrameta'] .= '<style>OL { counter-reset: item;list-style:inside;padding-left:0;}
LI { display: block;list-style:inside;padding-left:0;}
LI:before { content: counters(item, ".") " "; counter-increment: item ; display:inline-block;width:30px;font-weight:bold;}
.liheading{font-family: \'Yanone Kaffeesatz\', sans-serif;font-weight:700;font-size: 26px;margin-bottom:30px;}
.linormal{font-family: \'Open Sans\', sans-serif;font-size: 12px;line-height:1.8;font-weight:normal;}
.liheading ol{margin-top:10px;}</style>';
