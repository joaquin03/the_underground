<?php
$array['pagetitle'] = 'Site Terms & Policies';
$array['pagedescription'] = '';
$page->page .= $page->get_temp('templates/legal/index.htm');
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Legal',2);
$array['extrameta'] .= '
<meta name="robots" content="noindex,follow">';
