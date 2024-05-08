<?php
include('configfile.php');
include_once(''.$serverpath.'/addons/Db.class.php');
$db = new Db();
//////   GET CAMPAIGN
$campaign = $_GET['c'];


$mobile = (isset($_GET['m'])) ? 'y': 'n';


/////  INSERT REGISTRATION DATA
$vip = $_SERVER['REMOTE_ADDR'];
$rdev = (isset($_GET['m'])) ? 'mobile': 'desktop';
$time = time();
//



///////////////   GET ACCOUNT LINKS FOR HUGE TRAFFIC
if($_GET['b'] == 'fling' || $_GET['b'] == 'asstok' || $_GET['b'] == 'meetlocals' || $_GET['b'] == 'instabang' || $_GET['b'] == 'milfplay' || $_GET['b'] == 'fuckdate' || $_GET['b'] == 'uberhorny' || $_GET['b'] == 'sexygamerz')
{
////  CAMPAIGN LINK
$c = ($campaign != '') ? '&cmp='.$campaign: '';
//// ACCOUNTS
$account = array(
'1' => 'theusc_ppl',
'2' => 'theusc_ppl'
);
$num = rand(1,2);
$ac = $account[$num];
}









///////////////   GET ACCOUNT LINKS FOR DATEPROFIT
if($_GET['b'] == 'milfaholic' || $_GET['b'] == 'affairalert')
{
////  CAMPAIGN LINK
$c = ($campaign != '') ? '&k1='.$campaign: '';
}








///////////////   GET ACCOUNT LINKS FOR DATING GOLD
if($_GET['b'] == 'affairhookups' || $_GET['b'] == 'housewife' || $_GET['b'] == 'seekingmilf' || $_GET['b'] == 'cheatinghookup' || $_GET['b'] == 'hookupcougars' || $_GET['b'] == 'milfsaffair')
{
////  CAMPAIGN LINK
$c = ($campaign != '') ? '&xcc='.$campaign: '';
}















///////   FLING
if($_GET['b'] == 'fling')
{
//////  LINKS
$links = array(
'1' => 'https://www.fling.com/enter.php?t=best&id='.$ac,
'2' => 'https://www.fling.com/enter.php?t=best&id='.$ac,
'3' => 'https://www.fling.com/enter.php?t=best&id='.$ac
);
$num = rand(1,3);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}





///////   ASSTOK
if($_GET['b'] == 'asstok')
{
//////  LINKS
$links = array(
'1' => 'http://www.asstok.com/enter.php?t=best&id='.$ac
);
$num = rand(1,1);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}

///////   SNAPSEXT
if($_GET['b'] == 'snapsext')
{
//////  LINKS
$links = array(
'1' => 'http://www.asstok.com/enter.php?t=best&id='.$ac
);
$num = rand(1,1);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}





///////   MEETLOCALS
if($_GET['b'] == 'meetlocals')
{
//////  LINKS
$links = array(
'1' => 'http://www.asstok.com/enter.php?t=best&id='.$ac,
'2' => 'http://www.asstok.com/enter.php?t=best&id='.$ac,
'3' => 'http://www.asstok.com/enter.php?t=best&id='.$ac
);
$num = rand(1,3);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}





///////   INSTABANG
if($_GET['b'] == 'instabang')
{
//////  LINKS
$links = array(
'1' => 'http://www.instabang.com/enter.php?t=best&id='.$ac,
'2' => 'http://www.instabang.com/enter.php?t=best&id='.$ac,
'3' => 'http://www.instabang.com/enter.php?t=best&id='.$ac
);
$num = rand(1,3);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}









///////   MILFPLAY
if($_GET['b'] == 'milfplay')
{
//////  LINKS
$links = array(
'1' => 'https://www.milfplay.com/enter.php?t=best&id='.$ac
);
$num = rand(1,1);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}









///////   FUCKDATE and OLD UBERHORNY
if($_GET['b'] == 'uberhorny' || $_GET['b'] == 'fuckdate')
{
//////  LINKS
$links = array(
'1' => 'https://www.fuckdate.com/enter.php?t=best&id='.$ac
);
$num = rand(1,1);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}




///////   SEXYGAMERZ
if($_GET['b'] == 'sexygamerz')
{
//////  LINKS
$links = array(
'1' => 'https://www.sexygamerz.com/enter.php?t=best&id='.$ac
);
$num = rand(1,1);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}











///////   AFFAIR HOOKUPS
if($_GET['b'] == 'affairhookups')
{
if($mobile == 'n')//////  FULL LINKS
{
$links = array(
'1' => 'http://www.affairhookups.com?ainfo=MzM1OTZ8MTI2M3ww&skin=200&i=1',
'2' => 'http://www.affairhookups.com?ainfo=MzM1OTZ8MTI2M3ww&skin=202&i=2'
);
}
else/// MOBILE LINKS
{
$links = array(
'1' => 'http://mobile.affairhookups.com/?ainfo=MzM1OTZ8MTI2N3ww&skin=29&i=1',
'2' => 'http://mobile.affairhookups.com/?ainfo=MzM1OTZ8MTI2N3ww&skin=17&i=1'
);
}
$num = rand(1,2);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}











