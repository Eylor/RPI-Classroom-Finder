<?php
//
//	Get a schedule formatted into html for caller
//
function getSchedule($uid) {
	$dh=new DB("findmyclass");
	$dh1=new DB("findmyclass");
	
	$dh->Query("select class_id from user2schedule where user_id='$uid'");
	$c=explode(",","25%,75%");
	$fs="11px";
	$hfs="13px";
	if ($_SESSION['is_mobile']==0) {
		$c=explode(",","20%,80%");
		$fs="16px";
		$hfs="19px";
	}
	print <<< END
<style>
.hdr {font-size:$hfs}
.txt {font-size:$fs}
</style>
<table cellspacing=1 cellpadding=2 width=100%>
<tr>
<td width="$c[0]" valign="top" class=hdr>CRN</td>
<td width="$c[1]" valign="top" class=hdr>Class Information</td>
</tr>
END;
	for ($i=0;$i<$dh->NumRows();$i++) {
		list($cid)=$dh->FetchRow();
		$dh1->Query("select crn,title,instructor,meets from class where id='$cid'");
		list($crn,$tit,$ins,$meets)=$dh1->FetchRow();
		$bgcolor="";
		$crn=stripslashes($crn);
		$tit=stripslashes($tit);
		$ins=stripslashes($ins);
		$meets=stripslashes($meets);
		if ($i & 1) $bgcolor=' bgcolor="#e8e8e8"';
		print <<< END
<tr$bgcolor onclick="changeCRN('$crn',$cid);" onmouseover="setCursor(this);" onmouseout="clearCursor(this);">
<td width="$c[0]" valign="top" class=txt>$crn</td>
<td width="$c[1]" valign="top" class=txt>$tit<br>Instructor: $ins<br>Meets: $meets</td>
</tr>
END;
	}
	print "</table>";
}
