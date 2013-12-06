<?php
//
//	Actually make schedule changes here
//	Copyright 2012 by Michael Lewis
//	All Rights Reserved
//
if (!isset($_POST['scid'])) exit;
if (!isset($_POST['ad'])) exit;
$scid=str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_POST['scid']);
$ad=str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_POST['ad']);
include("/home/hss/session.php");
include("/home/hss/common.php");
include("/home/hss/mailer.php");
include("/home/hss/sendsms.php");
isLoggedIn("am;dv");
$dh=new DB("sportsnet");
$dh1=new DB("sportsnet");
$uid=$_SESSION['valid'];
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
	$dh->Query("select * from schedule_change where id='$scid'");
	$r=$dh->FetchArray();
	$gamedate=$r['game_date'];
	$gametime=$r['game_time'];
	$schid=$r['sch_id'];
	$oid=$r['o_id'];
	$delete=$r['delete_entry'];
	$ip=$r['ip'];
	$notes=stripslashes($r['notes']);
	$away=$r['away'];
	$bywho=$r['bywho'];
	$requesttime=$r['request_time'];
	$dh1->Query("select name from school where id='$oid'");
	list($oname)=$dh1->FetchRow();
	$oname=stripslashes($oname);
	$dh1->Query("select * from schedule where id='$schid'");
	$rs=$dh1->FetchArray();
	$sid=$rs['s_id'];
	$spid=$rs['sport_id'];
	$ogamedate=$rs['game_date'];
	$ogametime=$rs['game_time'];
	$oaway=$rs['away'];
	$ooid=$rs['o_id'];
	$dh1->Query("select name from school where id='$ooid'");
	list($ooname)=$dh1->FetchRow();
	$ooname=stripslashes($ooname);
	$dh1->Query("select * from school where id='$sid'");
	$rsc=$dh1->FetchArray();
	$sname=stripslashes($rsc['name']);
	$scity=stripslashes($rsc['city']);
	$sstate=stripslashes($rsc['state']);
	$dh1->Query("select * from sport where id='$spid'");
	$rsp=$dh1->FetchArray();
	$gender=stripslashes($rsp['gender']);
	$level=stripslashes($rsp['level']);
	$sport=stripslashes($rsp['name']);
	$dh1->Query("select lastname,firstname,email,phone2 from person where id='$bywho'");
	list($ln,$fn,$email,$cn)=$dh1->FetchRow();
	$ln=stripslashes($ln);
	$fn=stripslashes($fn);
	$email=stripslashes($email);
	$cn=stripslashes($cn);
	$rt=fmtDateTime($requesttime,'dty');
	$ap=array();
	$ap['s_id']=$sid;
	$ap['sport_id']=$spid;
	$ap['wc']="";
	$ap['ident']="$sname $gender $level $sport";
	$ap['ogd']=$ogamedate;
	$ap['game_date']=$gamedate;
	$ap['ogt']=$ogametime;
	$ap['game_time']=$gametime;
if ($ad=="D") {
	$dh1->Exec("delete from schedule_change where id='$scid'");
	$html=<<< END
$emailheader
<h1>HSS4.ME Schedule Change Rejected</h1>
<p>The schedule change request that you submitted for $sname $gender $level on $rt to delete that schedule entry has been denied. This could be for any number of reasons including that the change
has already been submitted by someone else or that we are unable to verify the information. Thank you for your submission. Your participation helps everyone who uses the system</p>
$emailfooter
END;
	putEmail($html,"HSS4.ME Delete Schedule Change Rejected",$email,"$fn $ln",$ap);
	putSMS("HSS4.ME delete schedule change request rejected.",$cn,$ap);
	exit;
}

