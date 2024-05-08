<?php
if($_SESSION['userid'] != 100)
{
header("Location:".$array['rooturl']."");
exit;
}


/*
$array['pagetitle'] = 'Moderation';
$page->page .= $page->get_temp('templates/myhome/moderatevisitors.htm');

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Moderation',2);
//
*/




//// BAN IP
if(isset($_GET['ban']))
{
$check = $db->query("SELECT id FROM s_ipblacklist WHERE ip = :ip AND type = 'ip' limit 1",array("ip"=>$_GET['ban']),PDO::FETCH_NUM,'y');
if($check == 0 && $_GET['ban'] != '')
{
/// INSERT THE BAN
$insert = $db->query("INSERT INTO s_ipblacklist(type,ip) VALUES(:t,:ip)", array("t"=>'ip',"ip"=>$_GET['ban']), PDO::FETCH_ASSOC,"n");
///  TELL SYSTEM TO MAKE NEW HTACCESS FILES FOR EACH SITE
$db->query("UPDATE s_admin SET value = 'y' WHERE name = 'create_htaccess'", null,PDO::FETCH_ASSOC,"n");
}
/// REMOVE IP FROM VISITORS
if($_GET['ban'] != '')
{
$db->query("DELETE FROM s_visitors WHERE ip = :ip", array("ip"=>$_GET['ban']),PDO::FETCH_ASSOC,"n");
}
// REDIRECT
$_SESSION['gmessage'] = 'Ban Successful';
header("Location: ".$rooturl."/account/moderation-visitors/?site=".$_GET['site']);
exit;
}




$array['pagetitle'] = 'Visitors';
$array['pagedescription'] = '';
$page->page .= $page->get_temp('templates/myhome/moderatevisitors.htm');
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Moderation',2);
$array['title'] = $array['pagetitle'];

$site = ($_GET['site'] == 'underground') ? 'the-usc.com': 'theundergroundsexclub.com';
$array['site'] = $site;
$array['othersite'] = ($_GET['site'] == 'underground') ? 'the-usc': 'underground';
$x = 0;
$y = 0;



$query = $db->query("SELECT userid,bot,ip, COUNT(*) AS count FROM s_visitors WHERE site = :s GROUP BY ip ORDER BY count DESC", array("s"=>$site),PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
$user = ($data['userid'] > 0) ? ' &middot; '.$short->user($data['userid'].'','text','n') : '';
$x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
/// IP HEADING
$array['visitors'] .= $spacer.'<b><a href="'.$rooturl.'/?mod=myhome&file=moderatevisitors&more='.$data['ip'].'">'.$data['ip'].'</a> ('.$data['count'].')</b> &middot; <a href="https://www.abuseipdb.com/check/'.$data['ip'].'" target="_blank">Who</a> &middot; <a href="'.$rooturl.'/?mod=myhome&file=moderatevisitors&ban='.$data['ip'].'">Block</a>'.$user.'
<div class="space5"></div>
'.$data['bot'].'';

if(isset($_GET['more']) && $data['ip'] == $_GET['more'])
{
$array['visitors'] .= '
<div class="space5"></div><div style="padding-left:10px;">';
/// LOOP FOR EACH IP IF MORE DETAILS REQUIRED
$query2 = $db->query("SELECT * FROM s_visitors WHERE site = :s AND ip = :ip ORDER BY stamp DESC", array("s"=>$site, "ip"=>$data['ip']),PDO::FETCH_ASSOC,"n");
foreach ( $query2 as $data2 ) {
$y++;
$array['visitors'] .= '<div class="space5"></div>
<a href="https://www.'.$site.''.$data2['location'].'" target="_blank">'.$data2['location'].'</a>
  <div class="space5"></div>
  '.$short->timeago($data2['stamp']).'';
}
$array['visitors'] .= '</div>';
}




/*
$array['visitors'] .= $spacer.'<span class="fleft"><a href="https://www.'.$site.''.$data['location'].'" target="_blank">'.$data['xlocation'].'</a> &middot; '.$data['ip'].' &middot;
<a href="https://www.abuseipdb.com/check/'.$data['ip'].'" target="_blank">Who</a> &middot;
<a href="'.$rooturl.'/account/moderation-visitors/?site='.$_GET['site'].'&ban='.$data['ip'].'">Block</a><span><span class="fright grey">'.$short->timeago($data['stamp']).'</span><div class="clear"></div>'.$data['xbot'].'';
*/
}
$array['tot'] = number_format($y);
