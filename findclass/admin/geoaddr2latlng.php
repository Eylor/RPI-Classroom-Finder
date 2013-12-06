<?php
//
//	Geo locate an address to get its lat/lng
//	Copyright 2011 by Michael A. Lewis
//	All Rights Reserved
//
include("common.php");
include("webpage.php");
if (!isset($_POST['address'])) exit;
$fulladdr=str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_POST['address']);
$page=webPage("http://maps.googleapis.com/maps/api/geocode/json?address=$fulladdr&sensor=false");
$i=stripos($page,"location");
if ($i) {
	$i=stripos($page,"lat",$i);
	if ($i) {
		$i=strpos($page,":",$i)+2;
		$j=strpos($page,",",$i);
		$lat=trim(substr($page,$i,$j-$i));
		$i=stripos($page,"lng",$j);
		if ($i) {
			$i=strpos($page,":",$i)+2;
			$j=strpos($page,"}",$i);
			$lng=trim(substr($page,$i,$j-$i));
		}
	}
}
print "$lat|$lng";
exit;
