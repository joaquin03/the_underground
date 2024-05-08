<?php
class image
{






function save($file,$type,$width,$height,$location,$quality,$watermark)
{
$im = new imagick($file);
///  IF FORCHED TO WIDTH AND HEIGHT
if($width > 0 && $height > 0)
{
$im->cropThumbnailImage($width, $height);
}
/// IF JUST FORCED TO A WIDTH OR A HEIGHT
else
{
$im->scaleImage($width, $height);
}
/// SET THE FORMAT TYPE
$im->setImageFormat($type);
/// JPG
if($type = 'jpg')
{
$im->setCompression(Imagick::COMPRESSION_JPEG);
$im->setImageCompressionQuality($quality);
$im->setInterlaceScheme(Imagick::INTERLACE_PLANE);
}
/// PNG
else if($type = 'png')
{
$im->setImageBackgroundColor('white');
$im = $im->flattenImages();
}


if($watermark != '')
{
$watermark = new Imagick($watermark);
$iwidth = $im->getImageWidth();
$iheight = $im->getImageHeight();
$wwidth = $watermark->getImageWidth();
$wheight = $watermark->getImageHeight();
$x = ($iwidth-$wwidth-10);
$y = ($iheight-$wheight-10);
$im->compositeImage($watermark, Imagick::COMPOSITE_OVER, $x, $y);
}



/// WRITE IMAGE AND CLOSE
$im->writeImage($location);
$im->clear();
$im->destroy();
}


















///////////////////////////////////////////////////////////////////////////////////////// END WHOLE FUNCTION LIST
}
$image = NEW image();
