<?php
$page->page .= $page->get_temp('templates/error/503.tpl');
header('HTTP/1.1 503 Service Temporarily Unavailable');
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: 86400');
