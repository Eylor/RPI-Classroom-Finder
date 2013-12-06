<?php
//
//	Fetch class schedule for an user
//
include("session.php");
include("common.php");
include("getschedule.php");
if (isset($_SESSION['valid']) && $_SESSION['valid']) {
	$uid=$_SESSION['valid'];
	getSchedule($uid);
}

