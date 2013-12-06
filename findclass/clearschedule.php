<?php
//
//	Clear Class schedule for user
//
include("session.php");
include("common.php");
if (isset($_SESSION['valid']) && $_SESSION['valid']) {
	$uid=$_SESSION['valid'];
	$dh=new DB("findmyclass");
	$dh->Exec("delete from user2schedule where user_id='$uid'");
}

