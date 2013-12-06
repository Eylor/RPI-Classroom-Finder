<?php
//
//	route to classroom
//	Copyright 2013 by Michael Lewis
//	All Rights Reserved
//
include("session.php");
include("common.php");
if (!isset($_POST['bid'])) exit;
$bid=str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_POST['bid']);
$dh=new DB("findmyclass");
$dh->Query("select * from entrance2buildings where building_id='$bid'");
$entArr=Array();
for ($j=0;$j<$dh->NumRows();$j++) {
        $tempEnt=$dh->FetchArray();
	$entArr[$j]=Array();
        $entArr[$j][0]=$tempEnt['lat'];
        $entArr[$j][1]=$tempEnt['lng'];
}
$entArr = json_encode($entArr);

print <<< END
$entArr
END;