if ($delete=="Y") {
	$dh1->Exec("delete from schedule_change where id='$scid'");
	$dh1->Exec("delete from schedule where id='$schid'");
	$html=<<< END
$emailheader
<h1>HSS4.ME Schedule Change Confirmation</h1>
<p>The schedule change request that you submitted for $sname $gender $level on $rt to delete that schedule entry has been approved. The change has been made in our database. Thank you for your submission. Your participation
helps everyone who uses the system</p>
$emailfooter
END;
	$ap['wc']="Game vs. $ooname deleted from schedule.";
	putEmail($html,"HSS4.ME Schedule Change Approval",$email,"$fn $ln",$ap);
	putSMS("HSS4.ME submitted schedule change approved.",$cn,$ap);
	exit;
}
$dh1->Exec("delete from schedule_change where id='$scid'");
$html=<<< END
$emailheader
<h1>HSS4.ME Schedule Change Confirmation</h1>
<p>The schedule change request that you submitted for $sname $gender $level on $rt has been approved. The changes you requested that were approved are:<ul>
END;
$chg="";
if ($gamedate!=$ogamedate) {
	$chg.=",game_date='$gamedate'";
	$html.="<li>Game date changed to ".fmtDateTime($gamedate,'dy')."</li>";
	$ap['wc'].="|Game date changed to ".fmtDateTime($gamedate,'dy');
}
if ($gametime!=$ogametime) {
	$chg.=",game_time='$gametime'";
	$html.="<li>Game time changed to $gametime</li>";
	$ap['wc'].="|Game time changes to $gametime";
}
if ($away!=$oaway) {
	$chg.=",away='$away'";
	if ($oaway=="Y") {
		$html.="<li>Changed to a HOME game</li>";
		$ap['wc'].="|Changed to a HOME game";
	} else {
		$html.="<li>Changed to an AWAY game</li>";
		$ap['wc'].="|Changed to an AWAY game";
	}
}
if ($oid!=$ooid) {
	$chg.=",o_id='$oid'";
	$html.="<li>Opponent changed to $oname</li>";
	$ap['wc'].="|Opponent changed to $oname";
}
$now=date("Y-m-d H:i:s");
$dh1->Exec("update schedule set last_modified='$now'$chg where id='$schid'");
$html.=<<< END
</ul>
The change has been made in our database. Thank you for your submission. Your participation helps everyone who uses the system</p>
$emailfooter
END;
putEmail($html,"HSS4.ME Schedule Change Approval",$email,"$fn $ln",$ap);
putSMS("HSS4.ME schedule change request approved",$cn,$ap);
exit;
//
//	Put an email out
//
function putEmail($html,$subject,$pto,$toname,$ap) {
	global $emailheader,$emailfooter,$dh1,$dh;
	
	$body="You need an HTML email client to view this email (such as Outlook Express or Safari)";
	if (strpos($pto,"@")) sendMail("DO-NOT-REPLY@hss4.me","hss4.me Web Site",$pto,$toname,$subject,$body,$html,"");
	if ($ap['wc']=="") return;
	$sid=$ap['s_id'];
	$spid=$ap['sport_id'];
	$dh1->Query("select * from alert_list where s_id='$sid' && sport_id='$spid'");
	if  ($dh1->NumRows()==0) return;
	if (substr($ap['wc'],0,1)=="|") $ap['wc']=substr($ap['wc'],1);
	$ca=array($ap['wc']);
	$ident=$ap['ident'];
	if (strpos($ap['wc'],"|")>0) $ca=explode("|",$ap['wc']);
	$list="";
	for ($i=0;$i<count($ca);$i++) {
		$list.="<li>$ca[$i]</li>";
	}
	$gd=fmtDateTime($ap['ogd'],'dy');
	$gt=$ap['ogt'];
	$html=<<< END
$emailheader
<h1>Schedule Change Alert Notification</h1>
<p>The scheduled game for $ident has been changed as follows for the game originally scheduled for $gd at $gt:<ul>
$list
</ul>
$emailfooter
END;
	$now=date("Y-m-d H:i:s");
	for ($i=0;$i<$dh1->NumRows();$i++) {
		$r=$dh1->FetchArray();
		$aid=$r['id'];
		if ($r['email']!="Y") continue;
		$userid=$r['user_id'];
		$dh->Query("select firstname,lastname,email,phone2 from person where id='$userid'");
		list($firn,$lasn,$pto,$ph2)=$dh->FetchRow();
		$firn=stripslashes($firn);
		$lasn=stripslashes($lasn);
		$pto=stripslashes($pto);
		$ph2=stripslashes($ph2);
		$toname="$firn $lasn";
		if (strpos($pto,"@")) sendMail("DO-NOT-REPLY@hss4.me","hss4.me Web Site",$pto,$toname,"Schedule Change",$body,$html,"");
		$dh->Exec("update alert_list set last_alert='$now' where id='$aid'");
	}
}
//
//	Put an SMS out
//
function putSMS($msg,$cn,$ap) {
	global $dh,$dh1;
	
	if (strlen($cn)>6) sendSMS("hss4me",$msg,0,array(),array($cn));
	if ($ap['wc']=="") return;
	$sid=$ap['s_id'];
	$spid=$ap['sport_id'];
	$dh1->Query("select * from alert_list where s_id='$sid' && sport_id='$spid'");
	if  ($dh1->NumRows()==0) return;
	if (substr($ap['wc'],0,1)=="|") $ap['wc']=substr($ap['wc'],1);
	$ca=array($ap['wc']);
	if (strpos($ap['wc'],"|")>0) $ca=explode("|",$ap['wc']);
	$ident=$ap['ident'];
	$list="";
	$sep="";
	$gd=fmtDateTime($ap['ogd'],'d');
	$gt=$ap['ogt'];
	for ($i=0;$i<count($ca);$i++) {
		$list.="$sep$ca[$i]";
		$sep=", ";
	}
	$t=<<< END
$ident schedule change for $gd @ $gt: $list
END;
	$now=date("Y-m-d H:i:s");
	for ($i=0;$i<$dh1->NumRows();$i++) {
		$r=$dh1->FetchArray();
		$aid=$r['id'];
		$userid=$r['user_id'];
		if ($r['cell']!="Y") continue;
		$dh->Query("select phone2 from person where id='$userid'");
		list($cnum)=$dh->FetchRow();
		$cnum=stripslashes($cnum);
		if (strlen($cnum)>6) sendSMS("hss4me",$t,0,array(),array($cnum));
		$dh->Exec("update alert_list set last_alert='$now' where id='$aid'");
	}
}
