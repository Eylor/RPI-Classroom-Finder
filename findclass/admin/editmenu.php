<?php
/*
	Back End Screen for Table menu
	Copyright 2005 by Michael Lewis.
	All Rights Reserved.

Edit history:
	Created 07/21/2005 11:33:31
*/
include("../session.php");
include("../common.php");
$utype=$_SESSION['usertype'];
$hdr['title']="Maintain Menu Items";
$hdr['focus']="title";
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
if ($_SESSION['cp']!="menu.php") {
	header("location: index.php");
	exit;
}
if (isset($_GET['id'])) $pid=$_GET['id'];
if (isset($_GET['idx'])) $cid=$_GET['idx'];
$step=0;
if (isset($_GET['s'])) $step=$_GET['s'];
$error="";
if (isset($_POST['delete_rec']) && $pid) {
	$dh->Exec("delete from menu where id='$pid'");
	header("location: menu.php?idx=$cid");
	exit;
}
$error="";
if ($step==1) {
	$title="";
	if (isset($_POST['title'])) $title=$_POST['title'];
	$category="";
	if (isset($_POST['category'])) $category=$_POST['category'];
	$execute="";
	if (isset($_POST['execute'])) $execute=$_POST['execute'];
	$access="";
	if (isset($_POST['access'])) {
		if (count($_POST['access'])<2) {
			$access=$_POST['access'][0];
		} else {
			$access=join(';',$_POST['access']);
		}
	}
	$title=htmlentities(addslashes($title),ENT_QUOTES);
	$category=htmlentities(addslashes($category),ENT_QUOTES);
	$execute=htmlentities(addslashes($execute),ENT_QUOTES);
	$access=htmlentities(addslashes($access),ENT_QUOTES);
	if (strlen($title)==0) {
		$error=" 'Title' is a required entry.";
	}
	if (strlen($category)==0) {
		$error=" 'Category' is a required entry.";
	}
	if (strlen($execute)==0) {
		$error=" 'Program to Run' is a required entry.";
	} else {
		if (strlen($execute)<5) $error=" 'Program to Run' must be at least 5 characters in length.";
	}
	if (strlen($access)==0) {
		$error=" 'User Type' is a required entry.";
	}
	if (strlen($error)==0) {
		if ($pid==0) {
			$dh->Exec("insert into menu (title,category,execute,access) values ('$title','$category','$execute','$access')");
			$pid=$dh->LastID();
		} else {
			$extra="";
			$dh->Exec("update menu set title='$title',category='$category',execute='$execute',access='$access'$extra where id='$pid'");
		}
		header("location: menu.php?idx=$cid");
		exit;
	}
}
$action="Add New";
if ($pid) $action="Edit";
if (strlen($error)==0) {
	if ($pid==0) {
		$title="";
		$category="Site Administration";
		$execute="";
		$access="";
	} else {
		$dh->Query("select * from menu where id='$pid'");
		$r=$dh->FetchArray();
		$title=stripslashes($r['title']);
		$category=stripslashes($r['category']);
		$execute=stripslashes($r['execute']);
		$access=stripslashes($r['access']);
	}
} else {
	$error="<tr><td colspan=2><table width=100% border=1 cellpadding=3 callspacing=0><tr><td align=center><p><font color=red>$error</font></p></td></tr></table></td></tr>";
}
putMaintHeader($hdr);
$cw=explode(",","35%,65%");
print <<< END
<form name=mpform action=editmenu.php?idx=$cid&s=1&id=$pid method=post enctype="multipart/form-data"><center><table border=0 cellpadding=8 cellspacing=0 width=740>
<tr><td width=90%><h1>$action Menu Items</h1></td>
<td width=10% align=right valign=top>
<a href=menu.php?idx=$cid><img src=images/uplevel.gif border=0 onMouseover="showtip(this,event,'Up one level');" onMouseOut=hidetip();></a>
</td>
</tr>
$error
<tr><td colspan=2><table width=100% border=1 cellpadding=3 cellspacing=0>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Title</p></td><td width=$cw[1] valign=top><input type=text name=title value="$title" size=60></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Category</p></td><td width=$cw[1] valign=top><select name=category size=4>
END;
$opts="Maintenence:Maintenence;Reports:Reports;Site Administration:Site Administration;Miscellaneous:Miscellaneous";
if (isset($o)) unset($o);
if (strpos($opts,";")==0) {
	$o[0]=$opts;
} else {
	$o=explode(";",$opts);
}
if (isset($ta)) unset($ta);
if (strpos($category,";")) {
	$ta=explode(";",$category);
} else {
	$ta[0]=$category;
}
for ($i=0,$j=count($o);$i<$j;$i++) {
	print "<option value=\"";
	if (strpos($o[$i],":")==0) {
		$v=$i+1;
		$txt=$o[$i];
	} else {
		list($txt,$v)=explode(":",$o[$i]);
	}
	print "$v\"";
	if (in_array($v,$ta)) print " selected";
	print ">$txt</option>\n";
}
print <<< END
</select>
</td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Program to Run</p></td><td width=$cw[1] valign=top><input type=text name=execute value="$execute" size=60></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>User Type</p></td><td width=$cw[1] valign=top>
END;
$dh->Query("select * from usertypes order by name");
$nr=$dh->NumRows();
$size=6;
if ($nr<6) $size=$nr;
if ($size==0) $size=1;
print "<select name=access"."[] multiple size=$size>";
if (isset($v)) unset($v);
if (isset($ta)) unset($ta);
if (strpos($access,";")) {
	$ta=explode(";",$access);
} else {
	$ta[0]=$access;
}
for ($i=0;$i<$nr;$i++) {
	$r=$dh->FetchArray();
	print "<option value=\"".$r['typecode'].'"'; 
	if (in_array($r['typecode'],$ta)) print " selected";
	print ">".$r['name']."</option>\n";
}
print <<< END
</select>
</td></tr>
END;
$action="Add New Record";
if ($pid) {
	$action="Save Changes";
	print "<tr><td colspan=2 valign=top align=right><input type=checkbox name=delete_rec value=\"Y\">&nbsp; Delete this record?</td></tr>";
}
print <<< END
<tr><td colspan=2 align=center><input type=submit value="$action">&nbsp;&nbsp;&nbsp;<input type=button value="Cancel" onclick=self.location="menu.php?idx=$cid"></td></tr>
</table></td></tr>
</table></center></form>
END;
putMaintFooter();
?>

