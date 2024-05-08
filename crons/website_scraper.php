<?php




$gettime = $time-(60*60*24*30);    // 30 Days
////////////////////////////////////////////////////////////
$query = $db->query("SELECT * FROM websites WHERE scrapped < :t LIMIT 5",array("t"=>$gettime),PDO::FETCH_ASSOC,"n");
foreach($query as $data)
{

// MARK AS SCRAPPED INCASE OF ERROR
$db->query("UPDATE websites SET scrapped = :s WHERE id = :id", array("id"=>$data['id'],"s"=>$time),PDO::FETCH_ASSOC,"n");


$title = '';
$description = '';
$imagef = '';
//
$urlpage = $data['url'];

$dom = new DOMDocument();
libxml_use_internal_errors(true);

if($dom->loadHTMLFile($urlpage))
{
/// TITLE
$list = $dom->getElementsByTagName("title");
if ($list->length > 0){$title = $list->item(0)->textContent;}
// DESC
$metas = $dom->getElementsByTagName('meta');
foreach ($metas as $meta) {
  if (strtolower($meta->getAttribute('name')) == 'description') {
    $description = $meta->getAttribute('content');
  }
}
// IMAGE
$metas = $dom->getElementsByTagName('meta');
foreach ($metas as $meta) {
  if (strtolower($meta->getAttribute('property')) == 'og:image') {
    $imagef = $meta->getAttribute('content');
  }
}
}/// END IF FILE EXISTS

libxml_use_internal_errors(false);


//////////////////////////////////////////  IF THERE IS AN IMAGE MOVE AND RESIZE
if($imagef != '')
{
$icode = '';
$remote_image = file_get_contents($imagef);
if($remote_image != '')
{
///  REMOVE OLD images
@unlink($serverpath.'/images/websites/'.$data['image'].'-original.jpg');
@unlink($serverpath.'/images/websites/'.$data['image'].'.jpg');
@unlink($serverpath.'/images/websites/'.$data['image'].'-thumb.jpg');
///
include_once(''.$serverpath.'/addons/image.php');
$ext = end((explode(".", $imagef)));
$icode = $short->createcode(10);
file_put_contents($serverpath."/images/websites/".$icode."-import.".$ext, $remote_image);
$image->save($serverpath."/images/websites/".$icode."-import.jpg",'jpg','800','0',$serverpath."/images/websites/".$icode."-original.jpg",'100','');
$image->save($serverpath."/images/websites/".$icode."-import.jpg",'jpg','500','0',$serverpath."/images/websites/".$icode.".jpg",'70','');
$image->save($serverpath."/images/websites/".$icode."-import.jpg",'jpg','148','148',$serverpath."/images/websites/".$icode."-thumb.jpg",'70','');
@unlink($serverpath."/images/websites/".$icode."-import.jpg");
}// END IF COPIED IMAGE EXISTS
}
///////////////////////////////////////  IF XVidEOS VidEO
if(strpos($data['url'], 'www.xvideos.com') !== false )
{
foreach ($metas as $meta) {
  if (strtolower($meta->getAttribute('property')) == 'og:video') {
    $videostring = $meta->getAttribute('content');
  }
  if (strtolower($meta->getAttribute('property')) == 'og:video:width') {
    $videowidth = $meta->getAttribute('content');
  }
  if (strtolower($meta->getAttribute('property')) == 'og:video:height') {
    $videoheight = $meta->getAttribute('content');
  }
}
/// GET VidEO id
$videoid = end((explode("=", $videostring)));
$video = 'xvideos|'.$videoid;
$vidratio = ($videowidth != '' && $videoheight != '') ? $videoheight/$videowidth : '';
}



/////
$title = addslashes(str_replace(",", "&#8218;", $title));
$title = mb_convert_encoding($title, 'HTML-ENTITIES', 'UTF-8');
$description = addslashes(str_replace(",", "&#8218;", $description));
$description = mb_convert_encoding($description, 'HTML-ENTITIES', 'UTF-8');
//
$db->query("UPDATE websites SET title = :t, description = :d, image = :i, video = :v, videoratio = :vr, scrapped = :s WHERE id = :id", array("id"=>$data['id'],"t"=>$title,"d"=>$description,"i"=>$icode,"v"=>$video,"vr"=>$vidratio,"s"=>$time),PDO::FETCH_ASSOC,"n");
}
