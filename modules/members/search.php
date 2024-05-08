<?php

/// TITLE TAGS SET
$sexpreftitle = '';
$sextitle = 'Members';
$countrytitle = '';
$agetitle = '';
$sexstatustitle = '';
$profilepictitle = '';


$page->page .= $page->get_temp('templates/members/search.htm');
include(''.$serverpath.'/addons/form_validation.php');




if($_GET['photo'] == '1'){$array['pic'] = 'checked="checked"';}


//////////////////////////////////////////////////////////////////////////////// RESULTS
$sql = '';
foreach($_GET as $key => $value)
{
$value = addslashes($value);
$value = trim($value);
$post[$key] = $value;
}
///// COUNTRY
if(!empty($post['country']))
{
$sql .= "country = '{$post['country']}' AND ";
$countrytitle = ' from '.$post['country'].'';
}
////// SEX
if($post['sex'] != 'Any' & $post['sex'] != '')
{
$sql .= "sex LIKE '{$post['sex']}' AND ";
$sextitle = ($post['sex'] == 'Male') ? 'Men' : 'Women';
}
//// AGE
$array['fromage'] = $_GET['from'];
if ($post['from'] == 'From Age')
{
$post['from'] = '';
}
if ($post['to'] == 'To Age')
{
$array['toage'] = 'To Age';
$post['to'] = '';
}
if(!empty($post['from']) && !empty($post['to']))
{
$from = date("Y") - $post['from'];
$to = date("Y") - $post['to'];
for($x = $to; $x <= $from; $x++)
{
$sql2 .= "dob_date LIKE '%$x%' OR ";
}
if(substr($sql2, 0, -4) != '')
{
$sql .= '('.substr($sql2, 0, -4).') AND ';
///// AGE TITLE
$agetitle = ' Aged '.$post['from'].' - '.$post['to'].'';
}
}

/////////////// SEX STATUS
if($post['sex_relstatus'] != 'Any' && $post['sex_relstatus'] != '')
{
$sql .= "sex_relstatus LIKE '%{$post['sex_relstatus']}%' AND ";
$sexstatustitle = ' who are '.$post['sex_relstatus'];
}
/////////////// SEX PREF
if($post['sex_pref'] != 'Any')
{
$sql .= "sex_pref LIKE '%{$post['sex_pref']}%' AND ";
$sexpreftitle = $post['sex_pref'].' ';
}
//// PHOTO
if($post['photo'] == 1)
{
$sql .= "image != '' AND ";
$profilepictitle = ' with a Photo';
}
/////////// NOT ME
if($in == 'y')
{
$meid = $_SESSION['userid'];
$sql .= 'id != '.$meid.' AND ';
}




$sql2 = 'ORDER BY currentlogin DESC';
$sql3 = '';
if($_GET['keywords'] != '')
{
$sql2 = " AND MATCH (town, about,username) AGAINST ('".$_GET['keywords']."')";
$sql3 = ", MATCH (town, about,username) AGAINST ('".$_GET['keywords']."')";
}






/// PAGINATION
$resultcount = $db->query("SELECT id $sql3 FROM members WHERE id != 100 AND $sql validated = 'y' $sql2", null,PDO::FETCH_NUM,'y');
$perpage = '20';
$spage = ($_GET['pagenum'] > 1) ? $_GET['pagenum']: 1;
$startnum = ($perpage*($spage-1));
///  REDIRECT FAKE PAGES
if($_GET['page'] > ceil($resultcount/$perpage))
{
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$short->removepagenum($pageurl));
exit;
}
$array['pagination'] = $short->pagination($resultcount,$perpage,$spage,$pageurl,$paginate_adj);


/// GET RESULTS
$x=0;
$query = $db->query("SELECT id $sql3 FROM members WHERE id != 100 AND $sql validated = 'y' $sql2 LIMIT $startnum,$perpage", null,PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
  $x++;
$spacer = ($x==1) ? '': '<div class="divline"></div>';
$array['members'] .= $spacer.$short->user($data['id'],'result','n');

if($x==5 || $x==10)
{
  $array['members'] .= $spacer.$short->contentad($mobilemod);
}


}

////  NO RESULTS
if($array['members'] == '')
{
$array['members'] = 'Sorry, your search returned no results.';
}































//////////////////////////////// GET SEARCH VALUES
$array['f'] = $_GET['from'];
$array['t'] = $_GET['to'];
$array['keywords'] = $_GET['keywords'];


if($_GET['country'] == '')
{
$_GET['country'] = 'Any';
}










