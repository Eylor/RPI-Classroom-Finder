<?php
/*
	Mobile Login to system
	All Rights Reserved.
*/
if (!isset($_POST['emailAddr'])) exit;
if (!isset($_POST['password'])) exit;
if (!isset($_POST['seid'])) exit;
$seid=str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_POST['seid']);
$sn=str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_POST['emailAddr']);
$pass=str_replace(array(';','\r','\n','<','>'),"",$_POST['password']);
session_id($seid);
include("session.php");
include("common.php");
include("class.phpmailer.php");
include("mobile.php");
$dh=new DB('findmyclass');
$goto="";
$when=date('Y-m-d H:i:s');
$id=0;
$sn = htmlspecialchars(addslashes($sn), ENT_QUOTES);
$pass = htmlspecialchars(addslashes($pass), ENT_QUOTES);
$dh->Query("select * from person where email='$sn' || handle='$sn' && active='Y'");
$bad=0;
$numrows=$dh->NumRows();
if ($numrows==0) {
	$bad=1;
} else {
	$r=$dh->FetchArray();
	$expires=$r['enddate'];
	if ((substr($expires,0,4)!="0000" && strlen($expires)) && $expires<date('Y-m-d')) {
		$bad=1;
	} else {
		$ta=$r['ta'];
		$id=$r['id'];
		if ($ta>0 && $ta>time()) {
			$ns=$ta-time();
			$mins=$ns/60;
			print "wait:$mins";
			exit;
		}
		if ($pass!=decStr($r['password'])) {
			$ba=$r['ba'];
			$ba++;
			$ip=$_SERVER['REMOTE_ADDR'];
			$dt=date('Y-m-d H:i:s');
			$result=1;
			$ta=0;
			if ($ba>=8) {
				$ns=($ba-4)*1800;
				$ta=time()+$ns;
				$result=2;
				$username=$r['firstname']." ".$r['lastname'];
				$tele=$r['phone1'];
				$email=$r['email'];
				$to=$supportemail;
				$toname=$supportname;
				$subject="SECURITY WARNING: Too many login attempts!";
				$from=$to;
				$fromname="Webmaster";
				$ip=$_SERVER['REMOTE_ADDR'];
				$body="\nSECURITY ALERT!\n\nSomeone is attempting to log into Pokerleague4.us with the screen name '$sn' $ba times without a valid password.";
				$body.="\n\nAccount information:\nReal name: $username\nTelephone: $tele\nEmail address: $email\nIP Address: $ip\n\n";
				$mins=$ns/60;
				$body.=" The account has been frozen for $mins minutes. Each subsequent bad login will result in";
				$body.=" increasing the time the account is frozen by 30 minutes. Note that this may be a confused user or someone trying to hack a user's password.";
				$mail=new phpmailer();
				$mail->Host="mail.xelent.net";
				$mail->Mailer="mail";
				$mail->WordWrap=75;
				$mail->AddAddress($to,$toname);
				$mail->AltBody=$body;
				$mail->Body=str_replace("\n","<br>",$body);
				$mail->Subject=$subject;
				$mail->From=$to;
				$mail->FromName=$fromname;
				if (!$mail->Send()) {
					print "Could not send message";
					exit;
				}
			}
			$dh->Exec("update $database_name.person set ba='$ba',ta='$ta' where id='$id'");
			$dh->Exec("insert into $database_name.badlog (sn,ip,attwhen,result,pass) values ('$sn','$ip','$dt','$result','$pass')");
			$bad=2;
		}
	}
}
if ($bad) {
	$header="Cannot find you on file. Check your email address and password carefully. Make sure your CAPS LOCK key isn't on.";
} else {
	$dh->Exec("update $database_name.person set ba='0',ta='0' where id='$id'");
	$screenname=$r['email'];
	$tim=strlen($id).$id.date('U');
	$type=strtolower($r['type']);
	$email=$r['email'];
	$noton=$r['noton'];
	$firsttime=$r['first_time'];
	$fullname=stripslashes($r['lastname']).", ".stripslashes($r['firstname']);
	$ip=$_SERVER['REMOTE_ADDR'];
///	if ($noton!=1) {
///		$lastactivity=$r['lastactivity'];
///		$dh->Exec("insert into $database_name.trxlog (acttime,type,program,user) values('$lastactivity','2','2','$id')");
///	}
	$_SESSION['valid']=$id;
	$_SESSION['screenname']=$screenname;
	$_SESSION['ip']=$_SERVER['REMOTE_ADDR'];
	$_SESSION['type']=$_SESSION['usertype']=$type;
	$_SESSION['email']=$email;
	$_SESSION['name']=$fullname;
	$_SESSION['pagesize']=$r['pagesize'];
	$_SESSION['cell']=stripslashes($r['phone2']);
	$date=date('Y-m-d H-i-s');
	$_SESSION['lastlogged']=$r['lastlogged'];
	$dh->Exec("update $database_name.person set LASTLOGGED='$date', NOTON=0,first_time=0 where id='$id'");
	$dh->Exec("insert into $database_name.trxlog (acttime,type,program,user) values('$date','1','1','$id')");
	$uid=$_SESSION['valid'];
	$_SESSION['lastviewed']=$r['lastviewed'];
	$_SESSION['canpost']=$r['canpost'];
	$_SESSION['updoc']=$r['updocs'];
	$_SESSION['docrev']=$r['docrev'];
	$_SESSION['galrev']=$r['galrev'];
	$_SESSION['state']=$r['state'];
	$handle=$_SESSION['handle']=stripslashes($r['handle']);
	$_SESSION['is_tablet']=0;
	$_SESSION['is_mobile']=1;
	$md=new Mobile_Detect();
	if ($md->isTablet()) {
		$_SESSION['is_tablet']=1;
		$_SESSION['is_mobile']=0;
	} else {
		if (!$md->isMobile()) $_SESSION['is_mobile']=0;
	}
	print "$uid-$firsttime-$type-$handle";
	exit;
}
print 0;
exit;

