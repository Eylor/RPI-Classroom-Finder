<?php
//
//	Log off the system
//
if (!isset($_GET['seid'])) exit;
session_id($_GET['seid']);
include("session.php");
include("common.php");
if (isset($_SESSION['valid'])) {
	$id=$_SESSION['valid'];
	$_SESSION['valid']=0;
	$dh=new DB("findmyclass");
	$sql="update person set noton='1' where id='$id'";
	$dh->Exec($sql);
}
$when=date('Y-m-d H:i:s');
$_SESSION['valid']=0;
unset($_SESSION['valid']);
///session_destroy();
$_SESSION['usertype']="us";
print "OFF";

