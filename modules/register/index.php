<?php


/// DECLARE MAIL USE CLASS
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;




if(isset($_SESSION['userid']))
{
header("Location: ".$array['rooturl']);
exit;
}
include(''.$serverpath.'/addons/form_validation.php');
$array['pagetitle'] = 'Registration';
$array['pagedescription'] = 'Register to be a member on the Underground Sex Club. Members contact other members for free.';
$page->page .= $page->get_temp('templates/register/index.htm');


//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Free Member Registration',2);


/////////////////////////////////////////////////////////
$array['un1'] = '';
$array['un2'] = '';
$array['em1'] = '';
$array['em2'] = '';
$array['pa1'] = '';
$array['pas1'] = '';
$array['pa2'] = '';
$array['pas2'] = '';
$array['db1'] = '';
$array['db2'] = '';
$array['c1'] = '';
$array['c2'] = '';
$post['day'] = 'd';
$post['month'] = 'd';
$post['year'] = 'd';
$post['sex'] = 'Male';
$post['country'] = '';
$array['username'] = '';
$array['email'] = '';
$array['password'] = '';
$array['bottomad'] = '';




//////////////////////////////////////   MOBILE TOP IMAGES
$query = $db->query("SELECT * FROM fakemembers ORDER BY rand() LIMIT 7",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$fake .= '<td class="one7th">
      <span class="minus2around"><img src="'.$array['rooturl'].'/images/users/'.$data['id'].'.jpg" class="width100" alt=""/>
	  </span>
      </td>';
}
$array['mimages'] .= '<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tbody><tr>'.$fake.'</tr>
  </tbody>
</table><div class="space10"></div>';















