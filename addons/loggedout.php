<?php

////////////  MODIFY MENU SYSTEM TO SHOW LOGGED IN
$array['hometext'] = 'Home';
$array['hometext2'] = 'Home';
$array['membersection'] = '';
$array['loggedinsection'] = '<a href="../?mod=login"><span class="menuitem menured">LOGIN</span></a>
<a href="../?mod=register"><span class="menuitem menured">REGISTER</span></a>';


///// GET SOME PAGES FOR THE BOTTOM
$setting = $db->row("SELECT value FROM site_settings WHERE `name` = 'page list'");
$array['pagelist'] = $setting['value'];


/// NOT MOBILE VERSION
if($mobilemod == '')
{
/// 250 WIDE LAST AD
$a = $db->row("SELECT * FROM ads WHERE type = :t AND active = 'y' ORDER BY rand() LIMIT 1",array("t"=>'250wide'));
$array['lastrightad'] = '<div id="frozen"><a href="'.$a['link'].'" rel="nofollow"><img border="0" src="'.$staticads.'/images/ads/'.$a['id'].'.'.$a['ext'].'" width="'.$a['display_x'].'" height="'.$a['display_y'].'" alt=""/></a>

<div class="space30"></div>
<h2>Site Search</h2>
<form method="get" action="'.$rooturl.'">
<input name="q" type="text" class="formfield width100" id="q" value="" placeholder="Search Here" autocorrect="off"  autocomplete="off"   />
<input type="submit" class="button" value="Search" />
</form>

</div>

';
}
