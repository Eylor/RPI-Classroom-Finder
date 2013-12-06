<?php
//
//	Main start page for Administration
//	Copyright 2005 by Michael Lewis
//	All rights reserved
//
$ret="";
if (isset($_GET['idx'])) {
	session_id($_GET['idx']);
	include("../session.php");
	$ret=$_GET['ret'];
	$_SESSION['ret']=$ret;
	$from=0;
} else {
	include("../session.php");
}
$_SESSION['cp']="index.php";
include("../common.php");
isLoggedIn("am;de");
$dh=new DB();
$ut=$_SESSION['usertype'];
$dh->Query("select * from menu where access like '%$ut%' order by category,title");
$j=$dh->NumRows();
if ($j==0) {
	print "No menu items defined";
	exit;
}
for ($i=0;$i<$j;$i++) {
	$r=$dh->FetchArray();
	$title[$i]=$r['title'];
	$category[$i]=$r['category'];
	$execute[$i]=$r['execute'];
	$access[$i]=$r['access'];
}
$hdr['title']="$sitename Administration";
putMaintHeader($hdr);
print <<< END
<center><table border="0" cellpadding="8" cellspacing="0" width="740">
<tr><td width=40% colspan=2><h1>$sitename Site Administration</h1><br></td></tr>
END;
$lc="";
$cols=2;
$k=0;
for ($i=0;$i<$j;$i++) {
	if ($lc!=$category[$i]) {
		if ($i!=0) {
			print "</td>";
		}
		if ($k==$cols) {
			print "</tr><tr><td width=20% colspan=$cols align=center><hr></td></tr>";
			$k=1;
		} else {
			$k++;
		}
		if ($k==1) {
			print "<tr>";
		}
		$lc=$category[$i];
		print "<td width=50% valign=top align=left><h3><img src=images/button_triangle_black_rt.gif>&nbsp;$lc</h3>";
	}
	$ex=$execute[$i];
	$tit=$title[$i];
	print "<p><img src=\"images/n.gif\">&nbsp;<a href=\"$ex\">$tit</p></a>";
}
if ($k>0) {
	while ($k<$cols) {
		print "<td width=50%>&nbsp;</td>";
		$k++;
	}
	print "</tr>";
}
$leave=<<< END
<img src=images/uplevel.gif border=0 onclick=self.location="/#general">&nbsp;<a href="/index.php#general">Return to Main Site</a>
END;
print <<< END
<tr><td width=30% colspan=$cols><hr><p align=center>$leave</p></td></tr>
</table></center>
END;
putMaintFooter();
?>
