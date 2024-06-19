<?php

$array['pagetitle'] = 'Validation';
$page->page .= $page->get_temp('templates/register/validate.htm');

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=register','Member Registration',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Email Validation',3);

$array['c1'] = '';
$array['c2'] = '';
$array['code'] = '';
$array['additional'] = '';

//////////// GET CODE AND POPULATE FROM URL IF IT EXISTS
if(isset($_GET['validate']))
{
$array['code'] = $_GET['validate'];
}



$user = $db->row("SELECT * FROM members WHERE usercode = :u",array("u"=>$array['code']));


/////////  IF VALidATING NEW EMAIL ADDRESS
if($user['newemail'] != '')
{
$array['subheading'] = 'Validate Your New Email';
$array['text'] = 'Please enter your validation code below.';


///////////////////////////////////////////  RUNCE ONCE SUBMITTED
if(isset($_POST['button']))
{
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}
$array['code'] = $post['code'];
$code = $post['code'];

/////// GET MEMBER FROM DATABASE IF IT EXISTS

/////////// ERROR IF INCORRECT CODE
if($user['id'] == 0)
{
$array['c1'] = 'error';	$array['c2'] = 'is Not a Valid Code';
}
else////////// ELSE VALidATE AND SEND TO LOGIN
{
$db->query("UPDATE members SET email = :e, newemail = '' WHERE id = :id", array("e"=>$user['newemail'],"id"=>$user['id']),PDO::FETCH_ASSOC,"n");
$_SESSION['gmessage'] = 'New Email Validated';
header("Location:".$array['rooturl']."");
exit;
}

}


}



//////  ELSE VALidATING NEW ACCOUNT
else
{
$array['subheading'] = 'Check Your Emails Now';
$array['text'] = 'We have sent an email to you containing a validation code. Please enter the code below.';



///////////////////////////////////////////  RUNCE ONCE SUBMITTED
if(isset($_POST['button']))
{
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}
$array['code'] = $post['code'];
$code = $post['code'];

/////// GET MEMBER FROM DATABASE IF IT EXISTS
$memberdata = $db->row("SELECT id,validated FROM members WHERE usercode = :uc",array("uc"=>$code));

/////////// ERROR IF INCORRECT CODE
if($memberdata['id'] == 0)
{
$array['c1'] = 'error';	$array['c2'] = 'is Not a Valid Code';
}
else////////// ELSE VALidATE AND SEND TO LOGIN
{
/////  INSERT NEWS IF NOT ALREADY EXISTING
if($memberdata['validated'] == 'n')
{
$db->query("INSERT INTO news(owner,itemid,type,stamp) VALUES(:u,:item,:type,:st)",
array("u"=>$memberdata['id'],"item"=>'0',"type"=>'registered',"st"=>$time),PDO::FETCH_ASSOC,"n");
}
$db->query("UPDATE members SET validated = :y WHERE id = :id", array("y"=>'y',"id"=>$memberdata['id']),PDO::FETCH_ASSOC,"n");

///
$_SESSION['gmessage'] = 'Email Validated &middot; You can now login';
header("Location:".$array['rooturl']."/?mod=login&action=validated");
exit;
}

}



}
