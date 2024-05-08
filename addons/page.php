<?php
class Page
{
var $page,$msg;


function replace_tags($tags)
{
if (sizeof($tags) > 0)
{
foreach ($tags as $tag => $data)
{
$this->page = str_replace('{'.stripslashes($tag).'}', $data, $this->page);
}
}
}


function get_temp($file)
{
ob_start();
if($file != "." && $file != "..")
include($file);
$buffer = ob_get_contents();
ob_end_clean();
return $buffer;
}


function output()
{
print($this->page);
}


}
$page = NEW Page();
