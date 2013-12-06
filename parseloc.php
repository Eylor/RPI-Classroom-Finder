<?php
//
//	Parse entrance location information
//
include("findclass/new-dbc.php");

$dh=new DB("findmyclass");

$f=file_get_contents("bed.csv");
$f=str_replace("\r","",$f);
$fa=explode("\n",$f);

for($i=0;$i<count($fa);$i++) {
	$l=trim($fa[$i]);
	if (strlen($l)<6) continue;
	list($bname,$lat,$lng,$eid,$tm)=explode(",",$l);
	$dh->Query("select id,lat from building where name='$bname'");
	if ($dh->NumRows()>0) {
		list($bid,$lt)=$dh->FetchRow();
		$dh->Exec("update entrance2buildings set lat=$lat,lng=$lng where building_id='$bid' && e_id='$eid'");;
		if ($lt==0) $dh->Exec("update building set lat='$lat',lng='$lng' where id='$bid'");
	} else {
		$bn=addslashes($bname);
		$dh->Exec("insert into building (name,lat,lng,grid,notes,img,human_name) values('$bn',$lat,$lng,0,'','','')");
		$bid=$dh->LastID();
		print "Adding building $bname\n";
	}
	$dh->Query("select id from entrance2buildings where building_id='$bid' && e_id='$eid'");
	if ($dh->NumRows()>0) {
		print "Skipping $bname entrance $eid, already on file\n";
		continue;
	}
	$dh->Exec("insert into entrance2buildings (building_id,lat,lng,e_id) values('$bid','$lat','$lng','$eid')");
	print "	added entrance $eid to $bname\n";
}
