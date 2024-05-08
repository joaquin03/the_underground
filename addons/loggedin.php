<?php


$member = $db->row("SELECT * FROM members WHERE usercode = :uc AND validated = 'y'",array("uc"=>$_SESSION['active']));
////////////  MODIFY MENU SYSTEM TO SHOW LOGGED IN
$array['hometext'] = 'Dashboard';
$array['hometext2'] = 'Dashboard';
$array['pagelist'] = '';


//////////// BOOT IF NOT A VAL USER
if($member['id'] == 0)
{
setcookie('active', '', strtotime("-3 months"), '/');
unset($_SESSION['active']);
unset($_SESSION['userid']);
header("Location: ".$array['rooturl']);
exit;
}


/// MARK AS MODERATOR
$moderator = ($memberdata['id'] == $sysadminid) ? 'y': 'n';


//////////// UPDATE TIMES FOR NEW SESSION
if($newsession == 'y')
{
$db->query("UPDATE members SET currentlogin = :time, lastonline = :oldtime WHERE id = :id", array("time"=>$time,"oldtime"=>$member['currentlogin'],"id"=>$member['id']),PDO::FETCH_ASSOC,"n");
}


//////////////////////////////   COUNT MAIL
$nmc = $db->query("SELECT id FROM pm WHERE `to` = :to AND `read` = 'n' AND delto != 'y' GROUP BY `from`",array("to"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');
$mailcount = ($nmc > 0) ? '<span class="notify'.$mobilemod.'">'.$nmc.'</span>' : '';
$mtitletext = ($nmc > 0) ? ' ('.$nmc.' New)' : '';

//////////////////////////////   COUNT NOTIFICATIONS
$nc = $db->query("SELECT id FROM notifications WHERE owner = :to AND `read` = 'n'",array("to"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');
$notifycount = ($nc > 0) ? '<span class="notify'.$mobilemod.'">'.$nc.'</span>' : '';
$ntitletext = ($nc > 0) ? ' ('.$nc.' New)' : '';



////////////////////////// SET SOME EXTRA VARIABLES
$array['scripts'] .= 'function vote(id,type,vote)
{
  $.post(\'phpfiles/actions.php?action=vote\',{\'id\': id,\'type\':type}, function(data){
	$("#" + type + "-" + id).html(data);
	loading = false;
})
}
function comment(id,type,comment)
{
    $.post(\'phpfiles/actions.php?action=comment\',{\'id\': id,\'type\':type,\'comment\':comment}, function(data){
	$("#" + type + "-" + id + "-comment").html(data);
	loading = false;
})
}
function ClearComment(id) {
$(".cnum" + id).html(parseInt($(".cnum" + id).html(), 10)+1)
     document.getElementById("comment" + id).value = "";
}
';







/// MOBILE VERSION
if($mobilemod == '-mobile')
{
$array['loggedinsection'] = '<a href="../?mod=myhome&file=settings"><span class="menuitem menured">ACCOUNT SETTINGS</span></a>
<a href="../?mod=logout"><span class="menuitem menured">LOGOUT</span></a>';
$array['membersection'] = '<a href="../"><span class="menuitem menured">DASHBOARD</span></a>
<a href="../?mod=myhome&file=mail"><span class="menuitem menured">MESSAGES'.$mailcount.'</span></a>
<a href="../?mod=myhome&file=notifications"><span class="menuitem menured">NOTIFICATIONS'.$notifycount.'</span></a>';



}



///// FULL VERSION
else
{
$array['loggedinsection'] = '
<a href="../?mod=logout"><span class="menuitem menured">Logout</span></a>
<a href="../?mod=myhome&file=settings"><span class="menuitem menured">Settings</span></a>
<a href="../?mod=myhome&file=notifications"><span class="menuitem menured">Notifications'.$notifycount.'</span></a>
<a href="../?mod=myhome&file=mail"><span class="menuitem menured">Mail'.$mailcount.'</span></a>';


/// 250 WE LAST AD
$a = $db->row("SELECT * FROM ads WHERE type = :t AND active = 'y' ORDER BY rand() LIMIT 1",array("t"=>'250wide'));
$array['lastrightad'] = '<div id="frozen"><a href="'.$a['link'].'" rel="nofollow"><img border="0" src="'.$staticads.'/images/ads/'.$a['id'].'.'.$a['ext'].'" width="'.$a['display_x'].'" height="'.$a['display_y'].'" alt=""/></a>

<div class="space30"></div>
<h2>Site Search</h2>
<form method="get" action="'.$rooturl.'">
<input name="q" type="text" class="formfield width100" id="q" value="" placeholder="Search Here" autocorrect="off"  autocomplete="off"   />
<input type="submit" class="button" value="Search" />
</form>

</div>';


}
