<?php
/// DECLARE MAIL USE CLASS
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(!isset($_SESSION['userid']))
{
header("Location:".$array['rooturl']."/?mod=login");
exit;
}
include(''.$serverpath.'/addons/form_validation.php');
$array['pagetitle'] = 'Add/Edit Information';
$array['pagedescription'] = '';
$page->page .= $page->get_temp('templates/myhome/info.htm');

//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Edit Profile Information',2);



$array['em1'] = '';
$array['em2'] = '';
$array['a1'] = '';
$array['a2'] = '';

foreach($member as $key => $value)
{
$array[$key] = stripslashes($short->clean($value));
}
$uid = $array['id'];









if(isset($_POST['button']))
{

foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}
/////////////////////
$ok = 'y';


///////////////////// CHECK PW
if($post['password'] == '')
{
$post['password'] = $member['password'];
}
else
{
$post['password'] = $short->password($_POST['password']);
}
///////////////////// CHECK EMAIL
$check = $db->query("SELECT id FROM members WHERE `email` = :em AND id != :id limit 1",array("em"=>$post['email'],"id"=>$_SESSION['userid']),PDO::FETCH_NUM,'y');
if($check > 0)
{
$ok = 'n';$array['em1'] = 'error';	$array['em2'] = 'is Already in Use';
}
if($post['email'] == '')
{
$ok = 'n';$array['em1'] = 'error';	$array['em2'] = 'is Required';
}
if($ok == 'y')
{
$newemail = '';
$usercode = $member['usercode'];
$emailchange = '';
if($member['email'] != $post['email'])
{
$usercode = $short->createusercode();
$_SESSION['active'] = $usercode;
$newemail = $post['email'];
$emailchange = 'y';
///////////// MESSAGE USER
$heading = 'Email Verification';
$message = 'Hello '.$post['username'].',<br/><br/>

You recently changed your email address.<br/><br/>

To validate your new email address, please click the following link:<br/><br/>

<a href="'.$array['rooturl'].'/?validate='.$usercode.'">'.$array['rooturl'].'/?validate='.$usercode.'</a><br/><br/>

If the above link does not hyperlink, simply copy and paste it into your browser.<br/><br/>

Thanks';
//header
//// HTML
$emailbody = $page->get_temp(''.$serverpath.'/templates/main/email.htm');
$emailbody = str_replace("{body}", $message, $emailbody);
$emailbody = str_replace("{heading}", $heading, $emailbody);
// TEXT ONLY EMAIL
$textbody = str_replace("<br/>","",$message);
//// SEND
require_once ''.$serverpath.'/phpmailer6/src/Exception.php';
require_once ''.$serverpath.'/phpmailer6/src/PHPMailer.php';
require_once ''.$serverpath.'/phpmailer6/src/SMTP.php';
try {
$mail = new PHPMailer(true);
//$mail->SMTPDebug = 3;
$mail->isSMTP();
$mail->Host = 'mail.'.$domainonly.'';
$mail->SMTPAuth = true;
$mail->Username = 'notifications@'.$domainonly.'';
$mail->Password = $emailpassword;
$mail->SMTPSecure = 'ssl';
$mail->SMTPOptions = array('ssl' => array('verify_peer' => false,'verify_peer_name' => false,'allow_self_signed' => true));
$mail->Port = 465;
$mail->setFrom('notifications@'.$domainonly.'', 'The USC');
$mail->addReplyTo('notifications@'.$domainonly.'', 'The USC');
$mail->addAddress($post['email']);
$mail->isHTML(true);
$mail->Subject = $heading;
$mail->Body    = $emailbody;
$mail->AltBody = $textbody;
$mail->send();
} catch (Exception $e) {
@mail($adminemailaddress, 'Send Mail Error: Forgot Password', 'Error: '.$mail->ErrorInfo, "From: ".$adminemailaddress);
}


}



/// DELETE SPAMMERS ABOUT INFO
$not1 = 'hixdat';
$not2 = 'thisissparespamtext';
$not3 = 'thisissparespamtext';
if(strpos($post['about'], $not1) !== false ||
strpos($post['about'], $not2) !== false ||
strpos($post['about'], $not3) !== false)
{
$short->deletemember($_SESSION['userid'],'y');
header("Location:".$array['rooturl']."/?mod=logout");
exit;
}





$db->query("UPDATE members SET usercode = :uc, sex = :sex, country = :c, town = :t, newemail = :ne, sex_relstatus = :srs, sex_pref = :sp, sex_position = :spos, sex_lookingfor = :slf, sex_freq = :sf, password = :pw, about = :a WHERE id = :id",

array("uc"=>$usercode,"sex"=>$post['sex'],"c"=>$post['country'],"t"=>$post['town'],"ne"=>$newemail,"srs"=>$post['sex_relstatus'],"sp"=>$post['sex_pref'],"spos"=>$post['sex_position'],"slf"=>$post['sex_lookingfor'],"sf"=>$post['sex_freq'],"pw"=>$post['password'],"a"=>$post['about'],"id"=>$_SESSION['userid']),PDO::FETCH_ASSOC,"n");



////////////// EMAIL CHANGE
if($emailchange == 'y')
{
$_SESSION['gmessage'] = 'Information Updated &middot; Email Verification Required';
}
else
{
$_SESSION['gmessage'] = 'Information Updated Successfully'.$test;
}

header("Location:".$array['rooturl']."/?mod=myhome&file=info");
exit;

}


}// END POST















