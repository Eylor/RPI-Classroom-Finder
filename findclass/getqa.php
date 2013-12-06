<?php
//
//	Save User Questions and Answers
//
include("session.php");
include("common.php");
$dh=new DB('findmyclass');
if (!isset($_SESSION['valid'])) exit;
$uid=$_SESSION['valid'];
if (!$uid) exit;
$dh->Query("select email,firstname,lastname,city,state,postal_code,phone2 from person where id='$uid'");
list($email,$fn,$ln,$city,$state,$zip,$cell)=$dh->FetchRow();
$email=stripslashes($email);
$fn=stripslashes($fn);
$ln=stripslashes($ln);
$city=stripslashes($city);
$state=stripslashes($state);
$zip=stripslashes($zip);
$cell=stripslashes($cell);
print "$email|$fn|$ln|$city|$state|$zip|$cell";

