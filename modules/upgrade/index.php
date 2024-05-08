<?php
if(!isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']."");
exit;
}
$array['pagetitle'] = 'Upgrade to Premium';
$page->page .= $page->get_temp('templates/upgrade/index2.htm');

$array['chatbar'] = '';

//// REDIRECT COMPLETED
if($_GET['action'] == 'complete')
{
header('Location: '.$array['rooturl'].'/?mod=upgrade&file=complete');
exit;
}


$array['cellpadding'] = ($mobilemod == '') ? '10': '5';



$array['breadcrumb'] = $short->breadcrumb('Upgrade Account',$array['ogurl']);

///////// RETURN MESSAGES
if($_GET['action'] == 'return')
{
$array['errormessage'] = $short->message('Payment Process Cancelled','y');
}





/////////////////////////    WHAT TO SHOW IF ALREADY UPGRADED
if($showads == 'n')
{
$exttext = ' Extension';
$array['title'] = 'Extend your Membership';
$array['title2'] = 'Choose Your Extension';
$array['options'] = '<option value="7">7 Day Premium Membership Extension: $29</option>
<option value="30">30 Day Premium Membership Extension: $79</option>
<option value="60">60 Day Premium Membership Extension: $129 (Most Popular)</option>
<option value="120">120 Day Premium Membership Extension: $199</option>
<option value="365">1 Year Premium Membership Extension: $299</option>';
}


/////  NOT A CURRENT PREMIUM MEMBER
else
{
$exttext = '';
$array['title'] = 'Upgrade to Premium';
$array['title2'] = 'Choose Your Upgrade';
$array['options'] = '<option value="7">7 Day Premium Membership: $29 USD</option>
<option value="30">30 Day Premium Membership: $79 USD</option>
<option value="60">60 Day Premium Membership: $129 USD (Most Popular)</option>
<option value="120">120 Day Premium Membership: $199 USD</option>
<option value="365">1 Year Premium Membership: $299 USD</option>';
}








if(isset($_POST['button']))
{
if($_POST['period'] == '7'){$amt = '29';$pro = '7 Day';}
else if($_POST['period'] == '30'){$amt = '79';$pro = '30 Day';}
else if($_POST['period'] == '60'){$amt = '129';$pro = '60 Day';}
else if($_POST['period'] == '120'){$amt = '199';$pro = '120 Day';}
else{$amt = '299';$pro = '1 Year';}

$notify = urlencode($array['rooturl']."/phpfiles/paypal.php");
$return = urlencode($array['rooturl']."/?mod=upgrade&action=return");
$complete = urlencode($array['rooturl']."/?mod=upgrade&action=complete");
$gotourl = "https://www.paypal.com/cgi-bin/webscr?"
."cmd=_xclick"
."&business=info@ryridge.com"
."&item_name=".$pro." Premium Membership".$exttext
."&amount=".$amt.""
."&notify_url=".$notify.""
."&custom=".$member['id']."-".$_POST['period'].""
."&no_shipping=1"
."&no_note=1"
."&currency_code=USD"
."&page_style=theusc"
."&bn=PP-BuyNowBF"
."&return=".$complete.""
."&cancel_return=".$return."";

header('Location: '.$gotourl.'');
exit;
}
