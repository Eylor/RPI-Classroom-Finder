<?php
/*
	Back End Screen for Table cred
	Copyright 2009 by Michael Lewis.
	All Rights Reserved.

Edit history:
	Created 08/02/2009 12:03:12
*/
include("../session.php");
include("../common.php");
$utype=$_SESSION['type'];
$hdr['title']="Maintain SMS Authorized Senders";
$hdr['focus']="name";
$FOCUSFIELD="name";
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
if ($_SESSION['cp']!="smscreds.php") {
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
	$dh->Exec("delete from cred where id='$pid'");
	header("location: smscreds.php?idx=$cid$return");
	exit;
}
$error="";
if ($step==1) {
	$name="";
	if (isset($_POST['name'])) $name=$_POST['name'];
	$username="";
	if (isset($_POST['username'])) $username=$_POST['username'];
	$password="";
	if (isset($_POST['password'])) $password=$_POST['password'];
	$notes="";
	if (isset($_POST['notes'])) $notes=$_POST['notes'];
	$name=htmlentities(addslashes($name),ENT_QUOTES);
	$username=htmlentities(addslashes($username),ENT_QUOTES);
	$password=htmlentities(addslashes($password),ENT_QUOTES);
	$notes=htmlentities(addslashes($notes),ENT_QUOTES);
	if (strlen($name)==0) {
		$error.=" 'Real Name' is a required entry.";
	}
	if (strlen($username)==0) {
		$error.=" 'User Name' is a required entry.";
	}
	if (strlen($password)==0) {
		$error.=" 'Password' is a required entry.";
	}
	if (strlen($error)==0) {
		if ($pid==0) {
			$dh->Exec("insert into cred (name,username,password,notes) values ('$name','$username','$password','$notes')");
			$pid=$dh->LastID();
		} else {
			$extra="";
			$dh->Exec("update cred set name='$name',username='$username',password='$password',notes='$notes'$extra where id='$pid'");
		}
		header("location: smscreds.php?idx=$cid$return");
		exit;
	}
}
$action="Add New";
if ($pid) $action="Edit";
if (strlen($error)==0) {
	if ($pid==0 && $clid==0) {
		$name="";
		$username="";
		$password="";
		$notes="";
	} else {
		$wid=$pid;
		if ($wid==0) $wid=$clid;
		$dh->Query("select * from cred where id='$wid'");
		$r=$dh->FetchArray();
		$name=stripslashes($r['name']);
		$username=stripslashes($r['username']);
		$password=stripslashes($r['password']);
		$notes=stripslashes($r['notes']);
	}
} else {
	$error="<tr><td colspan=2><table width=100% border=1 cellpadding=3 callspacing=0><tr><td align=center><p><font color=red>$error</font></p></td></tr></table></td></tr>";
}
putMaintHeader($hdr);
$cw=explode(",","30%,70%");
print <<< END
</td></tr></table>
<form name=mpform action=editsmscreds.php?idx=$cid&s=1&id=$pid$return method=post enctype="multipart/form-data"><center><table border=0 cellpadding=8 cellspacing=0 width=1000>
<tr><td width=90%><h1>$action SMS Authorized Senders</h1></td>
<td width=10% align=right valign=top>
<a href=smscreds.php?idx=$cid$return><img src=/images/uplevel.gif border=0 title="Up one level" alt="Up one level"></a>
</td>
</tr>
$error
<tr><td colspan=2><table width=100% border=1 cellpadding=3 cellspacing=0>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Real Name</p></td><td width=$cw[1] valign=top><input type=text name=name value="$name" size=32></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>User Name</p></td><td width=$cw[1] valign=top><input type=text name=username value="$username" size=30></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Password</p></td><td width=$cw[1] valign=top><input type=text name=password value="$password" size=30></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Notes</p></td><td width=$cw[1] valign=top><textarea name=notes rows=12 cols=60>$notes</textarea></td></tr>
END;

$action="Add New Record";
if ($pid) {
	$action="Save Changes";
	print "<tr><td colspan=2 valign=top align=right><input type=checkbox name=delete_rec value=\"Y\">&nbsp; Delete this record?</td></tr>";
}
print <<< END
<tr><td colspan=2 align=center><input type=submit value="$action">&nbsp;&nbsp;&nbsp;<input type=button value="Cancel" onclick=self.location="smscreds.php?idx=$cid$return"></td></tr>
</table></td></tr>
</table></center></form>
END;
include("../footer.php");
?>