///////   CHEATING HOUSEWIFE
if($_GET['b'] == 'housewife')
{
if($mobile == 'n')//////  FULL LINKS
{
$links = array(
'1' => 'http://www.cheatinghousewife.com?ainfo=MzM1OTZ8MzI3fDA=&skin=200&i=1',
'2' => 'http://www.cheatinghousewife.com?ainfo=MzM1OTZ8MzI3fDA=&skin=202&i=1'
);
}
else/// MOBILE LINKS
{
$links = array(
'1' => 'http://mobile.cheatinghousewife.com?ainfo=MzM1OTZ8MzMxfDA=&skin=29&i=1',
'2' => 'http://mobile.cheatinghousewife.com?ainfo=MzM1OTZ8MzMxfDA=&skin=17&i=1'
);
}
$num = rand(1,2);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}










///////   CHEATING HOOKUP
if($_GET['b'] == 'cheatinghookup')
{
if($mobile == 'n')//////  FULL LINKS
{
$links = array(
'1' => 'http://www.cheatinghookup.com?ainfo=MzM1OTZ8MTE2Mnww&skin=200&i=2',
'2' => 'http://www.cheatinghookup.com?ainfo=MzM1OTZ8MTE2Mnww&skin=202&i=1'
);
}
else/// MOBILE LINKS
{
$links = array(
'1' => 'http://mobile.cheatinghookup.com?ainfo=MzM1OTZ8MTE2Nnww&skin=29&i=1',
'2' => 'http://mobile.cheatinghookup.com?ainfo=MzM1OTZ8MTE2Nnww&skin=17&i=1'
);
}
$num = rand(1,2);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}







///////   HOOKUP COUGARS
if($_GET['b'] == 'hookupcougars')
{
if($mobile == 'n')//////  FULL LINKS
{
$links = array(
'1' => 'http://www.hookupcougars.com?ainfo=MzM1OTZ8MzA2OXww&skin=200&i=2',
'2' => 'http://www.hookupcougars.com?ainfo=MzM1OTZ8MzA2OXww&skin=202&i=1'
);
}
else/// MOBILE LINKS
{
$links = array(
'1' => 'http://mobile.hookupcougars.com?ainfo=MzM1OTZ8MzA3M3ww&skin=29&i=1',
'2' => 'http://mobile.hookupcougars.com?ainfo=MzM1OTZ8MzA3M3ww&skin=17&i=1'
);
}
$num = rand(1,2);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}







///////   MILFS AFFAIR
if($_GET['b'] == 'milfsaffair')
{
if($mobile == 'n')//////  FULL LINKS
{
$links = array(
'1' => 'http://www.milfsaffair.com?ainfo=MzM1OTZ8MTYzNnww&skin=200&i=2',
'2' => 'http://www.milfsaffair.com?ainfo=MzM1OTZ8MTYzNnww&skin=200&i=1'
);
}
else/// MOBILE LINKS
{
$links = array(
'1' => 'http://mobile.milfsaffair.com?ainfo=MzM1OTZ8MTY0MHww&skin=29&i=1',
'2' => 'http://mobile.milfsaffair.com?ainfo=MzM1OTZ8MTY0MHww&skin=17&i=1'
);
}
$num = rand(1,2);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}





///////   SEEKING MILF
if($_GET['b'] == 'seekingmilf')
{
if($mobile == 'n')//////  FULL LINKS
{
$links = array(
'1' => 'http://www.seekingmilf.com?ainfo=MzM1OTZ8MTM1OXww&skin=200&i=1',
'2' => 'http://www.seekingmilf.com?ainfo=MzM1OTZ8MTM1OXww&skin=202&i=2'
);
}
else/// MOBILE LINKS
{
$links = array(
'1' => 'http://mobile.seekingmilf.com?ainfo=MzM1OTZ8MTM2M3ww&skin=2&i=1&sgnr=2',
'2' => 'http://mobile.seekingmilf.com?ainfo=MzM1OTZ8MTM2M3ww&skin=17&i=1'
);
}
$num = rand(1,2);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}













///////   CHEATING HOUSEWIFE
if($_GET['b'] == 'milfaholic')
{
if($mobile == 'n')//////  FULL LINKS
{
$links = array(
'1' => 'http://www.milfaholic.com/?page=x3&wm_login=ryptldapi&ps=p',
'2' => 'http://www.milfaholic.com/?page=x5&wm_login=ryptldapi&ps=p'
);
}
else/// MOBILE LINKS
{
$links = array(
'1' => 'http://mobile.milfaholic.com/?page=x1&wm_login=ryptldapi&ps=p',
'2' => 'http://mobile.milfaholic.com/?page=mjoin&from=mobile2&nn=N&wm_login=ryptldapi&ps=p'
);
}
$num = rand(1,2);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}




///////   AFFAIR ALERT
if($_GET['b'] == 'affairalert')
{
if($mobile == 'n')//////  FULL LINKS
{
$links = array(
'1' => 'http://www.affairalert.com/?page=pg6&wm_login=ryptldapi&ps=p',
'2' => 'http://www.affairalert.com/?page=x3&wm_login=ryptldapi&ps=p'
);
}
else/// MOBILE LINKS
{
$links = array(
'1' => 'http://mobile.affairalert.com/?page=mobile8&wm_login=ryptldapi&ps=p',
'2' => 'http://mobile.affairalert.com/?page=mjoin&from=mobile2&wm_login=ryptldapi&ps=p'
);
}
$num = rand(1,2);
$link = $links[$num];
///////// REDIRECT
header("Location:".$link.$c);
exit;
}





///// REDIRECT IF NOTHING IS SET
header("Location:http://www.theundergroundsexclub.com");
exit;
