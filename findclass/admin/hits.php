<?php
//
//	Web site hits report
//	Copyright 2010 by Michael A. Lewis
//	All Rights Reserved
//
include("../session.php");
include("../common.php");
isLoggedIn("am");
$title="Web Site Activity Report";
$hdr['title']=$title;
$step=0;
$dh=new DB("findmyclass");
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
	$file=file_get_contents("/httpd/logs/pl4u_access");
	$fa=explode("\n",$file);
	$nl=count($fa);
	$totalhits=0;
	$hits=0;
	$ld="";
	$oa=array();
	for ($i=0;$i<$nl;$i++) {
		if (strlen($fa[$i])<20) continue;
		list($ip,$x,$x1,$date,$offset,$type,$filename,$x4)=explode(" ",$fa[$i]);
		$date=substr($date,1);
		list($date,$hr,$mn,$sc)=explode(":",$date);
		list($day,$m,$year)=explode("/",$date);
		$mon=sprintf("%02d",stripos("   janfebmaraprmayjunjulaugsepoctnovdec",$m)/3);
		$cd="$year-$mon-$day";
		if ($cd<$date1) continue;
		if ($cd>$date2) break;
		if ($filename!="/") {
			if (stripos(" ".$filename,"index.php")==0) continue;
		}
		$totalhits++;
		if ($cd!=$ld) {
			if ($ld!="") {
				$oa[]="$ld,$hits";
				$hits=0;
			}
			$ld=$cd;
		}
		$hits++;
	}
	if ($hits) {
		$oa[]="$ld,$hits";
	}
	$bgcolor="#00c0ff";
	$c=explode(',',"40%,20%");
	putMaintHeader($hdr);
	print <<< END
<center><table border="0" cellpadding="0">
<p align="center"><font size="6"><strong>Web Site Activity Report</strong></font></p><p align=center><font size=4>$d1 - $d2</font></p><br>
<tr><td align=center><table width=795 cellpadding=0>
END;
	$toggle=0;
	print <<< END
<tr><td width=$c[0] valign=top bgcolor="$bgcolor"><p>&nbsp;Date</p></td>
<td align=center valign=top bgcolor="$bgcolor" width=$c[1]><p>Hits</p></td>
</tr>
END;
		for ($i=0;$i<count($oa);$i++) {
			list($d,$h)=explode(",",$oa[$i]);
		$bg="";
        	if ($toggle & 1) { $bg='#f5ede2'; }
		$toggle++;
		print <<< END
<tr><td align=left valign=top bgcolor="$bg" width=$c[0]><p>$d</p></td>
<td align=center valign=top bgcolor="$bg" width=$c[1]><p>$h</p></td>
</tr>
END;
	}
	print <<< END
<tr><td width=$c[0] valign=top bgcolor="$bgcolor" align=right><p>Total Hits</p></td>
<td align=center valign=top bgcolor="$bgcolor" width=$c[1]><p>$totalhits</p></td>
</tr>
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
<tr><td><p align="center"><font size="6"><strong>Web Site Activity Report</strong></font><br></p><hr>
<form method="POST" enctype="multipart/form-data" name="mpform" action="hits.php?s=1">
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
