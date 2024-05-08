<?php
session_start();
ob_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE);

////////////////////////////////////////////////////////   INCLUDE SITE CONFIG
include('/var/www/vhosts/theundergroundsexclub.com/httpdocs/configfile.php');
include_once(''.$serverpath.'/addons/Db.class.php');
$db = new Db();
include(''.$serverpath.'/addons/short.php');
include(''.$serverpath.'/addons/page.php');






include('api_fling.php');




//////////////  DELETE DIAGNOSED FAILS
//// EMAIL IN USE
$db->query("DELETE FROM s_api_registrations WHERE company = 'fling' AND returnstring LIKE :v", array("v"=>'%mail address in use%'),PDO::FETCH_ASSOC,"n");
//// BAD EMAIL ADDRESS
$db->query("DELETE FROM s_api_registrations WHERE company = 'fling' AND returnstring LIKE :v", array("v"=>'%Bad Email Address%'),PDO::FETCH_ASSOC,"n");


/// DELETE AFTER 4 TRIES
$db->query("DELETE FROM s_api_registrations WHERE tries > 4", null,PDO::FETCH_ASSOC,"n");

//////////////  DELETE COMPLETED REGS
$db->query("DELETE FROM s_api_registrations WHERE company = 'fling' AND returnstring LIKE :v", array("v"=>'0|%'),PDO::FETCH_ASSOC,"n");

//// NO REASON FAILS - CANT FIX
$db->query("DELETE FROM s_api_registrations WHERE company = 'fling' AND returnstring LIKE :v", array("v"=>'1||%'),PDO::FETCH_ASSOC,"n");
