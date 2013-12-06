<?php
/*
	Back End Screen for Table user
	Copyright 2009 by Michael Lewis.
	All Rights Reserved.

Edit history:
	Created 08/02/2009 11:30:28
*/
include("../session.php");
include("../common.php");
$utype=$_SESSION['type'];
$hdr['title']="Maintain SMS Users";
$hdr['focus']="firstname";
$FOCUSFIELD="firstname";
$hdr['backimage']="fade.gif";
if (strpos(" hc;ac;am",";")) {
	$usa=explode(";","hc;ac;am");
} else {
	$usa[0]="hc;ac;am";
}
if (!in_array($utype,$usa)) {
	header('location: index.php');
	exit;
}
$dh=new DB("sms");
$cid="";
$pid=0;
$clid=0;
if ($_SESSION['cp']!="smsusers.php") {
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
	$dh->Exec("delete from user where id='$pid'");
	$dh->Exec("delete from grp2user where user_id='$pid'");
	header("location: smsusers.php?idx=$cid$return");
	exit;
}
$error="";
if ($step==1) {
	if (isset($_POST['owner'])) $owner=$_POST['owner'];
	$firstname="";
	if (isset($_POST['firstname'])) $firstname=$_POST['firstname'];
	$lastname="";
	if (isset($_POST['lastname'])) $lastname=$_POST['lastname'];
	$phone="";
	if (isset($_POST['phone'])) $phone=$_POST['phone'];
	$owner=$_SESSION['valid'];
	$firstname=addslashes($firstname);
	$lastname=addslashes($lastname);
	$normphone=trim(str_replace(array(")","(","+","-","."," ","'",'"'),"",$phone));
	if (strlen($firstname)==0) {
		$error.=" 'First Name' is a required entry.";
	}
	if (strlen($lastname)==0) {
		$error.=" 'Last Name' is a required entry.";
	}
	if (strlen($phone)==0) {
		$error.=" 'Cell Number' is a required entry.";
	}
	if (strlen($error)==0) {
		if ($pid==0) {
			$dh->Exec("insert into user (owner,firstname,lastname,phone,norm_phone) values ('$owner','$firstname','$lastname','$phone','$normphone')");
			$pid=$dh->LastID();
		} else {
			$extra="";
			$dh->Exec("update user set owner='$owner',firstname='$firstname',lastname='$lastname',phone='$phone',norm_phone='$normphone'$extra where id='$pid'");
		}
		header("location: smsusers.php?idx=$cid$return");
		exit;
	}
}
$action="Add New";
if ($pid) $action="Edit";
if (strlen($error)==0) {
	if ($pid==0 && $clid==0) {
		$owner="";
		$firstname="";
		$lastname="";
		$phone="";
	} else {
		$wid=$pid;
		if ($wid==0) $wid=$clid;
		$dh->Query("select * from user where id='$wid'");
		$r=$dh->FetchArray();
		$owner=stripslashes($r['owner']);
		$firstname=stripslashes($r['firstname']);
		$lastname=stripslashes($r['lastname']);
		$phone=stripslashes($r['phone']);
	}
} else {
	$error="<tr><td colspan=2><table width=100% border=1 cellpadding=3 callspacing=0><tr><td align=center><p><font color=red>$error</font></p></td></tr></table></td></tr>";
}
putMaintHeader($hdr);
$cw=explode(",","30%,70%");
print <<< END
</td></tr></table>
<form name=mpform action=editsmsusers.php?idx=$cid&s=1&id=$pid$return method=post enctype="multipart/form-data"><center><table border=0 cellpadding=8 cellspacing=0 width=1000>
<tr><td width=90%><h1>$action SMS Users</h1></td>
<td width=10% align=right valign=top>
<a href=smsusers.php?idx=$cid$return><img src=/images/uplevel.gif border=0 title="Up one level" alt="Up one level"></a>
</td>
</tr>
$error
<tr><td colspan=2><table width=100% border=1 cellpadding=3 cellspacing=0>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>First Name</p></td><td width=$cw[1] valign=top><input type=text name=firstname value="$firstname" size=30></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Last Name</p></td><td width=$cw[1] valign=top><input type=text name=lastname value="$lastname" size=30></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Cell Number</p></td><td width=$cw[1] valign=top><input type=text name=phone value="$phone" size=20></td></tr>
END;

$action="Add New Record";
if ($pid) {
	$action="Save Changes";
	print "<tr><td colspan=2 valign=top align=right><input type=checkbox name=delete_rec value=\"Y\">&nbsp; Delete this record?</td></tr>";
}
print <<< END
<tr><td colspan=2 align=center><input type=submit value="$action">&nbsp;&nbsp;&nbsp;<input type=button value="Cancel" onclick=self.location="smsusers.php?idx=$cid$return"></td></tr>
</table></td></tr>
</table></center></form>
END;
include("../footer.php");
?>

