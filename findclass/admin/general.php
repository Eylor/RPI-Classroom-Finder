<?php
/*
	Front End Screen for Table general
	Copyright 2006 by Michael Lewis.
	All Rights Reserved.

Edit history:
	Created 06/08/2006 08:53:19
*/
include("../session.php");
include("../common.php");
$utype=$_SESSION['type'];
$hdr['title']="Maintain General Site Information";
if (strpos(" am;",";")) {
	$usa=explode(";","am;");
} else {
	$usa[0]="am;";
}
if (!in_array($utype,$usa)) {
	header('location: index.php');
	exit;
}
putMaintHeader($hdr);
$dh=new DB();
$dh1=new DB();
if (isset($_GET['act']) && $_GET['act']==3) {
	$id=$_GET['id'];
	$dh->Exec("delete from general where id='$id'");
}
$cid="";
$ret="index.php";
if (isset($_GET['idx'])) $cid=$_GET['idx'];
print <<< END
<center><table border="0" cellpadding="8" cellspacing="0" width=740>
<tr><td width=90%><h1>Maintain General Site Paramaters</h1></td>
<td width=10% align=right valign=top>
<a href=editgeneral.php?idx=$cid&id=><img src=images/adddir.gif border=0 onMouseover="showtip(this,event,'Add a New General Site Paramater');" onMouseOut=hidetip();></a>&nbsp;<a href=$ret><img src=images/uplevel.gif border=0 onMouseover="showtip(this,event,'Up one level');" onMouseOut=hidetip();></a>
</td>
</tr>
<tr><td><table width=100% border=0 cellpadding=0 cellspacing=0>
END;
$_SESSION['cp']="general.php";
$cw=explode(',','50%,50%');
$dh->Query("select * from general");
$j=$dh->NumRows();
$half=floor($j/2);
print "<tr><td width=50% valign=top><table width=100% cellpadding=4 cellspacing=0>";
$row=0;
for ($i=0; $i<$j; $i++) {
	if ($i==$half) {
		print "</table></td><td width=50% valign=top><table width=100% cellpadding=4 cellspacing=0>";
		$row=0;
	}
	$r=$dh->FetchArray();
	$id=$r['id'];
	print "<tr>";
	$b="";
	print "<td width=$cw[0] align=left valign=top$b><p>".$r['season']."</p></td>";
	print "<td width=$cw[1] align=left valign=top$b><p>".$r['salesemail']."</p></td>";
	$icons="";
	print "<td width=70 align=right valign=top$b>$icons<a href=editgeneral.php?idx=$cid&id=$id><img src=images/edit.gif border=0 onMouseover=\"showtip(this,event,'Edit this General Site Paramater');\" onMouseOut=hidetip();></a>";
	print "</td>";
	print "</tr>";
}
print <<< END
</td></tr></table>
</table></td></tr>
<tr><td width=100%<hr><p align=center><img src=images/uplevel.gif>&nbsp;<a href=$ret>Up One Level</a></p></td></tr>
</table></center>
END;
putMaintFooter();
?>
