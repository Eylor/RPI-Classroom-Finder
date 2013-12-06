<?php
//
//	Save New Schedule Entry by looking up crn in database and saving to the users schedule databast table
//	
if (!isset($_POST['seid'])) exit;
if (!isset($_POST['crn'])) exit;
$seid=str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_POST['seid']);
$crn=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['crn']));
session_id();
include("session.php");
include("common.php");
include("getschedule.php");
$dh=new DB('findmyclass');
if (!isset($_SESSION['valid'])) exit;
$uid=$_SESSION['valid'];
if (!$uid) exit;
$dh->Query("select id from class where crn='$crn'");
if ($dh->NumRows()==0) {
	print "bad";
	exit;
}
list($cid)=$dh->FetchRow();
$dh->Query("select id from user2schedule where user_id='$uid' && class_id='$cid'");
if ($dh->NumRows()>0) {
	print "already";
	exit;
}
$dh->Exec("insert into user2schedule(user_id,class_id) values('$uid','$cid')");
getSchedule($uid);
