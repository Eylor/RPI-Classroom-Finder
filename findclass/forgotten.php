<?php
//
//	Forgotten Password Program
//
include("session.php");
include("common.php");
include("mailer.php");
$dh=new DB("findmyclass");
if (!isset($_POST['emailAddr'])) exit;
$em=str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['emailAddr']);
$email = addslashes($em);
$sel="select * from person where email='$email'";
$dh->Query($sel);
$num=$dh->NumRows();
if ($num==0) {
	print "Bad";
	exit;
}
$body="User Name(s) and Password(s) that are on file for email address $em: ";
for ($i=0;$i<$dh->NumRows();$i++) {
	$r=$dh->FetchArray();
	$password=stripslashes(decStr($r['password']));
	$username=stripslashes($r['handle']);
	$body.="$username -- $password; ";
}
$firstname=stripslashes($r['firstname']);
$lastname=stripslashes($r['lastname']);
$name=$firstname.' '.$lastname;
$to=$em;
$toname=$name;
$from="DO-NOT-REPLY@findmyclass4.me";
$fromname="findmyclass4.me Web Site Support";
$subject="Forgotten Password";
$HTML="";
sendMail($from,$fromname,$to,$toname,$subject,$body,$HTML,$HTML);
print "Ok";
exit;

