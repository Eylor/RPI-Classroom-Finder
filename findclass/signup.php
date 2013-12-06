<?php
//
//	Signup form for find my class site
//	
//
if (!isset($_POST['seid'])) exit;
if (!isset($_POST['sHandle'])) exit;
if (!isset($_POST['sEmailAddr'])) exit;
if (!isset($_POST['sPassword'])) exit;
if (!isset($_POST['sRePassword'])) exit;
if (!isset($_POST['sCell'])) exit;
if (!isset($_POST['sFirst'])) exit;
if (!isset($_POST['sLast'])) exit;
$seid=str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_POST['seid']);
if ($seid!="") session_id($seid);
$handle=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['sHandle']));
$email=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['sEmailAddr']));
$pass=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['sPassword']));
$cell=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['sCell']));
$first=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['sFirst']));
$last=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['sLast']));
$city=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['sCity']));
$zip=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['sZip']));
$state=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['sState']));
///$answer=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['sAnswer']));
if ($handle=="" || $email=="") exit;
include("session.php");
include("common.php");
include("mobile.php");
$dh=new DB("findmyclass");
$dh->Query("select id from blacklist where email='$email'");
if ($dh->NumRows()>0) {
	print "black";
	exit;
}
//$dh->Query("select id from person where email='$email'");
$dh->Query("select id from person where handle='$handle'");
if ($dh->NumRows()>0) {
	print "already";
	exit;
}
if (0) {
$alpha=0;
$num=0;
$special=0;
$p=stripslashes($pass);
for ($i=0;$i<strlen($p);$i++) {
	$char=substr($p,$i,1);
	if (ctype_alpha($char)) {
		$alpha=1;
	} else {
		if (is_numeric($char)) {
			$num=1;
		} else {
			if (strpos('a?|\`~();{[}]<>"'."'",$char)) {
				print "badchar";
				exit;
			}
			$special=1;
		}
	}
}
if ($alpha+$num+$special<2) {
	print "weak";
	exit;
}
}
$now=date("Y-m-d H:i:s");
$lastviewed=time();
$pass=encStr(stripslashes($pass));
$dh->Exec("insert into person (password,lastname,firstname,active,email,type,address,city,state,postal_code,phone1,phone2,startdate,enddate,lastactivity,lastlogged,ta,ba,lastviewed,pagesize,updocs,canpost,galrev,docrev,balance,credits,invoice,handle) values ('$pass','$last','$first','Y','$email','us','','$city','$state','$zip','','$cell','$now','0000-00-00 00:00:00','$now','$now','0','0','$lastviewed','25','Y','Y','Y','Y','0.00','0.00','N','$handle')");
$userid=$dh->LastID();
$fullname=stripslashes($last).", ".stripslashes($first);
	$ip=$_SERVER['REMOTE_ADDR'];
	$_SESSION['valid']=$userid;
	$_SESSION['screenname']=$email;
	$_SESSION['ip']=$_SERVER['REMOTE_ADDR'];
	$_SESSION['type']=$_SESSION['usertype']="us";
	$_SESSION['email']=$email;
	$_SESSION['name']=$fullname;
	$_SESSION['pagesize']=25;
	$_SESSION['cell']=stripslashes($cell);
	$date=date('Y-m-d H-i-s');
	$dh->Exec("insert into trxlog (acttime,type,program,user) values ('$date','1','1','$userid')");
	$sid=session_id();
	$_SESSION['lastviewed']=$date;
	$_SESSION['canpost']="Y";
	$_SESSION['updoc']="Y";
	$_SESSION['docrev']="Y";
	$_SESSION['galrev']="Y";
	$_SESSION['state']="PA";
	$_SESSION['is_mobile']=1;
	$handle=$_SESSION['handle']=stripslashes($handle);
	$md=new Mobile_Detect();
	if (!$md->isMobile()) $_SESSION['is_mobile']=0;
print "$userid-$handle";
exit;