$array['new1'] = '<!--';
$array['new2'] = '-->';
if ($array['newemail'] != '')
{
$array['new1'] = '';
$array['new2'] = '';
}




$array['sex'] = $form->select_post_fix('Male,Female', 'Male,Female', $array['sex']);


$st = 'Not Specified,Single,In a Relationship,In an Open Relationship,Soft Swinging,Full Swinging';
$array['sex_relstatus'] = $form->select_post_fix($st,$st, $array['sex_relstatus']);
$st = 'Not Specified,Straight,Gay,Bi-Sexual';
$array['sex_pref'] = $form->select_post_fix($st,$st, $array['sex_pref']);
$st = 'Not Specified,Once Per Year,Once Per Month,Once Per Week,Twice Per Week,Three Times Per Week,Once Per Day,Twice Per Day,3 Times Per Day,5 Times Per Day,More Than 5 Times Per Day';
$array['sex_freq'] = $form->select_post_fix($st,$st, $array['sex_freq']);
//// COUNTRY
$ls = "Afghanistan,&Aring;land Islands,Albania,Algeria,American Samoa,Andorra,Angola,Anguilla,Antarctica,Antigua and Barbuda,Argentina,Armenia,Aruba,Australia,Austria,Azerbaijan,Bahamas,Bahrain,Bangladesh,Barbados,Belarus,Belgium,Belize,Benin,Bermuda,Bhutan,Bolivia,Bosnia and Herzegovina,Botswana,Bouvet Island,Brazil,British Indian Ocean territory,Brunei Darussalam,Bulgaria,Burkina Faso,Burundi,Cambodia,Cameroon,Canada,Cape Verde,Cayman Islands,Central African Republic,Chad,Chile,China,Christmas Island,Cocos (Keeling) Islands,Colombia,Comoros,Congo,Congo, Democratic Republic,Cook Islands,Costa Rica,C&ocirc;te d'Ivoire (Ivory Coast),Croatia (Hrvatska),Cuba,Cyprus,Czech Republic,Denmark,Djibouti,Dominica,Dominican Republic,East Timor,Ecuador,Egypt,El Salvador,Equatorial Guinea,Eritrea,Estonia,Ethiopia,Falkland Islands,Faroe Islands,Fiji,Finland,France,French Guiana,French Polynesia,French Southern Territories,Gabon,Gambia,Georgia,Germany,Ghana,Gibraltar,Greece,Greenland,Grenada,Guadeloupe,Guam,Guatemala,Guinea,Guinea-Bissau,Guyana,Haiti,Heard and McDonald Islands,Honduras,Hong Kong,Hungary,Iceland,India,Indonesia,Iran,Iraq,Ireland,Israel,Italy,Jamaica,Japan,Jordan,Kazakhstan,Kenya,Kiribati,Korea (north),Korea (south),Kuwait,Kyrgyzstan,Lao People's Democratic Republic,Latvia,Lebanon,Lesotho,Liberia,Libyan Arab Jamahiriya,Liechtenstein,Lithuania,Luxembourg,Macao,Madagascar,Malawi,Malaysia,Maldives,Mali,Malta,Marshall Islands,Martinique,Mauritania,Mauritius,Mayotte,Mexico,Micronesia,Moldova,Monaco,Mongolia,Montenegro,Montserrat,Morocco,Mozambique,Myanmar,Namibia,Nauru,Nepal,Netherlands,Netherlands Antilles,New Caledonia,New Zealand,Nicaragua,Niger,Nigeria,Niue,Norfolk Island,Northern Mariana Islands,Norway,Oman,Pakistan,Palau,Palestinian Territories,Panama,Papua New Guinea,Paraguay,Peru,Philippines,Pitcairn,Poland,Portugal,Puerto Rico,Qatar,R&eacute;union,Romania,Russian Federation,Rwanda,Saint Helena,Saint Kitts and Nevis,Saint Lucia,Saint Pierre and Miquelon,Saint Vincent and the Grenadines,Samoa,San Marino,Sao Tome and Principe,Saudi Arabia,Senegal,Serbia,Seychelles,Sierra Leone,Singapore,Slovakia,Slovenia,Solomon Islands,Somalia,South Africa,Spain,Sri Lanka,Sudan,Suriname,Svalbard and Jan Mayen Islands,Swaziland,Sweden,Switzerland,Syria,Taiwan,Tajikistan,Tanzania,Thailand,Togo,Tokelau,Tonga,Trinidad and Tobago,Tunisia,Turkey,Turkmenistan,Turks and Caicos Islands,Tuvalu,Uganda,Ukraine,United Arab Emirates,United Kingdom,United States of America,Uruguay,Uzbekistan,Vanuatu,Vatican City,Venezuela,Vietnam,Virgin Islands (British),Virgin Islands (US),Wallis and Futuna Islands,Western Sahara,Yemen,Zaire,Zambia,Zimbabwe";
$array['countries'] = $form->select_post_fix($ls,$ls, $array['country']);
