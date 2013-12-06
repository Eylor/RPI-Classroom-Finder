\<?php
/*
	Back End Screen for Table usertypes
	Copyright 2005 by Michael Lewis.
	All Rights Reserved.

Edit history:
	Created 07/19/2005 12:22:55
*/
include("../session.php");
include("../common.php");
$utype=$_SESSION['usertype'];
$hdr['title']="Maintain User Types";
$hdr['focus']="name";
$hdr['backimage']="fade.gif";
if (strpos(" am",";")) {
	$usa=explode(";","am");
} else {
	$usa[0]="am";
}
if (!in_array($utype,$usa)) {
	header('location: index.php');
	exit;
}
$dh=new DB();
$cid="";
$pid=0;
if ($_SESSION['cp']!="usertypes.php") {
	header("location: index.php");
	exit;
}
if (isset($_GET['id'])) $pid=$_GET['id'];
if (isset($_GET['idx'])) $cid=$_GET['idx'];
$step=0;
if (isset($_GET['s'])) $step=$_GET['s'];
$error="";
if (isset($_POST['delete_rec']) && $pid) {
	$dh->Exec("delete from usertypes where id='$pid'");
	header("location: usertypes.php?idx=$cid");
	exit;
}
$error="";
if ($step==1) {
	$name="";
	if (isset($_POST['name'])) $name=$_POST['name'];
	$typecode="";
	if (isset($_POST['typecode'])) $typecode=$_POST['typecode'];
	$name=htmlentities(addslashes($name),ENT_QUOTES);
	$typecode=htmlentities(addslashes($typecode),ENT_QUOTES);
	if (strlen($name)==0) {
		$error=" 'User Type Name' is a required entry.";
	}
	if (strlen($typecode)==0) {
		$error=" 'Two Character Code' is a required entry.";
	} else {
		if (strlen($typecode)<2) $error=" 'Two Character Code' must be at least 2 characters in length.";
	}
	if (strlen($error)==0) {
		if ($pid==0) {
			$dh->Exec("insert into usertypes (name,typecode) values ('$name','$typecode')");
			$pid=$dh->LastID();
		} else {
			$extra="";
			$dh->Exec("update usertypes set name='$name',typecode='$typecode'$extra where id='$pid'");
		}
		header("location: usertypes.php?idx=$cid");
		exit;
	}
}
$action="Add New";
if ($pid) $action="Edit";
if (strlen($error)==0) {
	if ($pid==0) {
		$name="";
		$typecode="";
	} else {
		$dh->Query("select * from usertypes where id='$pid'");
		$r=$dh->FetchArray();
		$name=stripslashes($r['name']);
		$typecode=stripslashes($r['typecode']);
	}
} else {
	$error="<tr><td colspan=2><table width=100% border=1 cellpadding=3 callspacing=0><tr><td align=center><p><font color=red>$error</font></p></td></tr></table></td></tr>";
}
putMaintHeader($hdr);
$cw=explode(",","35%,65%");
print <<< END
<form name=mpform action=editusertypes.php?idx=$cid&s=1&id=$pid method=post enctype="multipart/form-data"><center><table border=0 cellpadding=8 cellspacing=0 width=650>
<tr><td width=90%><h1>$action User Types</h1></td>
<td width=10% align=right valign=top>
<a href=usertypes.php?idx=$cid><img src=images/uplevel.gif border=0 onMouseover="showtip(this,event,'Up one level');" onMouseOut=hidetip();></a>
</td>
</tr>
$error
<tr><td colspan=2><table width=100% border=1 cellpadding=3 cellspacing=0>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>User Type Name</p></td><td width=$cw[1] valign=top><input type=text name=name value="$name" size=60></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Two Character Code</p></td><td width=$cw[1] valign=top><input type=text name=typecode value="$typecode" size=4></td></tr>
END;
$action="Add New Record";
if ($pid) {
	$action="Save Changes";
	print "<tr><td colspan=2 valign=top align=right><input type=checkbox name=delete_rec value=\"Y\">&nbsp; Delete this record?</td></tr>";
}
print <<< END
<tr><td colspan=2 align=center><input type=submit value="$action">&nbsp;&nbsp;&nbsp;<input type=button value="Cancel" onclick=self.location="usertypes.php?idx=$cid"></td></tr>
</table></td></tr>
<tr><td width=100%<hr><p align=center><img src=images/uplevel.gif>&nbsp;<a href=usertypes.php?idx=$cid>Up One Level</a></p></td></tr>
</table></center></form>
END;
putMaintFooter();
?>

