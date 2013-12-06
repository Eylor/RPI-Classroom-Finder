<?php
//
//	Process user agreement to terms and conditions
//
if (!isset($_POST['seid'])) exit;
$seid=str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_POST['seid']);
session_id($seid);
include("session.php");
include("common.php");
$dh=new DB('poker');
if (!isset($_SESSION['valid'])) exit;
$uid=$_SESSION['valid'];
if (!$uid) exit;
$aa=array();
$totalglb=0;
$totaldppa=0;
foreach ($_POST as $k=>$v) {
	if (substr($k,0,3)=="glb") {
		if ($k == "glbNouse") {
			$aa=array();
			break;
		}
		$totalglb++;
		$aa[]=$k;
	} else {
		if (substr($k,0,4)=="dppa") {
			if ($k == "dppaNouse") {
				$aa=array();
				break;
			}
			$totaldppa++;
			$aa[]=$k;
		}
	}
}
if (count($aa)==0 || $totalglb==0 || $totaldppa==0) {
	print "Bad";
	exit;
}
$agreeto=join(",",$aa);
$ip=$_SERVER['REMOTE_ADDR'];
$now=date("Y-m-d H:i:s");
$dh->Exec("insert into fc_agree (user_id,when_agreed,ip_address,agreed_to) values('$uid','$now','$ip','$agreeto')");
$dh->Exec("update person set first_time='0' where id='$uid'");
print "Ok";

