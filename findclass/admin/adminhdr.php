<?php
//
//	Output the admin header
//	Copyright 2012 by Michael Lewis
//	All Rights Reserved
//
function getAdminHeader($hdr) {
	$html=$title="";
	if (isset($hdr['title'])) $title=$hdr['title'];
	if (isset($hdr['html'])) $html=$hdr['html'];
	$txt=<<< END
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>$title</title>
<link rel="stylesheet" href="../css/jquery-ui.css" />
<link rel="stylesheet" href="../css/default.css" />
<link rel="stylesheet" href="../css/jquery.timepicker.css" />
<script src="../js/jquery.js"></script>
<script src="../js/jquery-ui.js"></script>
<script src="../js/jquery.timepicker.min.js"></script>
$html
</head>
<body style="margin:0px">
<table width=100% cellpadding=0 cellspacing=0><tr bgcolor="#1040ff"><td valign=top>
<p style="color:white;letter-spacing:12px;font-size:14pt;font-family:Trebuchet,Verdana,Arial,Helvetica,sans-serif;height:20px">&nbsp;<b>Poker League for Us Administration</b></td></tr>
<tr style="height:12px;"><td bgcolor=yellow valign=top></td></tr>
</table></center>
END;
	return $txt;
}
