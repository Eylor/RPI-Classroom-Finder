<?php
//
//	Bad login report
//
include("../session.php");
include("../common.php");
isLoggedIn("am");
$title="Bad Login Report";
$hdr['title']=$title;
$step=0;
$dh=new DB();
//
//	Beginning of program
//
if (isset($_GET['s'])) { $step=$_GET['s']; }
if ($step==1) {
	$year1=$year2=date('Y');
        $month1=$month2=date('m');
        $day1=$day2=date('d');
        if (isset($_POST['date1_year'])) { $year1 = $_POST['date1_year']; }
        if (isset($_POST['date1_mon'])) { $month1 = $_POST['date1_mon']; }
        if (isset($_POST['date1_day'])) { $day1 = $_POST['date1_day']; }
        if (isset($_POST['date2_year'])) { $year2 = $_POST['date2_year']; }
        if (isset($_POST['date2_mon'])) { $month2 = $_POST['date2_mon']; }
        if (isset($_POST['date2_day'])) { $day2 = $_POST['date2_day']; }
        $date1 = $year1 . '-' . $month1 . '-' . $day1;
        $date2 = $year2 . '-' . $month2 . '-' . $day2;
	$d1="$month1/$day1/$year1";
	$d2="$month2/$day2/$year2";
	$sql="select * from badlog where attwhen>='$date1 00:00:00' and attwhen<='$date2 23:59:59' order by sn,attwhen";
	$dh->Query($sql);
	$numrows=$dh->NumRows();
	$bgcolor="#00c0ff";
	list($c1,$c2,$c3,$c4,$c5)=explode(',',"30%,20%,12%,16%,22%");
	putMaintHeader($hdr);
	print <<< END
<center><table border="0" cellpadding="0">
<p align="center"><font size="6"><strong>Bad Login Report</strong></font></p><p align=center><font size=4>$d1 - $d2</font></p><br>
<tr><td align=center><table width=795 cellpadding=0>
END;
	$toggle=0;
	for ($i=0;$i<$numrows;$i++) {
		$r=$dh->FetchArray();
		$sn[$i]=$r['sn'];
		$acttime[$i]=$r['attwhen'];
		$result[$i]=$r['result'];
		$ps[$i]=$r['pass'];
		$ip[$i]=$r['ip'];
	}
	print <<< END
<tr><td width=$c1 valign=top bgcolor="$bgcolor"><p>&nbsp;Screen Name</p></td>
<td align=center valign=top bgcolor="$bgcolor" width=$c2><p>When</p></td>
<td align=center valign=top bgcolor="$bgcolor" width=$c3><p>Result</p></td>
<td align=center valign=top bgcolor="$bgcolor" width=$c4><p>IP Address</p></td>
<td align=center valign=top bgcolor="$bgcolor" width=$c5><p>Using Password</p></td>
</tr>
END;
	$lu="";
	for ($i=0,$j=count($sn);$i<$j;$i++) {
		$ls="&nbsp;";
		if ($lu!=$sn[$i]) {
			$toggle=0;
			$ls=$sn[$i];
			$lu=$ls;
			if ($i!=0) {
				print "<tr><td width 20% colspan=5><hr></td></tr>";
			}
		}
		$at=$acttime[$i];
		$when=substr($at,5,2).'/'.substr($at,8,2).'/'.substr($at,0,4).substr($at,10);
		$rs="&nbsp;";
		if ($result[$i]==2) { $rs="Frozen"; }
		$ipa=$ip[$i];
		$passw=$ps[$i];
		$bg="";
        	if ($toggle & 1) { $bg='#f5ede2'; }
		$toggle++;
		print <<< END
<tr><td align=left valign=top bgcolor="$bg" width=$c1><p>&nbsp;$ls</p></td>
<td align=center valign=top bgcolor="$bg" width=$c2><p>$when</p></td>
<td align=left valign=top bgcolor="$bg" width=$c3><p>$rs</p></td>
<td align=left valign=top bgcolor="$bg" width=$c4><p>$ipa</p></td>
<td align=left valign=top bgcolor="$bg" width=$c5><p>$passw</p></td>
</tr>
END;
	}
	print <<< END
</table></td></tr></table></center>
END;
	print <<< END
<p align=center><a href="index.php">Return to Main Menu</a></p>
END;
	putMaintFooter();
	exit;
}
$header="";
$hdr['focus']="date1_mon";
putMaintHeader($hdr);
print <<< END
<center><table border="0" cellpadding="0">
<tr><td><p align="center"><font size="6"><strong>Bad Login Report</strong></font><br></p><hr>
<form method="POST" enctype="multipart/form-data" name="mpform" action="badlog.php?s=1">
<center><table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse" width="760">
<tr><td width="35%" align="right" valign="top" colspan="2">$header</td></tr>
<tr><td align="right" width="35%" valign="top"><p>Start Date</p></td><td width="65%" valign="top" align=left><p>
END;
print datepick("date1",date('Y-m-d'),3,2010,2020,0);
print <<< END
</p></td></tr>
<tr><td align="right" width="35%" valign="top"><p>End Date</p></td><td width="65%" valign="top" align=left><p>
END;
print datepick("date2",date('Y-m-d'),3,2010,2020,0);
print <<< END
</p></td></tr>
<tr><td width=35%>&nbsp;</td><td width=65%><input type="submit" value="Produce Report" 
name="B1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onclick=self.location="index.php" 
value="Cancel" name="B2"></form></td></tr></table></center>
END;
putMaintFooter();
?>
