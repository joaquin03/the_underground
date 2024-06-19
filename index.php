<?php
session_start();
ob_start();
  error_reporting(E_ALL & ~E_NOTICE);

////
$ltt = microtime();
$ltt = explode(' ', $ltt);
$ltt = $ltt[1] + $ltt[0];
$ltstart = $ltt;



include_once('modules/legal/legal_modal.php');
include('configfile.php');


////////////////////////////////////////////////////////   INCLUDE ADDONS
include_once(''.$serverpath.'/addons/Db.class.php');
$db = new Db();
include(''.$serverpath.'/addons/page.php');
include(''.$serverpath.'/addons/Mobile_Detect.php');
include(''.$serverpath.'/addons/short.php');



///////  REDIRECT SUBDOMAINS THAT ARE NOT ALLOWED
$subd = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
if ( $subd != 'www' && false) { //todo: I change this.
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: ".$rooturl.$_SERVER['REQUEST_URI']);
	exit;
}




$array['scripts'] = '';
$array['pagetitle'] = '';
$array['pagetitlejoiner'] = ' &middot; ';
$array['pagedescription'] = '';
$array['breadcrumbs'] = '';
$array['extrameta'] = '';
$array['message'] = '';
$array['searchval'] = '';
$array['chatbar'] = '';







//////////////////////   DETECT MOBILE DEVICES
$detect = new Mobile_Detect;
if( $detect->isMobile() && !$detect->isTablet() ){
include(''.$serverpath.'/addons/versionmobile.php');
}
else
{
include(''.$serverpath.'/addons/versionfull.php');
}





///////  IF THERE IS A SESSION
if(isset($_SESSION['userid']))
{
include(''.$serverpath.'/addons/loggedin.php');
}
else ////// NOT A MEMBER LOGGED IN
{
include(''.$serverpath.'/addons/loggedout.php');
}







////////   STANDARD VARIABLES
if ($_SERVER['HTTP_REFERER'] != ""){$_SESSION['history'] = $_SERVER['HTTP_REFERER'];}
$array['backpage'] = $_SESSION['history'];

$array['pageurl'] = $rooturl.$_SERVER['REQUEST_URI'];
$array['ogimage'] = $rooturl.'/logos/og.jpg';
$array['ogurl'] = $rooturl.$_SERVER['REQUEST_URI'];


if(!isset($_GET['mod'])){$_GET['mod'] = 'home';}
if($_GET['mod'] == 'home' && isset($_SESSION['userid'])){$_GET['mod'] = 'myhome';}





//////////  OLD REDIRECTS
if(isset($_GET['p']) && isset($_GET['i']))
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$rooturl."/?i=".$_GET['i']."");
exit;
}
//////////  PAGE
if($_GET['mod'] == 'i')
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$rooturl."/?mod=register");
//header("Location: ".$rooturl."/?page=".str_replace('_','+',$_GET['file'])."");
exit;
}
//////////  OLD FEED TO HOMEPAGE
if($_GET['mod'] == 'feed')
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$rooturl);
exit;
}
//////////  OLD PRIVACY
if($_GET['mod'] == 'privacy')
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$rooturl."/?mod=legal&file=privacy");
exit;
}
//////////  OLD TERMS
if($_GET['mod'] == 'terms')
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$rooturl."/?mod=legal&file=terms");
exit;
}

if($_GET['mod'] == 'acceptable')
{
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: ".$rooturl."/?mod=legal&file=acceptable");
  exit;
}



////////////////////////////////////////////////////////////////////////////////// ABUSE
if (strpos($_SERVER['REQUEST_URI'], 'f-y_') !== false) {
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$rooturl."");
exit;
}








//////////////////////////////  SHORT PAGE URL REWRITES
if(isset($_GET['validate']))
{
$_GET['mod'] = 'register';
$_GET['file'] = 'validate';
}
if(isset($_GET['newvalidate']))
{
$_GET['mod'] = 'register';
$_GET['file'] = 'newvalidate';
}
if(isset($_GET['f']))
{
$_GET['mod'] = 'forum';
$_GET['file'] = 'discussion';
}
if(isset($_GET['a']))
{
$_GET['mod'] = 'personals';
$_GET['file'] = 'view';
}
if(isset($_GET['s']))
{
$_GET['mod'] = 'stories';
$_GET['file'] = 'read';
}
if(isset($_GET['p']))
{
$_GET['mod'] = 'galleries';
$_GET['file'] = 'view';
}
if(isset($_GET['u']))
{
$_GET['mod'] = 'members';
$_GET['file'] = 'profile';
}
if(isset($_GET['g']))
{
$_GET['mod'] = 'groups';
$_GET['file'] = 'group';
}
if(isset($_GET['n']))
{
$_GET['mod'] = 'sexnews';
$_GET['file'] = 'view';
}
////  IMAGES
if(isset($_GET['i']))
{
$_GET['mod'] = 'galleries';
$_GET['file'] = 'photo';
}
////  WEBSITE
if(isset($_GET['w']))
{
$_GET['mod'] = 'website';
}
////  FEED
if(isset($_GET['item']))
{
$_GET['mod'] = 'feed';
}




