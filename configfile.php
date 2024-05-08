<?php

include_once('phpfiles/helpers.php');
loadEnvironmentVariables(__DIR__ . '/.env');
/// DATABASE
$dbhost = getenv('DB_HOST');
//$dbuser = 'theusc';
$dbuser = getenv('DB_USER');
//$dbpassword = 'Cggy6#69';
$dbpassword = getenv('DB_PASS');
//$dbname = 'theusc';
$dbname = 'theunderground';
$emailpassword = 'y0p8Zg9#w';

/// TIME
date_default_timezone_set('America/New_York');
$time = time();
$array['cy'] = date("Y",$time);

/// SITE INFO
//$rooturl = 'https://www.theundergroundsexclub.com';
$rooturl = getenv('URL_BASE');
$array['rooturl'] = $rooturl;
$domainonly = 'theundergroundsexclub.com';
$array['domainonly'] = $domainonly;
//$serverpath = '/var/www/vhosts/theundergroundsexclub.com/httpdocs';
$serverpath = getenv('SERVER_PATH');
$pageurl = $rooturl.$_SERVER['REQUEST_URI'];
$array['sitename'] = 'The Underground Sex Club';

/// STATIC URLS
$staticurl = 'https://static.theundergroundsexclub.com';
$staticurl = getenv('URL_BASE');;
$array['staticurl'] = $staticurl;
$staticads = 'https://staticads.theundergroundsexclub.com';
$staticads = getenv('URL_BASE_STATIC');
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