//////////////////////////////////////////////////////////////// POST
if(isset($_POST['username']))
{
foreach($_POST as $key => $value)
{
$value = trim($value);
$post[$key] = $value;
}
$array['username'] = $_POST['username'];
$array['email'] = $_POST['email'];
$array['password'] = $_POST['password'];
$ok = 'y';



////  CHECK SPAMMERS USERNAME
if(strlen($post['username']) == 16)
{
$lettersonly = substr($post['username'], 0, 12);
$numbersonly = substr($post['username'], 12);
}





/////////////////////// CHECK USERNAME
$check = $db->query("SELECT id FROM members WHERE `username` = :un AND `email` != :em limit 1",array("un"=>$post['username'],"em"=>$post['email']),PDO::FETCH_NUM,'y');
if($check > 0)
{
$ok = 'n';$array['un1'] = 'error';	$array['un2'] = 'is Already in Use - Please Try Another Username';
}
/// NUMBERS AND LETTERS ONLY
$aValid = array('-', '_');
if(!ctype_alnum(str_replace($aValid, '', $post['username']))) {
$ok = 'n';$array['un1'] = 'error';	$array['un2'] = 'Can Only Contain Numbers & Letters';
}
if($post['username'] == '')
{
$ok = 'n';$array['un1'] = 'error';	$array['un2'] = 'is Required';
}



////// CHECK USERNAME FOR BLACKLIST WORDS
$query= $db->query("SELECT * FROM s_blacklist_words",null,PDO::FETCH_ASSOC,"n");
$ban = 'n';
foreach($query as $data)
{
if(strpos(' '.$post['username'], $data['word'])) {$ban = 'y';}
}
//////  IF FAILED THE BAN, BLACKLIST NOW
if($ban == 'y')
{
$ok = 'n';
/// EMAIL ADMIN THE BANNED USERNAME
@mail($adminemailaddress, 'Banned Username', $post['username'], "From: ".$adminemailaddress);
/// SEND TO SPAM
$banredirect =  "https://www.theundergroundsexclub.com/link.php?b=asstok&c=black";
header("Location: ".$banredirect);
exit;
}





//////  NORMALISE EMAIL
list($localemail,$domainemail) = explode("@", $post['email']);
$domainemail = strtolower($domainemail);
$localemailwdots = $localemail;
$localemail = str_replace('.', '', $localemail);
$nodotemail = $localemail.'@'.$domainemail;


///////////////////// CHECK EMAIL
$check = $db->query("SELECT id FROM members WHERE `email` = :em AND country != '' AND validated = 'y' limit 1", array("em"=>$post['email']),PDO::FETCH_NUM,'y');
if($check > 0)
{
$ok = 'n';$array['em1'] = 'error';	$array['em2'] = 'is Already in Use';
}
if($post['email'] == '')
{
$ok = 'n';$array['em1'] = 'error';	$array['em2'] = 'is Required';
}

//////////  BAN SPECIFIC SPAMMER
if($ok == 'y')
{
if(strpos(' '.$domainemail.' ', 'cashbenties') || strpos(' '.$domainemail.' ', 'crankymonkey') || strpos(' '.$domainemail.' ', 'cse445'))
{
$ok = 'n';
//////// REDIRECT BANNED PEOPLE
@mail($adminemailaddress, 'Banned Domain', $post['email'].' + '.$nodotemail, "From: ".$adminemailaddress);
$banredirect =  "https://www.theundergroundsexclub.com/link.php?b=asstok&c=black";
header("Location: ".$banredirect);
exit;
}
}



//////////////////  BLACK LIST DOMAINS
if($ok == 'y')
{
$check = $db->query("SELECT id FROM s_blacklist_domains WHERE domain = :d limit 1",array("d"=>$domainemail),PDO::FETCH_NUM,'y');
if($check > 0)
{
$ok = 'n';
//////// REDIRECT BANNED PEOPLE
@mail($adminemailaddress, 'Banned Domain', $post['email'].' + '.$nodotemail, "From: ".$adminemailaddress);
$banredirect =  "https://www.theundergroundsexclub.com/link.php?b=asstok&c=black";
header("Location: ".$banredirect);
exit;
}
}





//////////////////  BLACK LIST EMAIL
if($ok == 'y')
{
$check = $db->query("SELECT id FROM s_blacklist WHERE `email` = :em limit 1",array("em"=>$nodotemail),PDO::FETCH_NUM,'y');
if($check > 0)
{
$ok = 'n';
//////// REDIRECT BANNED PEOPLE
@mail($adminemailaddress, 'Banned Email', $post['email'].' + '.$nodotemail, "From: ".$adminemailaddress);
$banredirect =  "https://www.theundergroundsexclub.com/link.php?b=asstok&c=black";
header("Location: ".$banredirect);
exit;
}
}



//////////////////  REDIRECT EMAILS WITH 2 OR MORE DOTS
if($ok == 'y')
{
$dotcount = substr_count($localemailwdots,".");
if($dotcount > 1)
{
$ok = 'n';
//////// REDIRECT BANNED PEOPLE
@mail($adminemailaddress, 'Banned Email', $post['email'], "From: ".$adminemailaddress);
$banredirect =  "https://www.theundergroundsexclub.com/link.php?b=asstok&c=black";
header("Location: ".$banredirect);
exit;
}
}



///////////////  REDIRECT OUTLOOK USERS USING A PLUS SIGN - SPECIFIC SPAMMER
if($domainemail == 'outlook.com')
{
if (strpos($localemail, '+') !== false)
{
$ok = 'n';
//////// REDIRECT BANNED PEOPLE
@mail($adminemailaddress, 'Banned Email', $post['email'], "From: ".$adminemailaddress);
$banredirect =  "https://www.theundergroundsexclub.com/link.php?b=asstok&c=black";
header("Location: ".$banredirect);
exit;
}
}




//////////////////// CHECK PASSWORD 1
if(strlen($post['password']) < 4)
{
$ok = 'n';$array['pa1'] = 'error';	$array['pa2'] = 'is Less than 4 Characters';
}
if($post['password'] == '')
{
$ok = 'n';$array['pa1'] = 'error';	$array['pa2'] = 'is Required';
}



//////////////////////// CHECK AGE
$dob = $post['year'].'-'.$post['month'].'-'.$post['day'];
$d = DateTime::createFromFormat('Y-n-j', $dob);
if($d->format('Y-n-j') != $dob)
{
$ok = 'n';$array['db1'] = 'error';  $array['db2'] = 'is Not a Valid Date';
}
if($short->age($dob) < 18)
{
$ok = 'n';$array['db1'] = 'error';  $array['db2'] = 'is Too Young - Must be over 18';
}
if($post['day'] == '00' || $post['month'] == '00' || $post['year'] == '0000')
{
$ok = 'n';$array['db1'] = 'error';	$array['db2'] = 'all Fields are Required';
}




///////////////////// CHECK COUNTRY
if($post['country'] == '' or $post['country'] == '------')
{
$ok = 'n';$array['c1'] = 'error';	$array['c2'] = 'is Required';
}



///////////////////////////////////////// ALL OK
if($ok == 'y')
{

//// DELETE EXISTING HALF REGISTRATIONS
$db->query("DELETE FROM members WHERE `email` = :email", array("email"=>$post['email']),PDO::FETCH_ASSOC,"n");

///// ENCRYPE PASSWORD
$password = $short->password($_POST['password']);
/// CREATE USERCODE
$usercode = $short->createusercode();
////  GET TOWN AND ZIP
list($zip,$town) = explode("|", $short->zipfromip($userip));
$zip = ($zip == '') ? 'na': $zip;
////////////////////////////// ADD USER
$insert = $db->query("INSERT INTO members(username,email,password,regdate,regip,dob_date,sex,country,usercode,zipcode,town) VALUES(:un,:em,:pw,:rd,:ip,:dob,:sex,:c,:uc,:z,:t)",
array("un"=>$post['username'],"em"=>$post['email'],"pw"=>$password,"rd"=>$time,"ip"=>$userip,"dob"=>$dob,"sex"=>$post['sex'],"c"=>$post['country'],"uc"=>$usercode,"z"=>$zip,"t"=>$town),PDO::FETCH_ASSOC,"n");

$userid = $db->lastInsertId();


///
$plat = ($mobilemod == '') ? 'desktop': 'mobile';
/// INSERT FLING REGISTRATION
$insert = $db->query("INSERT INTO s_api_registrations(site,userid,company,platform,stamp,pw,seeking) VALUES(:st,:u,:c,:p,:s,:pw,:seek)",
 array("st"=>$domainonly,"u"=>$userid,"c"=>'fling',"p"=>$plat,"s"=>$time,"pw"=>$_POST['password'],"seek"=>$post['lookingfor']),PDO::FETCH_ASSOC,"n");


////////////////////////// SEND CONFIRM EMAIL
$message = 'Hello '.$post['username'].',<br/><br/>

Thankyou for registering with The Underground Sex Club.<br/><br/>

To activate your account, please enter your validation code on the validation page within 24 hours.<br/><br/>

Validation Code: '.$usercode.'<br/>
Validation Page: <a href="'.$array['rooturl'].'/?validate='.$usercode.'">'.$array['rooturl'].'/?validate='.$usercode.'</a><br/><br/>

If the above link does not hyperlink, simply copy and paste it into your browser.<br/><br/>

Thank you';
//SEND
$heading = 'Email Verification';

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
@mail($adminemailaddress, ''.$userip.' - Send Mail Error: Registration', 'Error: '.$mail->ErrorInfo, "From: ".$adminemailaddress);
}





////  SETUP REDIRECT LINK
$redirlink =  $array['rooturl']."/?mod=register&file=validate";



//////// REDIRECT
header("Location: ".$redirlink);
exit;
}
}












