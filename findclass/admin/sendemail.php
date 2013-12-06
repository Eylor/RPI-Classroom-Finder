<?php
//
//	Send email
//	Copyright 2013 by Michael A. Lewis
//	All Rights Reserved
//
include("../session.php");
include("../common.php");
if (isset($_POST['emailSubject'])) $_POST['s']=$_POST['emailSubject'];
if (isset($_POST['emailBody'])) $_POST['n']=$_POST['emailBody'];
if (!isset($_POST['tid'])) exit;
if (!isset($_POST['n'])) exit;
if (!isset($_POST['s'])) exit;
if (!isset($_POST['t'])) exit;
$tid=addslashes(str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_POST['tid']));
$p=addslashes(str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_POST['t']));
$subject=trim(urldecode($_POST['s']));
$subject=str_replace("%u2019","'",$subject);
$text=trim(urldecode($_POST['n']));
$text=str_replace("\n","<br>",$text);
$text=str_replace("%u2019","'",$text);
if (!isset($_SESSION['valid']) || $_SESSION['valid']==0) {
	header('location: index.php');
	exit;
}
$utype=$_SESSION['type'];
if (stripos(" am;de;td;to",$utype)==0) {
	header('location: index.php');
	exit;
}
$dh=new DB("findmyclass");
$dh1=new DB("findmyclass");
$heading="Fan Mail from Some Flounder";
$tname=" a Poker League for Us Tavern";
switch ($p) {
	case "p":
		if ($tid=="0") {
			foreach ($_POST as $k=>$v) {
				if (substr($k,0,4)=="tid_") {
					$tid=$v;
					break;
				}
			}
			if ($tid=="0") exit;
		}
		$dh->Query("select name from tavern where id='$tid'");
		list($tname)=$dh->FetchRow();
		$tname=stripslashes($tname);
		$heading="For Players at $tname";
		break;
	case "td":
		$heading="To Tournament Directors";
		break;
	case "to":
		$heading="To Tavern Contacts";
		break;
	case "t":
		$heading="To Tavern Contacts and Tournament Directors";
		break;
}
$emailheader=<<< END
<html><head><meta http-equiv="Content-Language" content="en-us">      
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<style>
body,p,td,sup,ul,li,h1,h2,h3,h4 {
        font-family: Arial,Helvetica,sans-serif;
        font-size: 10pt;
        color: #000000;
}
h1 {
        font-size: 18pt;
        font-weight: normal;
        color: #000099;
        margin-bottom: -10px;
        border-bottom: 1px solid #CC0000;
        padding-bottom: 5px;
}
h2 {
        font-size: 16pt;
        font-weight: normal;
        color: #000099;
}
h3 {
        font-size: 14pt;
        font-weight: bold;
        color: #CC0000;
}
h4 {
        font-size: 12pt;
        font-weight: bold;
        color: #CC0000;
}
.highlight {
        background-color: #FFFF99;
        font-weight: bold;
}
.small {
	font-size:11px;
}
</style>
</head>
<body>
END;
$emailfooter=<<< END
</body>
</html>
END;
	$html=<<< END
$emailheader
<h1>$heading</h1>
<br>
$text
$emailfooter
END;
switch (strtolower($p)) {
	case "x":
		$where="";
		foreach($_POST as $k=>$v) {
			if (substr($k,0,4)=="tid_") {
				if ($where=="") {
					$where="select user_id from game_results where tavern_id='$v'";
				} else {
					$where.=" || tavern_id='$v'";
				}
			}
		}
		if ($where=="") exit;
		$dh->Query($where);
		break;		
	case "p":
		$dh->Query("select user_id from game_results where tavern_id='$tid'");
		break;
	case "a":
		$dh->Query("select id from person where active='Y'");
		break;
	case "t":
		$dh->Query("select id from person where type='to' || type='td'");
		break;
	case "to":
		$dh->Query("select id from person where type='to'");
		break;
	case "td":
		$dh->Query("select id from person where type='td'");
		break;
}
$msg=addslashes($html);
$subject=addslashes($subject);
$dh1->Exec("insert into email_message (subject,message) values('$subject','$msg')");
$msgid=$dh1->LastID();
$usera=array();
for ($i=0;$i<$dh->NumRows();$i++) {
	list($guid)=$dh->FetchRow();
	if (isset($usera[$guid])) continue;
	$usera[$guid]=1;
	$dh1->Exec("insert into user2msg (user_id,msg_id) values('$guid','$msgid')");
}
if ($i==0) {
	$dh1->Exec("delete from email_message where id='$msgid'");
}

