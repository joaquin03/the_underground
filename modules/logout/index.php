<?php
$loc = $_SESSION['activeredirect'];
session_destroy();
session_start();
ob_start();
setcookie('active', '', strtotime("-3 months"), '/');
$_SESSION['gmessage'] = 'Logout Successful';
header("Location: ".$array['rooturl'].$loc);
exit;
