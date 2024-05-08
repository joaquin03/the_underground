<?php


//$db->query("UPDATE s_api_registrations SET completed = 'n'", null,PDO::FETCH_ASSOC,"n");




//// ACCOUNTS
$account = array(
'1' => 'theusc_ppl',
'2' => 'theusc_ppl'
);
$num = rand(1,2);
$ac = $account[$num];


/////
//$query = $db->query("SELECT * FROM s_api_registrations WHERE company = 'fling' AND completed != 'y' ORDER BY id DESC LIMIT 10", null,PDO::FETCH_ASSOC,"n");
$query = $db->query("SELECT * FROM s_api_registrations WHERE company = 'fling' ORDER BY id DESC LIMIT 10", null,PDO::FETCH_ASSOC,"n");
//
foreach($query as $data)
{
$continue = 'y';
$newtries = $data['tries']+1;


//// GET THE USER DATA
$user = $db->row("SELECT * FROM members WHERE id = :id",array("id"=>$data['userid']));
$user['regip'] = $user['regip'];




////  DELETE IF NO USER ID
if($user['id'] < 1) {
$db->query("DELETE FROM s_api_registrations WHERE id = :id", array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
$continue = 'n';
}


$city = '';
/// COUNTRY - DELETE IF NOT A MAJOR COUNTRY
if($continue == 'y'){
if($user['country'] == 'United States of America' || $user['country'] == 'United States'){$country = 'US';$city = 'New York';}
else if ($user['country'] == 'Australia'){$country = 'AU';$city = 'Sydney';}
else if ($user['country'] == 'Canada') {$country = 'CA';$city = 'Toronto';}
else if ($user['country'] == 'United Kingdom') {$country = 'UK';$city = 'London';}
else if ($user['country'] == 'New Zealand') {$country = 'NZ';$city = 'Auckland';}
else {
$db->query("DELETE FROM s_api_registrations WHERE id = :id", array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");
$continue = 'n';
}
}




if($continue == 'y'){
$result = sendreg($ac,$data['platform'],$user['regip'],$user['email'],$user['sex'],$data['seeking'],$user['dob_date'],$country,'');
$res = explode("|", $result);
$status = $res[0];
$reason = $res[1];
$saferesult = trim(str_replace(',','&#8218;',$result));
}


/// RESULT IS OK AND REG WORKED
if($status=='0') {
//$db->query("UPDATE s_api_registrations SET completed = :v, returnstring = :rs WHERE id = :id", array("v"=>'y',"id"=>$data['id'],"rs"=>$saferesult),PDO::FETCH_ASSOC,"n");

$db->query("DELETE FROM s_api_registrations WHERE id = :id", array("id"=>$data['id']),PDO::FETCH_ASSOC,"n");

$continue = 'n';
}



///   DISPLAY NAME FAULT
if($continue == 'y' && strpos($reason, 'Name in use') !== false)
{
$result = sendreg($ac,$data['platform'],$user['regip'],$user['email'],$user['sex'],$data['seeking'],$user['dob_date'],$country,$city);
$res = explode("|", $result);
$status = $res[0];
$reason = $res[1];
$saferesult = trim(str_replace(',','&#8218;',$result));
$continue = 'n';
/// RESULT IS OK - FOR THIS RUN
if($status=='0') {
$db->query("UPDATE s_api_registrations SET completed = :v, returnstring = :rs WHERE id = :id", array("v"=>'y',"id"=>$data['id'],"rs"=>$saferesult),PDO::FETCH_ASSOC,"n");
}
}




///   EMAIL ADDRESS IN USE
if($continue == 'y' && strpos($reason, 'address in use') !== false)
{
$ac = 'theusc_ml';
$result = sendreg($ac,$data['platform'],$user['regip'],$user['email'],$user['sex'],$data['seeking'],$user['dob_date'],$country,$city);
$res = explode("|", $result);
$status = $res[0];
$reason = $res[1];
$saferesult = trim(str_replace(',','&#8218;',$result));
$continue = 'n';
/// JUST MARK AS COMPLETED
$db->query("UPDATE s_api_registrations SET completed = :v, returnstring = :rs WHERE id = :id", array("v"=>'y',"id"=>$data['id'],"rs"=>$saferesult),PDO::FETCH_ASSOC,"n");
}



/// INVALid CITY
if($continue == 'y' && $reason == 'Invalid city name')
{
$result = sendreg($ac,$data['platform'],$user['regip'],$user['email'],$user['sex'],$data['seeking'],$user['dob_date'],$country,$city);
$res = explode("|", $result);
$status = $res[0];
$reason = $res[1];
$saferesult = trim(str_replace(',','&#8218;',$result));
$continue = 'n';
/// RESULT IS OK - FOR THIS RUN
if($status=='0') {
$db->query("UPDATE s_api_registrations SET completed = :v, returnstring = :rs WHERE id = :id", array("v"=>'y',"id"=>$data['id'],"rs"=>$saferesult),PDO::FETCH_ASSOC,"n");
}
}



/// Bad GEO
if($continue == 'y' && $reason == 'Bad geo')
{
$result = sendreg($ac,$data['platform'],$user['regip'],$user['email'],$user['sex'],$data['seeking'],$user['dob_date'],$country,$city);
$res = explode("|", $result);
$status = $res[0];
$reason = $res[1];
$saferesult = trim(str_replace(',','&#8218;',$result));
$continue = 'n';
/// RESULT IS OK - FOR THIS RUN
if($status=='0') {
$db->query("UPDATE s_api_registrations SET completed = :v, returnstring = :rs WHERE id = :id", array("v"=>'y',"id"=>$data['id'],"rs"=>$saferesult),PDO::FETCH_ASSOC,"n");
}
}









/// OR ELSE JUST RECORD THE ERROR
if($continue == 'y' && $status != 0){
$db->query("UPDATE s_api_registrations SET returnstring = :rs WHERE id = :id", array("id"=>$data['id'],"rs"=>$saferesult),PDO::FETCH_ASSOC,"n");
}




/// INCREASE TRIES
$db->query("UPDATE s_api_registrations SET tries = :t WHERE id = :id", array("t"=>$newtries,"id"=>$data['id']),PDO::FETCH_ASSOC,"n");


}////   END MAIN LOOP



















function sendreg($ac,$platform,$ip,$email,$sex,$seeking,$dob,$country,$city)
{
list($year,$month,$day) = explode('-', $dob);
$url = 'http://www.dating-hackers.com/api/rereg.php';
$post_data['secret_key'] = '_scamfree';
$post_data['cmp'] = ($platform == 'desktop') ? 'register' : 'registermobile';
$post_data['acode'] = $ac;
if($platform == 'mobile')
{
$post_data['mobile'] = '1';
}
$post_data['uip'] = $ip;
$post_data['username'] = $un;
$post_data['em'] = $email;
$post_data['gender'] = ($sex == 'Female') ? 'F' : 'M';
$post_data['seeking'] = $seeking;
$post_data['bday'] = $day;
$post_data['bmon'] = $month;
$post_data['byear'] = $year;
$post_data['country'] = $country;
if($city != '')
{
$post_data['city'] = $city;
}
if($pw != '')
{
$post_data['pass'] = $password;
}
foreach ( $post_data as $key => $value) {
$post_items[] = $key . '=' . $value;
}
$post_string = implode ('&', $post_items);
//mail($siteemail, 'New API Reg', $post_string, "From: notifications@the-usc.com");
$curl_connection =
curl_init($url);
curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($curl_connection, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);
//$result = curl_exec($curl_connection);
//curl_error($curl_connection);
curl_close($curl_connection);
return '1||GUID=90000'.rand(1000,9999);

return $result;
}
