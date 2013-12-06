<?php
/*
	Back End Screen for Table groups
	Copyright 2009 by Michael Lewis.
	All Rights Reserved.

Edit history:
	Created 08/02/2009 14:59:39
*/
include("../session.php");
include("../common.php");
$utype=$_SESSION['type'];
$hdr['title']="Maintain SMS Groups";
$hdr['html']=<<< END
<script>
function doBack(e,fn) {
        f=document.getElementById(fn);
        if (e.checked) {
                f.style.backgroundColor="yellow";
        } else {
                f.style.backgroundColor="";
        }
}
</script>
END;
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
if ($_SESSION['cp']!="smsgroups.php") {
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
	$dh->Exec("delete from groups where id='$pid'");
	header("location: smsgroups.php?idx=$cid$return");
	exit;
}
$error="";
if ($step==1) {
	if (isset($_POST['owner'])) $owner=$_POST['owner'];
	$name="";
	if (isset($_POST['name'])) $name=$_POST['name'];
	$owner=$_SESSION['valid'];
	$name=htmlentities(addslashes($name),ENT_QUOTES);
	if (strlen($name)==0) {
		$error.=" 'Group Name' is a required entry.";
	}
	if (strlen($error)==0) {
		if ($pid==0) {
			$dh->Exec("insert into groups (owner,name) values ('$owner','$name')");
			$pid=$dh->LastID();
		} else {
			$extra="";
			$dh->Exec("update groups set owner='$owner',name='$name'$extra where id='$pid'");
		}
		saveUserField($pid);
		header("location: smsgroups.php?idx=$cid$return");
		exit;
	}
}
$action="Add New";
if ($pid) $action="Edit";
if (strlen($error)==0) {
	if ($pid==0 && $clid==0) {
		$owner="";
		$name="";
		$_SESSION['ua_User']=array();
	} else {
		$wid=$pid;
		if ($wid==0) $wid=$clid;
		$dh->Query("select * from groups where id='$wid'");
		$r=$dh->FetchArray();
		$owner=stripslashes($r['owner']);
		$name=stripslashes($r['name']);
		$ua_User=array();
		$dh->Query("select user_id from sms.grp2user where grp_id='$pid'");
		$nr=$dh->NumRows();
		for ($i=0;$i<$nr;$i++) {
			list($gid)=$dh->FetchRow();
			$ua_User []=$gid;
		}
		$_SESSION['ua_User']=$ua_User;
	}
} else {
	$error="<tr><td colspan=2><table width=100% border=1 cellpadding=3 callspacing=0><tr><td align=center><p><font color=red>$error</font></p></td></tr></table></td></tr>";
}
putMaintHeader($hdr);
$cw=explode(",","30%,70%");
print <<< END
</td></tr></table>
<form name=mpform action=editsmsgroups.php?idx=$cid&s=1&id=$pid$return method=post enctype="multipart/form-data"><center><table border=0 cellpadding=8 cellspacing=0 width=1000>
<tr><td width=90%><h1>$action SMS Groups</h1></td>
<td width=10% align=right valign=top>
<a href=smsgroups.php?idx=$cid$return><img src=/images/uplevel.gif border=0 title="Up one level" alt="Up one level"></a>
</td>
</tr>
$error
<tr><td colspan=2><table width=100% border=1 cellpadding=3 cellspacing=0>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Group Name</p></td><td width=$cw[1] valign=top><input type=text name=name value="$name" size=32></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Users in Group</p></td><td width=$cw[1] valign=top>
END;
print getUserField();
print <<< END
</td></tr>
END;

$action="Add New Record";
if ($pid) {
	$action="Save Changes";
	print "<tr><td colspan=2 valign=top align=right><input type=checkbox name=delete_rec value=\"Y\">&nbsp; Delete this record?</td></tr>";
}
print <<< END
<tr><td colspan=2 align=center><input type=submit value="$action">&nbsp;&nbsp;&nbsp;<input type=button value="Cancel" onclick=self.location="smsgroups.php?idx=$cid$return"></td></tr>
</table></td></tr>
</table></center></form>
END;
include("../footer.php");
//
//	Get Users Field
//
function getUserField() {
	global $dh;
	
	$sid=$_SESSION['valid'];
	$ua=$_SESSION['ua_User'];
	$dh->Query("select * from sms.user where owner='$sid' order by lastname");
	$nr=$dh->NumRows();
	$k=0;
	$npr=4;
	$wid=floor(100/$npr);
	$t="<table width=100% cellpadding=2 cellspacing=0 border=0>";
	for ($i=0;$i<$nr;$i++) {
		$r=$dh->FetchArray();
		$id=$r['id'];
		$dsp="".stripslashes($r['lastname']).", ".stripslashes($r['firstname'])."";
		if ($k==0) $t.="<tr>";
		$t.="<td width=$wid% valign=top><span id=fn_$i><input name=chk_user_$id type=checkbox onclick=\"doBack(this,'fn_$i');\" value=\"$id\"";
		if (in_array($id,$ua)) {
			$t.=" checked><span style=\"background-color:yellow\">$dsp</span>";
		} else {
			$t.=">$dsp";
		}
		$t.="</span></td>";
		$k++;
		if ($k==$npr) {
			$k=0;
			$t.="</tr>";
		}
	}
	if ($k) {
		for(;$k<$npr;$k++) {
			$t.="<td>&nbsp;</td>";
		}
		$t.="</tr>";
	}
	$t.="</table>";
	return $t;
}
//
//	Save Users
//
function saveUserField($gid) {
	global $dh;

	$sid=$_SESSION['valid'];
	$dh->Exec("delete from sms.grp2user where grp_id='$gid'");
	foreach($_POST as $k=>$v) {
		if (substr($k,0,9)!="chk_user_") continue;
		$dh->Exec("insert into sms.grp2user (grp_id,user_id,owner) values('$gid','$v','$sid')");
	}
}

