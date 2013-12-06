<?php
//
//	Save User Questions and Answers
//
if (!isset($_POST['seid'])) exit;
if (!isset($_POST['mEmailAddr'])) exit;
if (!isset($_POST['mFirst'])) exit;
if (!isset($_POST['mLast'])) exit;
if (!isset($_POST['mCity'])) exit;
if (!isset($_POST['mState'])) exit;
if (!isset($_POST['mZip'])) exit;
if (!isset($_POST['mCell'])) exit;
$seid=str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_POST['seid']);
$email=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['mEmailAddr']));
$fn=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['mFirst']));
$ln=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['mLast']));
$city=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['mCity']));
$state=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['mState']));
$zip=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['mZip']));
$cell=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['mCell']));
session_id($seid);
include("session.php");
include("common.php");
$dh=new DB('poker');
if (!isset($_SESSION['valid'])) exit;
$uid=$_SESSION['valid'];
if (!$uid) exit;
$extra="";
if (isset($_POST['myaNewPW']) && strlen($_POST['myaNewPW'])>0) {
	$pw=$_POST['myaOldPW'];
	$dh->Query("select password from person where id='$uid'");
	list($tpwd)=$dh->FetchRow();
	if (stripslashes(decStr($tpwd))!=$pw) {
		print "Bad";
		exit;
	}
	$pw=encStr(addslashes($_POST['myaNewPW']));
	$extra=",password='$pw'";
}
$sql="update person set email='$email',firstname='$fn',lastname='$ln',city='$city',state='$state',postal_code='$zip',phone2='$cell'$extra where id='$uid'";
$dh->Exec($sql);
print "Ok";

