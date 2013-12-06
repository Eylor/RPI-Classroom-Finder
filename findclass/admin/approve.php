<?php
//
//	Approve/deny user-submitted schedule changes
//	Copyright 2012 by Michael Lewis
//	All Rights Reserved
//
include("../session.php");
include("../common.php");
isLoggedIn("am;dv");
$hdr['title']="Approve/Deny User-Submitted Schedule Changes";
$hdr['html']=<<< END
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script>
function setCursor(e) {
	e.style.cursor="pointer";
}
function clearCursor(e) {
	e.style.cursor="default";
}
function deny(scid,line) {
	$.post('approvedeny.php','ad=D&scid=' + scid,function(data) {
		$('#line_'+line).html(data);
	});
}
function approve(scid,line) {
	$.post('approvedeny.php','ad=A&scid=' + scid,function(data) {
		$('#line_'+line).html(data);
	});
}
</script>
END;
putMaintHeader($hdr);
$dh=new DB("sportsnet");
$dh1=new DB("sportsnet");
$dh->Query("select * from schedule_change order by request_time,bywho");
$nr=$dh->NumRows();
if ($nr==0) {
	print "<p>No changes currently pending</p>";
	putMaintFooter();
	exit;
}
$c=explode(",","25%,8%,5%,5%,20%,37%");
print <<< END
<table width=100% bgcolor="green" style="border-bottom:10px solid yellow" cellpadding=5 cellspacing=0><tr><td><p style="font-size:28px;font-weight:bold;color:white">Approve/Deny User Submitted Schedule Changes</p></td></tr></table>
<table width=100% cellpadding=3 cellspacing=2>
<tr>
<td width=$c[0] valign=top><b>School</b></td>
<td width=$c[1] valign=top><b>Game Date</b></td>
<td width=$c[2] valign=top><b>Time</b></td>
<td width=$c[3] valign=top><b>Away?</b></td>
<td width=$c[4] valign=top><b>Opponent</b></td>
<td width=$c[5] valign=top><b>Information</b></td>
</tr>
END;
$ls=0;
$lsp=0;
for ($i=0;$i<$nr;$i++) {
	$r=$dh->FetchArray();
	$scid=$r['id'];
	$schid=$r['sch_id'];
	$gamedate=fmtDateTime($r['game_date'],'dy');
	$gametime=$r['game_time'];
	$oid=$r['o_id'];
	$delete=$r['delete_entry'];
	$ip=$r['ip'];
	$notes=stripslashes($r['notes']);
	$away=$r['away'];
	$bywho=$r['bywho'];
	$requesttime=$r['request_time'];
	$dh1->Query("select name from school where id='$oid'");
	list($oname)=$dh1->FetchRow();
	$oname=stripslashes($oname);
	$dh1->Query("select * from schedule where id='$schid'");
	$rs=$dh1->FetchArray();
	$sid=$rs['s_id'];
	$spid=$rs['sport_id'];
	$ogamedate=fmtDateTime($rs['game_date'],'dy');
	$ogametime=$rs['game_time'];
	$oaway=$rs['away'];
	$ooid=$rs['o_id'];
	$dh1->Query("select * from school where id='$sid'");
	$rsc=$dh1->FetchArray();
	$sname=stripslashes($rsc['name']);
	$scity=stripslashes($rsc['city']);
	$sstate=stripslashes($rsc['state']);
	$dh1->Query("select * from sport where id='$spid'");
	$rsp=$dh1->FetchArray();
	$gender=stripslashes($rsp['gender']);
	$level=stripslashes($rsp['level']);
	$sport=stripslashes($rsp['name']);
	$dh1->Query("select lastname,firstname,email from person where id='$bywho'");
	list($ln,$fn,$email)=$dh1->FetchRow();
	$ln=stripslashes($ln);
	$fn=stripslashes($fn);
	$email=stripslashes($email);
	$rt=fmtDateTime($requesttime,'dyt');
	$info=<<< END
<img src="images/active20.png" title="Approve this submission" onmouseover="setCursor(this);" onmouseout="clearCursor(this);" onclick="approve($scid,$i);"><img style="padding-left:10px" src="images/cancel20.png" title="Deny this submission" onmouseover="setCursor(this);" onmouseout="clearCursor(this);" onclick="deny($scid,$i);">
<br>Submitted by $fn $ln<br>$email<br>From IP: $ip @ $rt<br>$notes
END;
	$ngamedate=$ngametime=$naway=$nopponent="&nbsp;";
	if ($gamedate!=$ogamedate) $ngamedate=$gamedate;
	if ($gametime!=$ogametime) $ngametime=$gametime;
	if ($away!=$oaway) $naway=$away;
	if ($oid!=$ooid) $nopponent=$oname;
	if ($delete=="Y") {
		print <<< END
<tr id=line_$i>
<td width=$c[0] valign=top>$sname $gender $level $sport</td>
<td width=$c[1] valign=top bgcolor="yellow">$gamedate<br><b>Delete this game</b></td>
<td width=$c[2] valign=top>$gametime</td>
<td width=$c[3] valign=top>$oaway</td>
<td width=$c[4] valign=top>$oname</td>
<td width=$c[5] valign=top>$info</td>
</tr>
END;
	} else {
		print <<< END
<tr id=line_$i>
<td width=$c[0] valign=top>$sname $gender $level $sport<br><span style='padding-left:200px'><b>Changed information</b></span></td>
<td width=$c[1] valign=top>$ogamedate<br><b>$ngamedate</b></td>
<td width=$c[2] valign=top>$ogametime<br><b>$ngametime</b></td>
<td width=$c[3] valign=top>$oaway<br><b>$naway</b></td>
<td width=$c[4] valign=top>$oname<b>$nopponent</b></td>
<td width=$c[5] valign=top>$info</td>
</tr>
END;
	}
}
print "</table>";
putMaintFooter();
