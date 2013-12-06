<?php
//
//	Return any user ID
//
if (!isset($_GET['x'])) exit;
include("session.php");
$uid="0";
if (isset($_SESSION['valid']) && $_SESSION['valid']) $uid=$_SESSION['valid'];
print $uid;

