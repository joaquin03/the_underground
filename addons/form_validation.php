<?php
class form_validation
{
	
	

function select_post_fix($values, $lables, $selected)
{
if($selected == '')
{
$disp = "<option value=\"\" selected>-- Select --</option>\n";
}
if(!empty($values))
{
$val = explode(",", $values);
$val_ok = 1;
}
$lab = explode(",", $lables);
//
for($x = 0; $x<=count($lab); $x++)
{
$value = $lab[$x];
if($val_ok = 1)
{
$value = $val[$x];
}
if($lab[$x] != "")
{
if($value == $selected)
{
$disp .= "<option value=\"".$value."\" selected>".$lab[$x]."</option>\n";
}
else
{
$disp .= "<option value=\"".$value."\">".$lab[$x]."</option>\n";
}
unset($value);
}
}
return $disp;
}




//
}
$form = NEW form_validation();
?>