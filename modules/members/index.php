<?php
if(!isset($_GET['mod']))
{
die();
}
$array['pagetitle'] = 'Members';
$array['pagedescription'] = 'Member directory for the Underground Sex Club. Search for members here.';
$page->page .= $page->get_temp('templates/members/index.htm');




//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'Members',2);


include(''.$serverpath.'/addons/form_validation.php');



/////////////  GET ALL THE LATEST WOMEN
$query = $db->query("SELECT id,image FROM members WHERE sex = 'Female' AND validated = 'y' AND image != '' ORDER BY id DESC LIMIT 28",null,PDO::FETCH_ASSOC,"n");
$array['women'] .= '<tr>';
$rx = 0;
foreach($query as $data)
{
$rx++;
if($rx == 8)
{
$array['women'] .= '</tr><tr>';
$rx=1;
}
$array['women'] .= '<td class="one7th">
      <span class="minus2around"><a href="../?u='.$data['id'].'"><img src="'.$staticurl.'/images/members/'.$data['image'].'-thumb.jpg" class="width100" alt=""/></a></span>
      </td>';
}
/// CLOSE CURRENT ROW
$array['women'] .= '</tr>';




/////////////  GET ALL THE LATEST MEN
$query = $db->query("SELECT id,image FROM members WHERE sex = 'Male' AND validated = 'y' AND image != '' ORDER BY id DESC LIMIT 28",null,PDO::FETCH_ASSOC,"n");
$array['men'] .= '<tr>';
$rx = 0;
foreach($query as $data)
{
$rx++;
if($rx == 8)
{
$array['men'] .= '</tr><tr>';
$rx=1;
}
$array['men'] .= '<td class="one7th">
      <span class="minus2around"><a href="../?u='.$data['id'].'"><img src="'.$staticurl.'/images/members/'.$data['image'].'-thumb.jpg" class="width100" alt=""/></a></span>
      </td>';
}
/// CLOSE CURRENT ROW
$array['men'] .= '</tr>';





//// AD
$array['ad1'] = '<div class="space30"></div>'.$short->contentad($mobilemod);





//////////////////////////////// GET SEARCH VALUES
$values['country'] = $_SESSION['country'];
$values['sex_relstatus'] = 'Any';
$values['sex_pref'] = 'Any';
$values['sex'] = 'Any';
$array['f'] = 'From Age';
$array['t'] = 'To Age';



$array['sex'] = $form->select_post_fix('Any,Male,Female', 'Any,Male,Female', $values['sex']);
$st = 'Any,Single,In a Relationship,In an Open Relationship,Soft Swinging,Full Swinging';
$array['sex_relstatus'] = $form->select_post_fix($st,$st, $values['sex_relstatus']);
$st = 'Any,Straight,Gay,Bi-Sexual';
$array['sex_pref'] = $form->select_post_fix($st,$st, $values['sex_pref']);
//

