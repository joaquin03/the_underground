<?php
session_start();
ob_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE);

///   INCLUDE SITE CONFIG
include('/var/www/vhosts/theundergroundsexclub.com/httpdocs/configfile.php');
include_once(''.$serverpath.'/addons/Db.class.php');
$db = new Db();
include(''.$serverpath.'/addons/short.php');
include(''.$serverpath.'/addons/page.php');


///////////////////////////   THIS CRON RUN HOURLY

///  DELETE RETURNED EMAIL USERS
include('delete_members_returned_emails.php');