$array['h1title'] = ''.$sexpreftitle.''.$sextitle.''.$agetitle.''.$countrytitle.''.$sexstatustitle.''.$profilepictitle.'';
///////  SET SESSIONS FOR RETURN TO SEARCH
if($array['h1title'] != 'Members')
{
}
else
{
$array['h1title'] = 'All Members';
}
$array['pagetitle'] = $array['h1title'].'';
$array['pagedescription'] = 'Browse through and contact '.$array['h1title'].' on the Underground Sex Club. You can further filter your results here. Searching over 100,000 members.';




//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=members','Members',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],$array['h1title'],3);










$array['sex'] = $form->select_post_fix('Any,Male,Female', 'Any,Male,Female', $_GET['sex']);
$st = 'Any,Single,In a Relationship,In an Open Relationship,Soft Swinging,Full Swinging';
$array['sex_relstatus'] = $form->select_post_fix($st,$st, $_GET['sex_relstatus']);
$st = 'Any,Straight,Gay,Bi-Sexual';
$array['sex_pref'] = $form->select_post_fix($st,$st, $_GET['sex_pref']);
//

$ls = ",,Australia,United States of America,United Kingdom,,Afghanistan,&Aring;land Islands,Albania,Algeria,American Samoa,Andorra,Angola,Anguilla,Antarctica,Antigua and Barbuda,Argentina,Armenia,Aruba,Australia,Austria,Azerbaijan,Bahamas,Bahrain,Bangladesh,Barbados,Belarus,Belgium,Belize,Benin,Bermuda,Bhutan,Bolivia,Bosnia and Herzegovina,Botswana,Bouvet Island,Brazil,British Indian Ocean territory,Brunei Darussalam,Bulgaria,Burkina Faso,Burundi,Cambodia,Cameroon,Canada,Cape Verde,Cayman Islands,Central African Republic,Chad,Chile,China,Christmas Island,Cocos (Keeling) Islands,Colombia,Comoros,Congo,Congo, Democratic Republic,Cook Islands,Costa Rica,C&ocirc;te d'Ivoire (Ivory Coast),Croatia (Hrvatska),Cuba,Cyprus,Czech Republic,Denmark,Djibouti,Dominica,Dominican Republic,East Timor,Ecuador,Egypt,El Salvador,Equatorial Guinea,Eritrea,Estonia,Ethiopia,Falkland Islands,Faroe Islands,Fiji,Finland,France,French Guiana,French Polynesia,French Southern Territories,Gabon,Gambia,Georgia,Germany,Ghana,Gibraltar,Greece,Greenland,Grenada,Guadeloupe,Guam,Guatemala,Guinea,Guinea Bissau,Guyana,Haiti,Heard and McDonald Islands,Honduras,Hong Kong,Hungary,Iceland,India,Indonesia,Iran,Iraq,Ireland,Israel,Italy,Jamaica,Japan,Jordan,Kazakhstan,Kenya,Kiribati,Korea (north),Korea (south),Kuwait,Kyrgyzstan,Lao People's Democratic Republic,Latvia,Lebanon,Lesotho,Liberia,Libyan Arab Jamahiriya,Liechtenstein,Lithuania,Luxembourg,Macao,Madagascar,Malawi,Malaysia,Maldives,Mali,Malta,Marshall Islands,Martinique,Mauritania,Mauritius,Mayotte,Mexico,Micronesia,Moldova,Monaco,Mongolia,Montenegro,Montserrat,Morocco,Mozambique,Myanmar,Namibia,Nauru,Nepal,Netherlands,Netherlands Antilles,New Caledonia,New Zealand,Nicaragua,Niger,Nigeria,Niue,Norfolk Island,Northern Mariana Islands,Norway,Oman,Pakistan,Palau,Palestinian Territories,Panama,Papua New Guinea,Paraguay,Peru,Philippines,Pitcairn,Poland,Portugal,Puerto Rico,Qatar,R&eacute;union,Romania,Russian Federation,Rwanda,Saint Helena,Saint Kitts and Nevis,Saint Lucia,Saint Pierre and Miquelon,Saint Vincent and the Grenadines,Samoa,San Marino,Sao Tome and Principe,Saudi Arabia,Senegal,Serbia,Seychelles,Sierra Leone,Singapore,Slovakia,Slovenia,Solomon Islands,Somalia,South Africa,Spain,Sri Lanka,Sudan,Suriname,Svalbard and Jan Mayen Islands,Swaziland,Sweden,Switzerland,Syria,Taiwan,Tajikistan,Tanzania,Thailand,Togo,Tokelau,Tonga,Trinidad and Tobago,Tunisia,Turkey,Turkmenistan,Turks and Caicos Islands,Tuvalu,Uganda,Ukraine,United Arab Emirates,United Kingdom,United States of America,Uruguay,Uzbekistan,Vanuatu,Vatican City,Venezuela,Vietnam,Virgin Islands (British),Virgin Islands (US),Wallis and Futuna Islands,Western Sahara,Yemen,Zaire,Zambia,Zimbabwe";
$ls2 = "Any,-----,Australia,United States of America,United Kingdom,-----,Afghanistan,&Aring;land Islands,Albania,Algeria,American Samoa,Andorra,Angola,Anguilla,Antarctica,Antigua and Barbuda,Argentina,Armenia,Aruba,Australia,Austria,Azerbaijan,Bahamas,Bahrain,Bangladesh,Barbados,Belarus,Belgium,Belize,Benin,Bermuda,Bhutan,Bolivia,Bosnia and Herzegovina,Botswana,Bouvet Island,Brazil,British Indian Ocean territory,Brunei Darussalam,Bulgaria,Burkina Faso,Burundi,Cambodia,Cameroon,Canada,Cape Verde,Cayman Islands,Central African Republic,Chad,Chile,China,Christmas Island,Cocos (Keeling) Islands,Colombia,Comoros,Congo,Congo, Democratic Republic,Cook Islands,Costa Rica,C&ocirc;te d'Ivoire (Ivory Coast),Croatia (Hrvatska),Cuba,Cyprus,Czech Republic,Denmark,Djibouti,Dominica,Dominican Republic,East Timor,Ecuador,Egypt,El Salvador,Equatorial Guinea,Eritrea,Estonia,Ethiopia,Falkland Islands,Faroe Islands,Fiji,Finland,France,French Guiana,French Polynesia,French Southern Territories,Gabon,Gambia,Georgia,Germany,Ghana,Gibraltar,Greece,Greenland,Grenada,Guadeloupe,Guam,Guatemala,Guinea,Guinea Bissau,Guyana,Haiti,Heard and McDonald Islands,Honduras,Hong Kong,Hungary,Iceland,India,Indonesia,Iran,Iraq,Ireland,Israel,Italy,Jamaica,Japan,Jordan,Kazakhstan,Kenya,Kiribati,Korea (north),Korea (south),Kuwait,Kyrgyzstan,Lao People's Democratic Republic,Latvia,Lebanon,Lesotho,Liberia,Libyan Arab Jamahiriya,Liechtenstein,Lithuania,Luxembourg,Macao,Madagascar,Malawi,Malaysia,Maldives,Mali,Malta,Marshall Islands,Martinique,Mauritania,Mauritius,Mayotte,Mexico,Micronesia,Moldova,Monaco,Mongolia,Montenegro,Montserrat,Morocco,Mozambique,Myanmar,Namibia,Nauru,Nepal,Netherlands,Netherlands Antilles,New Caledonia,New Zealand,Nicaragua,Niger,Nigeria,Niue,Norfolk Island,Northern Mariana Islands,Norway,Oman,Pakistan,Palau,Palestinian Territories,Panama,Papua New Guinea,Paraguay,Peru,Philippines,Pitcairn,Poland,Portugal,Puerto Rico,Qatar,R&eacute;union,Romania,Russian Federation,Rwanda,Saint Helena,Saint Kitts and Nevis,Saint Lucia,Saint Pierre and Miquelon,Saint Vincent and the Grenadines,Samoa,San Marino,Sao Tome and Principe,Saudi Arabia,Senegal,Serbia,Seychelles,Sierra Leone,Singapore,Slovakia,Slovenia,Solomon Islands,Somalia,South Africa,Spain,Sri Lanka,Sudan,Suriname,Svalbard and Jan Mayen Islands,Swaziland,Sweden,Switzerland,Syria,Taiwan,Tajikistan,Tanzania,Thailand,Togo,Tokelau,Tonga,Trinidad and Tobago,Tunisia,Turkey,Turkmenistan,Turks and Caicos Islands,Tuvalu,Uganda,Ukraine,United Arab Emirates,United Kingdom,United States of America,Uruguay,Uzbekistan,Vanuatu,Vatican City,Venezuela,Vietnam,Virgin Islands (British),Virgin Islands (US),Wallis and Futuna Islands,Western Sahara,Yemen,Zaire,Zambia,Zimbabwe";
$array['countries'] = $form->select_post_fix($ls,$ls2, $_GET['country']);
