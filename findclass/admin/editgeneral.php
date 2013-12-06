<?php
/*
	Back End Screen for Table general
	Copyright 2006 by Michael Lewis.
	All Rights Reserved.

Edit history:
	Created 06/08/2006 08:56:44
*/
include("../session.php");
include("../common.php");
$utype=$_SESSION['type'];
$hdr['title']="Maintain General Site Information";
$hdr['focus']="season";
$hdr['backimage']="fade.gif";
if (strpos(" am;",";")) {
	$usa=explode(";","am;");
} else {
	$usa[0]="am;";
}
if (!in_array($utype,$usa)) {
	header('location: index.php');
	exit;
}
$dh=new DB();
$cid="";
$pid=0;
$clid=0;
if ($_SESSION['cp']!="general.php") {
	header("location: index.php");
	exit;
}
$return="";
if (isset($_GET['id'])) $pid=$_GET['id'];
if (isset($_GET['idx'])) $cid=$_GET['idx'];
if (isset($_GET['clid'])) $clid=$_GET['clid'];
$step=0;
if (isset($_GET['s'])) $step=$_GET['s'];
$error="";
if (isset($_POST['delete_rec']) && $pid) {
	$dh->Exec("delete from general where id='$pid'");
	header("location: general.php?idx=$cid$return");
	exit;
}
$error="";
if ($step==1) {
	$season="";
	if (isset($_POST['season'])) $season=$_POST['season'];
	$salesemail="";
	if (isset($_POST['salesemail'])) $salesemail=$_POST['salesemail'];
	$hasstore="";
	if (isset($_POST['hasstore'])) $hasstore=$_POST['hasstore'];
	$season=htmlentities(addslashes($season),ENT_QUOTES);
	$salesemail=htmlentities(addslashes($salesemail),ENT_QUOTES);
	$hasstore=htmlentities(addslashes($hasstore),ENT_QUOTES);
	if (strlen($season)==0) {
		$error.=" 'Current Season' is a required entry.";
	}
	if (strlen($salesemail)==0) {
		$error.=" 'Email Address to send orders to' is a required entry.";
	}
	if (strlen($salesemail) && validemail($salesemail)==0) $error.=" Field salesemail does not contain a valid email address.";
	if (strlen($error)==0) {
		if ($pid==0) {
			$dh->Exec("insert into general (season,salesemail,hasstore) values ('$season','$salesemail','$hasstore')");
			$pid=$dh->LastID();
		} else {
			$extra="";
			$dh->Exec("update general set season='$season',salesemail='$salesemail',hasstore='$hasstore'$extra where id='$pid'");
		}
		header("location: general.php?idx=$cid$return");
		exit;
	}
}
$action="Add New";
if ($pid) $action="Edit";
if (strlen($error)==0) {
	if ($pid==0 && $clid==0) {
		$season="";
		$salesemail="";
		$hasstore="Y";
	} else {
		$wid=$pid;
		if ($wid==0) $wid=$clid;
		$dh->Query("select * from general where id='$wid'");
		$r=$dh->FetchArray();
		$season=stripslashes($r['season']);
		$salesemail=stripslashes($r['salesemail']);
		$hasstore=stripslashes($r['hasstore']);
	}
} else {
	$error="<tr><td colspan=2><table width=100% border=1 cellpadding=3 callspacing=0><tr><td align=center><p><font color=red>$error</font></p></td></tr></table></td></tr>";
}
putMaintHeader($hdr);
$chkhasstore="";
if ($hasstore=="Y") $chkhasstore=" checked";
$cw=explode(",","35%,65%");
print <<< END
<form name=mpform action=editgeneral.php?idx=$cid&s=1&id=$pid$return method=post enctype="multipart/form-data"><center><table border=0 cellpadding=8 cellspacing=0 width=740>
<tr><td width=90%><h1>$action General Site Paramaters</h1></td>
<td width=10% align=right valign=top>
<a href=general.php?idx=$cid$return><img src=images/uplevel.gif border=0 onMouseover="showtip(this,event,'Up one level');" onMouseOut=hidetip();></a>
</td>
</tr>
$error
<tr><td colspan=2><table width=100% border=1 cellpadding=3 cellspacing=0>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Current Season</p></td><td width=$cw[1] valign=top><input type=text name=season value="$season" size=4></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Email Address to send orders to</p></td><td width=$cw[1] valign=top><input type=text name=salesemail value="$salesemail" size=30></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Has Store?</p></td><td width=$cw[1] valign=top><input type=checkbox name=hasstore$chkhasstore value="Y"></td></tr>
END;
$action="Add New Record";
if ($pid) {
	$action="Save Changes";
	print "<tr><td colspan=2 valign=top align=right><input type=checkbox name=delete_rec value=\"Y\">&nbsp; Delete this record?</td></tr>";
}
print <<< END
<tr><td colspan=2 align=center><input type=submit value="$action">&nbsp;&nbsp;&nbsp;<input type=button value="Cancel" onclick=self.location="general.php?idx=$cid$return"></td></tr>
</table></td></tr>
</table></center></form>
END;
putMaintFooter();
?>

