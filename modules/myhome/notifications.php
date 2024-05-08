<?php
if(!isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']."/?mod=login");
exit;
}



$array['pagetitle'] = 'Notifications';
$page->page .= $page->get_temp('templates/myhome/notifications.htm');

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Notifications',2);
include(''.$serverpath.'/addons/notifications.php');



////////////////////////////////////////////////////////////////// GET READ NOTIFICATIONS
$array['old'] = '';
$x = 0;
$ad = 0;
$old = '';

$query= $db->query("SELECT * FROM notifications WHERE id IN (SELECT MAX(id) FROM notifications WHERE owner = :o AND `read` = 'y' GROUP BY code) ORDER BY stamp DESC",array("o"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
////  COUNT OTHERS
$count = $db->query("SELECT id FROM notifications WHERE owner = :o and `read` = 'y' AND code = :c",array("o"=>$_SESSION['userid'],"c"=>$data['code']),PDO::FETCH_NUM,'y');

$others = $count-1;
$result = '';
////   OVERALL
$oplur = ($others == 1) ? '' : 's';
$otherstext = ($others > 0) ? ' and '.$others.' other'.$oplur.'' : '';
$string = $notifications->notifystring($data['type'],$data['who'],$otherstext,$data['itemid'],$data['stamp']);

if($string != '')
{
$x++;
$ad++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$old .= $spacer.$string;
/////  INSERT ADS
if($ad==4)
{
$ad=0;
$a = $db->row("SELECT * FROM ads WHERE type = :t AND active = 'y' ORDER BY rand() LIMIT 1",array("t"=>'notifications'));
$old .= $spacer.'<a href="'.$a['link'].$array['admobilelink'].'">'.$a['text1'].'</a> offer: <a href="'.$a['link'].$array['admobilelink'].'">'.$a['text2'].'</a>
<div class="space5"></div>
<a href="'.$a['link'].$array['admobilelink'].'"><img src="'.$staticads.'/images/ads/'.$a['id'].'.'.$a['ext'].'" width="60"/></a>
<div class="space5"></div>
<span class="lightgrey">'.$a['text3'].'</span>';
}// END ADS

}//  END IF THERE IS A STRING
}//  END QUERY FOR GET READ NOTIFICATIONS









////////////////////////////////////////////////////////////////// GET UNREAD/NEW NOTIFICATIONS
$array['new'] = '';
$x = 0;
$ad = 0;
$new = '';

$query= $db->query("SELECT * FROM notifications WHERE id IN (SELECT MAX(id) FROM notifications WHERE owner = :o AND `read` = 'n' GROUP BY code) ORDER BY stamp DESC",array("o"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
////  COUNT OTHERS
$count = $db->query("SELECT id FROM notifications WHERE owner = :o and `read` = 'n' AND code = :c",array("o"=>$_SESSION['userid'],"c"=>$data['code']),PDO::FETCH_NUM,'y');

$others = $count-1;
$result = '';
////   OVERALL
$oplur = ($others == 1) ? '' : 's';
$otherstext = ($others > 0) ? ' and '.$others.' other'.$oplur.'' : '';
$string = $notifications->notifystring($data['type'],$data['who'],$otherstext,$data['itemid'],$data['stamp']);

if($string != '')
{
$x++;
$ad++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$new .= $spacer.$string;
/////  INSERT ADS
if($ad==4)
{
$ad=0;
$a = $db->row("SELECT * FROM ads WHERE type = :t AND active = 'y' ORDER BY rand() LIMIT 1",array("t"=>'notifications'));
$new .= $spacer.'<a href="'.$a['link'].$array['admobilelink'].'">'.$a['text1'].'</a> offer: <a href="'.$a['link'].$array['admobilelink'].'">'.$a['text2'].'</a>
<div class="space5"></div>
<a href="'.$a['link'].$array['admobilelink'].'"><img src="'.$staticads.'/images/ads/'.$a['id'].'.'.$a['ext'].'" width="60"/></a>
<div class="space5"></div>
<span class="lightgrey">'.$a['text3'].'</span>';

}// END ADS

}//  END IF THERE IS A STRING
}//  END QUERY FOR GET READ NOTIFICATIONS











////////////////  SHOW SECTIONS IF EXIST
if($new != '')
{
$array['new'] = '<h2>New</h2>'.$new.'';
}
if($old != '')
{
$array['old'] = '<h2>Old</h2>'.$old.'';
}
$array['spacer'] = ($array['new'] != '' && $array['old'] != '') ? '<div class="space20"></div>': '';
//////////////////// NONE
if($array['new'] == '' & $array['old'] == '')
{
$array['new'] .= 'No New Notifications';
}

/// MARK ALL NOTIFICATIONS READ
$db->query("UPDATE notifications SET `read` = :v WHERE owner = :id", array("v"=>'y',"id"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");
