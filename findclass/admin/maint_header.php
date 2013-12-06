<?php
//
//	Common header for non-pop-up programs
//
if (!isset($hdr['nologon'])) {
	if (!(isset($_SESSION['valid']) && $_SESSION['valid']>0)) {
		header("location: /index.php");
		exit;
	}
}
$title=$hdr['title'];
$focus=$backimage="";
$HDRHTML="";
if (isset($hdr['html'])) $HDRHTML=$hdr['html'];
if (isset($hdr['focus']) && strlen($hdr['focus'])) $focus=' onload="document.mpform.'.$hdr['focus'].'.focus(); "';
if (isset($hdr['backimage']) && strlen($hdr['backimage'])) $backimage=" background=images/".$hdr['backimage'];
print <<< END
<html><head><meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>$title</title>
<link rel="stylesheet" href="default.css" type="text/css"><script src="../subs.js"></script>
$HDRHTML
<STYLE><!--
   .ttip {border:1px solid black;font-family:Verdana,Arial,Helvetica,sans-serif;font-size:10px;layer-background-color:lightyellow;background-color:lightyellow}
--></STYLE>
<script><!--
function popUp(URL) {
day = new Date();
id = day.getTime();
w=870;
h=675;
l=(scrWidth()-w-30)/2;
t=(scrHeight()-h)/2-18;
eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=' + w + ',height=' + h + ',left = ' + l + ',top =' +  t)");
}
--></script>
</head>
<body$backimage$focus bgcolor="#FFFFFF" text="#000000" link="#0066CC" vlink="#333333" alink="#666666">
<div id="tooltip" style="position:absolute;visibility:hidden;border:1px solid 
black;font-size:10px;layer-background-color:lightyellow;background-color:lightyellow;padding:1px;font-family:Verdana,Arial,Helvetica,sans-serif;"></div>
END;
?>
