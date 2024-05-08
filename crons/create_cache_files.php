<?php






////////////////   CREATE EMAIL LATEST GIRLS
$text = '';
$query = $db->query("SELECT id,image FROM members WHERE sex = 'Female' AND validated = 'y' AND image != '' ORDER BY id DESC LIMIT 3",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$text .= '<td style="width:calc(100% / 3);"><a href="http://www.theundergroundsexclub.com/?u='.$data['id'].'"><img src="https://www.theundergroundsexclub.com/images/members/'.$data['image'].'-thumb.jpg" alt="" style="display:block;width:100%;"/></a></td>';
}
//
$myFile = '/var/www/vhosts/theundergroundsexclub.com/httpdocs/cachefiles/members/email-women.txt';
$fh = fopen($myFile, 'w');
chmod($myFile,0777);
// WRITE FILE
fwrite($fh, $text);
fclose($fh);




////////////////   CREATE EMAIL LATEST MEN
$text = '';
$query = $db->query("SELECT id,image FROM members WHERE sex = 'Male' AND validated = 'y' AND image != '' ORDER BY id DESC LIMIT 3",null,PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{
$text .= '<td style="width:calc(100% / 3);"><a href="http://www.theundergroundsexclub.com/?u='.$data['id'].'"><img src="https://www.theundergroundsexclub.com/images/members/'.$data['image'].'-thumb.jpg" alt="" style="display:block;width:100%;"/></a></td>';
}
//
$myFile = '/var/www/vhosts/theundergroundsexclub.com/httpdocs/cachefiles/members/email-men.txt';
$fh = fopen($myFile, 'w');
chmod($myFile,0777);
// WRITE FILE
fwrite($fh, $text);
fclose($fh);
