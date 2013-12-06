<?php
//
//	Change a schedule entry
//	Copyright 2013 by Michael A. Lewis
//	All Rights Reserved
//
if (!isset($_POST['seid'])) exit;
if (!isset($_POST['crn'])) exit;
if (!isset($_POST['oldid'])) exit;
$seid=str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_POST['seid']);
$crn=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['crn']));
$oldid=addslashes(str_replace(array(';','\r','\n','<','>','&','|'),"",$_POST['oldid']));
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
list($newid)=$dh->FetchRow();

$dh->Query("select id from user2schedule where user_id='$uid' && class_id='$newid'");
if ($dh->NumRows()>0) {
	print "already";
	exit;
}
$dh->Query("select id from user2schedule where user_id='$uid' && class_id='$oldid'");
list($id)=$dh->FetchRow();
if ($dh->NumRows()>0) {
	$dh->Exec("update user2schedule set class_id='$newid' where id='$id'");
}
getSchedule($uid);
