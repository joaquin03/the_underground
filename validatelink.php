<?php
session_start();
ob_start(); 

$redirect = '';

if($_SESSION['milfaholiclink'] != '')
{
$milflink = '<div class="divline"></div>
<table width="300px" border="0" cellspacing="0" cellpadding="5" class="center">
	<tbody>
		<tr>
			<td width="110" valign="middle"><img src="images/logo-milf.png" width="100" height="27" alt=""/></td>
			<td width="30" align="center" valign="middle"><img src="images/success-arrow.png"  height="30" alt=""/></td>
			<td width="160" valign="middle">Account Approved!<br><a href="'.$_SESSION['milfaholiclink'].'"><span class="button">Go to Account</span></a></td>
		</tr>
	</tbody>
</table>';
$redirect = '<meta http-equiv="refresh" content="5;url='.$_SESSION['milfaholiclink'].'" />';
$redirecttext = '<div class="divline"></div>Launching Your Milfaholics Account in <div id="counter">5</div> Seconds';
}

if($_SESSION['flinglink'] != '')
{
$milflink = '<div class="divline"></div>
<table width="300px" border="0" cellspacing="0" cellpadding="5" class="center">
	<tbody>
		<tr>
			<td width="110" valign="middle"><img src="images/logo-fling.png" width="64" height="28" alt=""/></td>
			<td width="30" align="center" valign="middle"><img src="images/success-arrow.png"  height="30" alt=""/></td>
			<td width="160" valign="middle">Account Approved!<br><a href="'.$_SESSION['flinglink'].'"><span class="button">Go to Account</span></a></td>
		</tr>
	</tbody>
</table>';
$redirect = '<meta http-equiv="refresh" content="5;url='.$_SESSION['milfaholiclink'].'" />';
$redirecttext = '<div class="divline"></div>Launching Your SnapSext Account in <div id="counter">5</div> Seconds';
}


if($_SESSION['milfaholiclink'] == '' && $_SESSION['flinglink'] == '')
{
header("Location: http://www.theundergroundsexclub.com/?mod=register&file=validate");
exit;
}

unset($_SESSION['milfaholiclink']);
unset($_SESSION['flinglink']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1" />
<?=$redirect;?>
<title>Success!</title>
<head>
<style type="text/css">
<!--
a:link {
    text-decoration: none;
}

a:visited {
    text-decoration: none;
}
body,td,th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000000;
}
body {
	background-color: #FFFFFF;
	margin-left: 20px;
	margin-top: 20px;
	margin-right: 20px;
	margin-bottom: 20px;
	text-align: center;
}
	.heading{
		font-weight: bold;
		text-transform: uppercase;
		font-size: 18px;
	}
.divline{
	border-bottom:2px solid #EAEAEA;margin-bottom:20px; padding-top:20px;
}
	.d20{
		height:20px;
	}
	table.center {
    margin-left:auto; 
    margin-right:auto;
		text-align:left;
  }
	.button{
		background-color:#FF4A4A;
		padding:5px;
		color:#FFF;
		font-weight:bold;
		text-decoration: none;
		display:inline-block;
		margin-top:5px;
	}
	#counter{
		font-weight: bold;
		text-transform: uppercase;
		font-size: 18px;
		margin:10px;
	}
-->
</style>
</head>
<body>
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-P4R2JC"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-P4R2JC');</script>
<!-- End Google Tag Manager -->


<img src="images/success.png" width="80" height="80" alt=""/>
<div class="d20"></div>
<span class="heading">Registration Successful</span>
<div class="divline"></div>

<table width="300px" border="0" cellspacing="0" cellpadding="5" class="center">
	<tbody>
		<tr>
			<td width="110" valign="middle"><img src="images/logo1.png" width="100" height="41" alt=""/></td>
			<td width="30" align="center" valign="middle"><img src="images/success-arrow.png"  height="30" alt=""/></td>
			<td width="160" valign="middle">Email Validation Required - Email Sent</td>
		</tr>
	</tbody>
</table>



<?=$flinglink;?>

<?=$milflink;?>

<?=$redirecttext;?>

<script>
        setInterval(function() {
            var div = document.querySelector("#counter");
            var count = div.textContent * 1 - 1;
            div.textContent = count;
            if (count <= 0) {
                div.textContent = '0';
            }
        }, 1000);
    </script>

</body>
</html>