$ls = ",,Australia,United States of America,United Kingdom,,Afghanistan,&Aring;land Islands,Albania,Algeria,American Samoa,Andorra,Angola,Anguilla,Antarctica,Antigua and Barbuda,Argentina,Armenia,Aruba,Australia,Austria,Azerbaijan,Bahamas,Bahrain,Bangladesh,Barbados,Belarus,Belgium,Belize,Benin,Bermuda,Bhutan,Bolivia,Bosnia and Herzegovina,Botswana,Bouvet Island,Brazil,British Indian Ocean territory,Brunei Darussalam,Bulgaria,Burkina Faso,Burundi,Cambodia,Cameroon,Canada,Cape Verde,Cayman Islands,Central African Republic,Chad,Chile,China,Christmas Island,Cocos (Keeling) Islands,Colombia,Comoros,Congo,Congo, Democratic Republic,Cook Islands,Costa Rica,C&ocirc;te d'Ivoire (Ivory Coast),Croatia (Hrvatska),Cuba,Cyprus,Czech Republic,Denmark,Djibouti,Dominica,Dominican Republic,East Timor,Ecuador,Egypt,El Salvador,Equatorial Guinea,Eritrea,Estonia,Ethiopia,Falkland Islands,Faroe Islands,Fiji,Finland,France,French Guiana,French Polynesia,French Southern Territories,Gabon,Gambia,Georgia,Germany,Ghana,Gibraltar,Greece,Greenland,Grenada,Guadeloupe,Guam,Guatemala,Guinea,Guinea Bissau,Guyana,Haiti,Heard and McDonald Islands,Honduras,Hong Kong,Hungary,Iceland,India,Indonesia,Iran,Iraq,Ireland,Israel,Italy,Jamaica,Japan,Jordan,Kazakhstan,Kenya,Kiribati,Korea (north),Korea (south),Kuwait,Kyrgyzstan,Lao People's Democratic Republic,Latvia,Lebanon,Lesotho,Liberia,Libyan Arab Jamahiriya,Liechtenstein,Lithuania,Luxembourg,Macao,Madagascar,Malawi,Malaysia,Maldives,Mali,Malta,Marshall Islands,Martinique,Mauritania,Mauritius,Mayotte,Mexico,Micronesia,Moldova,Monaco,Mongolia,Montenegro,Montserrat,Morocco,Mozambique,Myanmar,Namibia,Nauru,Nepal,Netherlands,Netherlands Antilles,New Caledonia,New Zealand,Nicaragua,Niger,Nigeria,Niue,Norfolk Island,Northern Mariana Islands,Norway,Oman,Pakistan,Palau,Palestinian Territories,Panama,Papua New Guinea,Paraguay,Peru,Philippines,Pitcairn,Poland,Portugal,Puerto Rico,Qatar,R&eacute;union,Romania,Russian Federation,Rwanda,Saint Helena,Saint Kitts and Nevis,Saint Lucia,Saint Pierre and Miquelon,Saint Vincent and the Grenadines,Samoa,San Marino,Sao Tome and Principe,Saudi Arabia,Senegal,Serbia,Seychelles,Sierra Leone,Singapore,Slovakia,Slovenia,Solomon Islands,Somalia,South Africa,Spain,Sri Lanka,Sudan,Suriname,Svalbard and Jan Mayen Islands,Swaziland,Sweden,Switzerland,Syria,Taiwan,Tajikistan,Tanzania,Thailand,Togo,Tokelau,Tonga,Trinidad and Tobago,Tunisia,Turkey,Turkmenistan,Turks and Caicos Islands,Tuvalu,Uganda,Ukraine,United Arab Emirates,United Kingdom,United States of America,Uruguay,Uzbekistan,Vanuatu,Vatican City,Venezuela,Vietnam,Virgin Islands (British),Virgin Islands (US),Wallis and Futuna Islands,Western Sahara,Yemen,Zaire,Zambia,Zimbabwe";
$ls2 = "Any,-----,Australia,United States of America,United Kingdom,-----,Afghanistan,&Aring;land Islands,Albania,Algeria,American Samoa,Andorra,Angola,Anguilla,Antarctica,Antigua and Barbuda,Argentina,Armenia,Aruba,Australia,Austria,Azerbaijan,Bahamas,Bahrain,Bangladesh,Barbados,Belarus,Belgium,Belize,Benin,Bermuda,Bhutan,Bolivia,Bosnia and Herzegovina,Botswana,Bouvet Island,Brazil,British Indian Ocean territory,Brunei Darussalam,Bulgaria,Burkina Faso,Burundi,Cambodia,Cameroon,Canada,Cape Verde,Cayman Islands,Central African Republic,Chad,Chile,China,Christmas Island,Cocos (Keeling) Islands,Colombia,Comoros,Congo,Congo, Democratic Republic,Cook Islands,Costa Rica,C&ocirc;te d'Ivoire (Ivory Coast),Croatia (Hrvatska),Cuba,Cyprus,Czech Republic,Denmark,Djibouti,Dominica,Dominican Republic,East Timor,Ecuador,Egypt,El Salvador,Equatorial Guinea,Eritrea,Estonia,Ethiopia,Falkland Islands,Faroe Islands,Fiji,Finland,France,French Guiana,French Polynesia,French Southern Territories,Gabon,Gambia,Georgia,Germany,Ghana,Gibraltar,Greece,Greenland,Grenada,Guadeloupe,Guam,Guatemala,Guinea,Guinea Bissau,Guyana,Haiti,Heard and McDonald Islands,Honduras,Hong Kong,Hungary,Iceland,India,Indonesia,Iran,Iraq,Ireland,Israel,Italy,Jamaica,Japan,Jordan,Kazakhstan,Kenya,Kiribati,Korea (north),Korea (south),Kuwait,Kyrgyzstan,Lao People's Democratic Republic,Latvia,Lebanon,Lesotho,Liberia,Libyan Arab Jamahiriya,Liechtenstein,Lithuania,Luxembourg,Macao,Madagascar,Malawi,Malaysia,Maldives,Mali,Malta,Marshall Islands,Martinique,Mauritania,Mauritius,Mayotte,Mexico,Micronesia,Moldova,Monaco,Mongolia,Montenegro,Montserrat,Morocco,Mozambique,Myanmar,Namibia,Nauru,Nepal,Netherlands,Netherlands Antilles,New Caledonia,New Zealand,Nicaragua,Niger,Nigeria,Niue,Norfolk Island,Northern Mariana Islands,Norway,Oman,Pakistan,Palau,Palestinian Territories,Panama,Papua New Guinea,Paraguay,Peru,Philippines,Pitcairn,Poland,Portugal,Puerto Rico,Qatar,R&eacute;union,Romania,Russian Federation,Rwanda,Saint Helena,Saint Kitts and Nevis,Saint Lucia,Saint Pierre and Miquelon,Saint Vincent and the Grenadines,Samoa,San Marino,Sao Tome and Principe,Saudi Arabia,Senegal,Serbia,Seychelles,Sierra Leone,Singapore,Slovakia,Slovenia,Solomon Islands,Somalia,South Africa,Spain,Sri Lanka,Sudan,Suriname,Svalbard and Jan Mayen Islands,Swaziland,Sweden,Switzerland,Syria,Taiwan,Tajikistan,Tanzania,Thailand,Togo,Tokelau,Tonga,Trinidad and Tobago,Tunisia,Turkey,Turkmenistan,Turks and Caicos Islands,Tuvalu,Uganda,Ukraine,United Arab Emirates,United Kingdom,United States of America,Uruguay,Uzbekistan,Vanuatu,Vatican City,Venezuela,Vietnam,Virgin Islands (British),Virgin Islands (US),Wallis and Futuna Islands,Western Sahara,Yemen,Zaire,Zambia,Zimbabwe";
$array['countries'] = $form->select_post_fix($ls,$ls2, $values['country']);
