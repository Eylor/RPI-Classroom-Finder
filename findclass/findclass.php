<?php
//
//	Return names for search
//
include("session.php");
include("common.php");
if (!isset($_POST['tn'])) exit;
$tn=str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_POST['tn']);
$dh=new DB("findmyclass");
$dh1=new DB("findmyclasss");
$tn=addslashes($tn);
$txt="";
$mobile=0;
$twid="900px";
if ($_SESSION['is_mobile']>0) {
        $mobile=1;
        $twid="100%";
}
$dh->Query("select * from class where title like '%$tn%' || prefix like '$tn%' || crn='$tn' || course_num like '$tn%' limit 20");
if ($dh->NumRows()) {
        $txt=<<< END
<div style="width:$twid">
<ul data-role="listview" data-inset="true" data-theme="d" data-divider-theme="b" data-split-icon="search">
<li data-role="list-divider">Select Class</li>
END;
	for ($i=0;$i<$dh->NumRows();$i++) {
		$r=$dh->FetchArray();
		$cid=$r['id'];
		$name=stripslashes($r['title']);
		$desc=stripslashes($r['des']);
		$crn=stripslashes($r['crn']);
		$cn=stripslashes($r['course_num']);
		$classtype=stripslashes($r['class_type']);
		$prefix=stripslashes($r['prefix']);
		$dh1->Query("select room_id from class2room where class_id='$cid'");
		list($rid)=$dh1->FetchRow();
		$dh1->Query("select building_id,name,notes from room where id='$rid'");
		list($bid,$rname,$rnotes)=$dh1->FetchRow();
		$rname=stripslashes($rname);
		$rnotes=stripslashes($rnotes);
		$dh1->Query("select name,lat,lng,grid,notes from building where id='$bid'");
		list($bname,$lat,$lng,$grid,$bnotes)=$dh1->FetchRow();
		$bname=stripslashes($bname);
		$bnotes=stripslashes($bnotes);
		$txt.=<<< END
<li><a style="text-decoration: none;" href="javascript:showClassDesc($cid);" title="Tap or Click to display class information"><h4>$name</h4><p>$prefix-$cn ($classtype) $bname $rname</p><p>$desc</p></a><a href="javascript:getClassDirections($cid);">Get Directions</a></li>
END;
	}
	$txt.="</ul></div>";
}
print $txt;