////////////////////////////////////////////  PAGE
if(isset($_GET['page']))
{
$_GET['mod'] = 'misc';
$_GET['file'] = 'page';
}
if(isset($_GET['t']))
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$rooturl."/?mod=register");
exit;
}
if(isset($_GET['userinfo']))
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$rooturl."/?mod=register");
exit;
}

////////////////////////////////////////////  LOCAL PAGES
if(isset($_GET['local']))
{
$_GET['mod'] = 'local';
$_GET['file'] = 'suburb';
}
if(isset($_GET['local-women']))
{
$_GET['mod'] = 'local';
$_GET['file'] = 'local-women';
}
if(isset($_GET['local-sluts']))
{
$_GET['mod'] = 'local';
$_GET['file'] = 'local-sluts';
}

////////////////////////////////////////////  CLUB
if(isset($_GET['club']))
{
$_GET['mod'] = 'misc';
$_GET['file'] = 'club';
}

////////////////////////////////////////////  SEARCH
if(isset($_GET['q']))
{
$_GET['mod'] = 'search';
$_GET['file'] = 'index';
}




////// SET MENU ARRAY
$array['ma-'.$_GET['mod'].''] = 'mactive';








///////////////////////////////////////////// LAST ACTIVE PAGE
if($_GET['mod'] != 'login' && $_GET['mod'] != 'register' && $_GET['mod'] != 'logout')
{
$_SESSION['activeredirect'] = $_SERVER['REQUEST_URI'];
}












///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   SESSION MESSAGES
$array['errormessage'] = '';
if(isset($_SESSION['gmessage']))
{
$array['errormessage'] = $short->message($_SESSION['gmessage'], 'g');
unset($_SESSION['gmessage']);
}
if(isset($_SESSION['rmessage']))
{
$array['errormessage'] = $short->message($_SESSION['rmessage'], 'r');
unset($_SESSION['rmessage']);
}
if(isset($_SESSION['emessage']))
{
$array['errormessage'] = $short->message($_SESSION['emessage'], 'e');
unset($_SESSION['emessage']);
}


















//// DISPLAY LOAD TIME 1
$ltt = microtime();
$ltt = explode(' ', $ltt);
$ltt = $ltt[1] + $ltt[0];
$ltfinish = $ltt;
$total_time1 = round(($ltfinish - $ltstart), 4);
$ltt = microtime();
$ltt = explode(' ', $ltt);
$ltt = $ltt[1] + $ltt[0];
$ltstart = $ltt;



////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   PAGE CREATION
$tpl = 'templates/main/index'.$mobilemod.'.htm';
if(isset($_GET['mod']) && !isset($_GET['file']))
{
$mod = basename($_GET['mod']);
$addy = 'modules/'.$mod.'/index.php';
if(!file_exists($addy)){$addy = "modules/error/404.php";}
}
if(isset($_GET['mod']) && isset($_GET['file']))
{
$mod = basename($_GET['mod']);
$file = basename($_GET['file']);
$addy = 'modules/'.$mod.'/'.$file.'.php';
if(!file_exists($addy)){$addy = "modules/error/404.php";}
}

////  MAINTENANCE
if($_SERVER['REMOTE_ADDR'] == '124.149.88.55ggg')
{
$tpl = 'templates/main/index-maintenance.htm';
$addy = 'modules/error/503.php';
}


$mainhtml = $page->get_temp($tpl);
$split = explode("<!--content-->", $mainhtml);
$page->page = $split[0];
include($addy);



/////////////////////////////  THINGS TO DO AFTER ALL PHP IS RUN FROM INDEX AND MODS

/// BREADCRUMB output
$array['bc'] = '
<span itemscope itemtype="http://schema.org/BreadcrumbList">
<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="inline">
<a href="'.$rooturl.'" itemprop="item"><meta itemprop="name" content="'.$array['hometext2'].'"/>'.$array['hometext2'].'</a>
<meta itemprop="position" content="1" />
</span>'.$array['breadcrumbs'].'</span>
';



//// DISPLAY LOAD TIME 2
$ltt = microtime();
$ltt = explode(' ', $ltt);
$ltt = $ltt[1] + $ltt[0];
$ltfinish = $ltt;
$total_time2 = round(($ltfinish - $ltstart), 4);
$array['loadtime'] = ($adminip == $userip) ? '<span class="bcdiv">/</span>Sec 1: '.$total_time1.' <span class="grey">|</span> Sec 2: '.$total_time2.'': '' ;


/////////// INSERT AND DELETE VISITORS
include(''.$serverpath.'/addons/visitorlog.php');



///
$page->page .= $split[1];
$page->replace_tags($array);
$page->output();


/*

///// CREATE CACHE IF IS A INCLUDED TYPE
if($createcache == 'y')
{
$cached = fopen($cachefile, 'w');
fwrite($cached, ob_get_contents());
fclose($cached);
}
*/
