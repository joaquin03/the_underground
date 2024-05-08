<?php
/// DATABASE
$dbhost = 'localhost';
//$dbuser = 'theusc';
$dbuser = 'root';
//$dbpassword = 'Cggy6#69';
$dbpassword = '';
//$dbname = 'theusc';
$dbname = 'theunderground';
$emailpassword = 'y0p8Zg9#w';

/// TIME
date_default_timezone_set('America/New_York');
$time = time();
$array['cy'] = date("Y",$time);

/// SITE INFO
//$rooturl = 'https://www.theundergroundsexclub.com';
$rooturl = 'http://localhost:8000';
$array['rooturl'] = $rooturl;
$domainonly = 'theundergroundsexclub.com';
$array['domainonly'] = $domainonly;
//$serverpath = '/var/www/vhosts/theundergroundsexclub.com/httpdocs';
$serverpath = '/Users/joaquinanduano/Sites/theundergroundsexclub.com/';
$pageurl = $rooturl.$_SERVER['REQUEST_URI'];
$array['sitename'] = 'The Underground Sex Club';

/// STATIC URLS
$staticurl = 'https://static.theundergroundsexclub.com';
$staticurl = 'http://localhost:8000';
$array['staticurl'] = $staticurl;
$staticads = 'https://staticads.theundergroundsexclub.com';
$staticads = 'http://localhost:8000';
$array['staticads'] = $staticads;

/// ADMIN
$adminip = '49.197.52.39';
$sysadminid = 100;
$adminemailaddress = 'ourteam@theundergroundsexclub.com';

/// USER
$userip = $_SERVER['REMOTE_ADDR'];
$useragent = str_replace(',','',$_SERVER['HTTP_USER_AGENT']);

/// CSS
//$cssnum = rand(560000,99000);
//$array['cssnum'] = $cssnum;
$array['cssnum'] = 'c';
