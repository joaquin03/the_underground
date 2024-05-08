<?php

$page->page .= $page->get_temp('templates/local/index.htm');


/// GET AREA
if(isset($_GET['area']))
{

$array['extrameta'] .= ($mobilemod == '') ? '<style>
.multicol{
-webkit-column-count: 4;
-moz-column-count: 4;
column-count: 4;
}
.mb5{
margin-bottom:5px;
}
</style>' : '';
/// SPLIT COUNTRY AND STATE
$string = $_GET['area'];
$exploded = explode('-', $string);
$c = end($exploded);
$s = str_replace('-'.$c,'',$_GET['area']);
///
$array['pagetitle'] = ''.$s.' Local Pages';
$array['title'] = 'The Local Pages '.$s.'';
$array['pagedescription'] = ''.$s.' Local Sex Pages. Search our directory and find local '.$s.' ('.$c.') people for sex.';
$array['leftcol'] .= '<h2>Select City/Town in '.$s.', '.$c.'</h2>';
$array['rightcol'] .= '<h2>Popular Local Pages</h2>';
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=local','Local Pages',2);
$array['breadcrumbs'] .= $short->bcitem($array['ogurl'],'The Local Pages '.$s.', '.$c.'',3);

$query = $db->query("SELECT suburb,url FROM towns WHERE country = :c AND state = :s AND local_active = 'y' ORDER BY suburb ASC", array("c"=>$c,"s"=>$s),PDO::FETCH_ASSOC,"n");
$list = '';
$x=0;
$subheading = '';
foreach ( $query as $data ) {
$x++;
$list .= '<div class="mb5"><a href="'.$rooturl.'/?local='.$data['url'].'">'.$data['suburb'].'</a></div>';
if($x==4)
{
$x=0;
}
}

$array['leftcol'] .= '<div class="multicol">'.$list.'</div>';



/// LOCAL LIST RIGHT COL
$query = $db->query("SELECT url,suburb,state FROM towns WHERE country = :c AND state = :s AND local_active = 'y' ORDER BY rand() LIMIT 15", array("c"=>$c,"s"=>$s),PDO::FETCH_ASSOC,"n");
foreach ( $query as $data ) {
$a1 = array(
'1' => 'Women',
'2' => 'Sluts'
);
$a2 = rand(1,2);
$pagetype = $a1[$a2];

$array['rightcol'] .= '<span class="onelinetext"><span class="red fstyle1 fs22 m0">&rsaquo;</span> &nbsp; <a href="'.$rooturl.'/?local-'.strtolower($pagetype).'='.$data['url'].'">'.$data['suburb'].' ('.$data['state'].') Local '.$pagetype.'</a></span>';
}

$array['rightcol'] .= '<div class="space30"></div>
'.$array['lastrightad'].'';

}



else{
$array['pagetitle'] = 'Local Pages &middot; Select Area';
$array['title'] = 'The Local Pages &middot; Select Area';
$array['pagedescription'] = 'Welcome to the local pages. Select a location, and meet local people for sex.';
$array['rightcol'] = $array['lastrightad'];
//// BREAD CRUMBS
$array['breadcrumbs'] .= $short->bcitem($rooturl.'/?mod=local','Local Pages',2);
/////  LOOP MAIN LOCATIONS
$query = $db->query("SELECT country,state FROM towns WHERE local_active = 'y' ORDER BY country ASC, state ASC", null,PDO::FETCH_ASSOC,"n");
$heading = '';
$h=0;
$subheading = '';
$s=0;
foreach ( $query as $data ) {
$h++;
$headingspacer = ($h==1) ? '' : '<div class="space30"></div>';
$subspacer = ($heading != $data['country']) ? '' : '&nbsp; ';


/// H2 Heading
$array['leftcol'] .= ($heading != $data['country']) ? $headingspacer.'<h2>'.$data['country'].'</h2>': '';
$heading = $data['country'];


/// Areas
$array['leftcol'] .= ($subheading != $data['state']) ? $subspacer.'<a href="'.$rooturl.'/?mod=local&area='.$data['state'].'-'.$data['country'].'"><strong class="mr5">'.$data['state'].'</strong></a>': '';
$subheading = $data['state'];

}
}
