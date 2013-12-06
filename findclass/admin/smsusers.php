<?php
/*
	Front End Screen for Table user
	Copyright 2009 by Michael Lewis.
	All Rights Reserved.

Edit history:
	Created 08/01/2009 16:28:03
*/
include("../session.php");
include("../common.php");
$utype=$_SESSION['type'];
$hdr['title']="Maintain SMS Users";
if (strpos(" hc;ac;am",";")) {
	$usa=explode(";","hc;ac;am");
} else {
	$usa[0]="hc;ac;am";
}
if (!in_array($utype,$usa)) {
	header('location: index.php');
	exit;
}
putMaintHeader($hdr);
$dh=new DB("sms");
$dh1=new DB("sms");
$cid="";
$ret="index.php";
if (isset($_GET['act']) && $_GET['act']==3) {
	$id=$_GET['id'];
	$dh->Exec("delete from user where id='$id'");
}
if (isset($_GET['idx'])) $cid=$_GET['idx'];
print <<< END
</td></tr></table>
<center><table border="0" cellpadding="8" cellspacing="0" width=1000>
<tr><td width=90%><h1>Maintain SMS Users</h1></td>
<td width=10% align=right valign=top>
<a href=editsmsusers.php?idx=$cid&id=><img src=/images/adddoc.gif border=0 onMouseover="showtip(this,event,'Add a New SMS User');" onMouseOut=hidetip();></a>&nbsp;<a href=$ret><img src=/images/uplevel.gif border=0 title="Up one level" alt="Up one level"></a>
</td>
</tr>
<tr><td align=center colspan=2><table width=100% border=0 cellpadding=3 cellspacing=0>
END;
$_SESSION['cp']="smsusers.php";
$cw=explode(',','30%,30%,20%');
if (isset($_GET['sort']) && strlen($_GET['sort'])) {
	$sql="select * from user order by ".$_GET['sort'];
	if ($_GET['d']=="d") $sql.=" desc";
} else {
	$sql="select * from user  order by owner,lastname,firstname";
}
$dh->Query($sql);
$j=$dh->NumRows();
$row=0;
print <<< END
<tr><td width=30% bgcolor="#20e0f0" align=left valign=top><p><a href=smsusers.php?sort=lastname&d=d><img src=/images/button_triangle_black_down.gif border=0></a>&nbsp;<b>Last Name&nbsp;<a href=smsusers.php?sort=lastname&d=u><img src=/images/button_triangle_black_up.gif border=0></a></b></p></td>
<td width=30% bgcolor="#20e0f0" align=left valign=top><p><a href=smsusers.php?sort=firstname&d=d><img src=/images/button_triangle_black_down.gif border=0></a>&nbsp;<b>Firstname&nbsp;<a href=smsusers.php?sort=firstname&d=u><img src=/images/button_triangle_black_up.gif border=0></a></b></p></td>
<td width=20% bgcolor="#20e0f0" align=left valign=top><p><a href=smsusers.php?sort=phone&d=d><img src=/images/button_triangle_black_down.gif border=0></a>&nbsp;<b>Cell Number&nbsp;<a href=smsusers.php?sort=phone&d=u><img src=/images/button_triangle_black_up.gif border=0></a></b></p></td>
<td bgcolor="#20e0f0" align=center><p><b>Action</b></p></td></tr>
END;
for ($i=0; $i<$j; $i++) {
	$r=$dh->FetchArray();
	$id=$r['id'];
	print "<tr>";
	$b="";
	if ($row & 1) $b=' bgcolor=#f5ede2';
	$row++;
	print "<td width=$cw[0] align=left valign=top$b><p>".$r['lastname']."</p></td>";
	print "<td width=$cw[1] align=left valign=top$b><p>".$r['firstname']."</p></td>";
	print "<td width=$cw[2] align=left valign=top$b><p>".$r['phone']."</p></td>";
	$icons="";
	print "<td width=70 align=right valign=top$b>$icons<a href=editsmsusers.php?idx=$cid&id=$id><img src=/images/edit.gif border=0 onMouseover=\"showtip(this,event,'Edit this SMS User');\" onMouseOut=hidetip();></a>";
	print "</td>";
	print "</tr>";
}
print <<< END
</table></td></tr>
<tr><td width=100% colspan=2><hr><p align=center><img src=/images/uplevel.gif>&nbsp;<a href=$ret>Up One Level</a></p></td></tr>
</table></center>
END;
include("../footer.php");
?>