/////////////  POPULATE SELECTIONS
// DATES DAYS
for ($i = 1; $i <= 31; $i++) {
	$selected = ($post['day'] == $i) ? 'selected': '';
	$array['day'] .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
}
// DATES MONTHS
for ($i = 1; $i <= 12; ++$i) {
	$selected = ($post['month'] == $i) ? 'selected': '';
	$v = date('n', mktime(0,0,0,$i, 1, date('Y')));
	$l = date('M', mktime(0,0,0,$i, 1, date('Y')));
	$array['month'] .= '<option value="'.$v.'" '.$selected.'>'.$l.'</option>';
  }
// DATES YEARS
for ($i = 80; $i >= 0; --$i) {
	$mtime = strtotime("-$i years", time());
	$v = date('Y', $mtime);
	$selected = ($post['year'] == $v) ? 'selected': '';
	$array['year'] .= '<option value="'.$v.'" '.$selected.'>'.$v.'</option>';
  }



$array['sex'] = $form->select_post_fix('Male,Female', 'Male,Female', $post['sex']);
//// COUNTRY
$ls = "United States of America,Australia,United Kingdom,------,Afghanistan,&Aring;land Islands,Albania,Algeria,American Samoa,Andorra,Angola,Anguilla,Antarctica,Antigua and Barbuda,Argentina,Armenia,Aruba,Australia,Austria,Azerbaijan,Bahamas,Bahrain,Bangladesh,Barbados,Belarus,Belgium,Belize,Benin,Bermuda,Bhutan,Bolivia,Bosnia and Herzegovina,Botswana,Bouvet Island,Brazil,British Indian Ocean territory,Brunei Darussalam,Bulgaria,Burkina Faso,Burundi,Cambodia,Cameroon,Canada,Cape Verde,Cayman Islands,Central African Republic,Chad,Chile,China,Christmas Island,Cocos (Keeling) Islands,Colombia,Comoros,Congo,Congo, Democratic Republic,Cook Islands,Costa Rica,C&ocirc;te d'Ivoire (Ivory Coast),Croatia (Hrvatska),Cuba,Cyprus,Czech Republic,Denmark,Djibouti,Dominica,Dominican Republic,East Timor,Ecuador,Egypt,El Salvador,Equatorial Guinea,Eritrea,Estonia,Ethiopia,Falkland Islands,Faroe Islands,Fiji,Finland,France,French Guiana,French Polynesia,French Southern Territories,Gabon,Gambia,Georgia,Germany,Ghana,Gibraltar,Greece,Greenland,Grenada,Guadeloupe,Guam,Guatemala,Guinea,Guinea Bissau,Guyana,Haiti,Heard and McDonald Islands,Honduras,Hong Kong,Hungary,Iceland,India,Indonesia,Iran,Iraq,Ireland,Israel,Italy,Jamaica,Japan,Jordan,Kazakhstan,Kenya,Kiribati,Korea (north),Korea (south),Kuwait,Kyrgyzstan,Lao People's Democratic Republic,Latvia,Lebanon,Lesotho,Liberia,Libyan Arab Jamahiriya,Liechtenstein,Lithuania,Luxembourg,Macao,Madagascar,Malawi,Malaysia,Maldives,Mali,Malta,Marshall Islands,Martinique,Mauritania,Mauritius,Mayotte,Mexico,Micronesia,Moldova,Monaco,Mongolia,Montenegro,Montserrat,Morocco,Mozambique,Myanmar,Namibia,Nauru,Nepal,Netherlands,Netherlands Antilles,New Caledonia,New Zealand,Nicaragua,Niger,Nigeria,Niue,Norfolk Island,Northern Mariana Islands,Norway,Oman,Pakistan,Palau,Palestinian Territories,Panama,Papua New Guinea,Paraguay,Peru,Philippines,Pitcairn,Poland,Portugal,Puerto Rico,Qatar,R&eacute;union,Romania,Russian Federation,Rwanda,Saint Helena,Saint Kitts and Nevis,Saint Lucia,Saint Pierre and Miquelon,Saint Vincent and the Grenadines,Samoa,San Marino,Sao Tome and Principe,Saudi Arabia,Senegal,Serbia,Seychelles,Sierra Leone,Singapore,Slovakia,Slovenia,Solomon Islands,Somalia,South Africa,Spain,Sri Lanka,Sudan,Suriname,Svalbard and Jan Mayen Islands,Swaziland,Sweden,Switzerland,Syria,Taiwan,Tajikistan,Tanzania,Thailand,Togo,Tokelau,Tonga,Trinidad and Tobago,Tunisia,Turkey,Turkmenistan,Turks and Caicos Islands,Tuvalu,Uganda,Ukraine,United Arab Emirates,United Kingdom,United States of America,Uruguay,Uzbekistan,Vanuatu,Vatican City,Venezuela,Vietnam,Virgin Islands (British),Virgin Islands (US),Wallis and Futuna Islands,Western Sahara,Yemen,Zaire,Zambia,Zimbabwe";
$array['countries'] = $form->select_post_fix($ls,$ls, $post['country']);
