<?php
/*
	Reports for SM referers
        Copyright 2010 by Michael A. Lewis
        All Rights Reserved.

Edit history:
        Created 11/26/2009 09:43:15
*/
if (isset($_GET['seid'])) session_id($_GET['seid']);
include("../session.php");
include("../common.php");
include_once("../cal/calendar.php");
$cal_st=new DHTML_Calendar('../cal/','en','calendar-win2k-2',true);
$cal_en=new DHTML_Calendar('../cal/','en','calendar-win2k-2',true);
$hdr['html']=$cal_st->load_files();
$hdr['focus']="st";
$dh=new DB("findmyclass");
$FOCUSFIELD="st";
$hdr['backimage']="fade.gif";
$edate=$startdate=$enddate="";
$rtype="";
$rep=$ord="";
$proda=array();
if (!isset($_SESSION['rep_type'])) $_SESSION['rep_type']="";
if (!isset($_SESSION['rep_ord'])) $_SESSION['rep_ord']="";
$title=$hdr['title']="Social Media Access Report";
isLoggedIn("am");
$step=0;
if (isset($_GET['s'])) $step=$_GET['s'];
$POPUPHEADER=1;
putMaintHeader($hdr);
if ($step==1) {
	$startdate=$_POST['st']." 00:00:00";
	$enddate=$_POST['en']." 23:59:59";
	$how=$_SESSION['rep_ord'];
	if (isset($_POST['order'])) {
		$how=$_POST['order'];
		$_SESSION['rep_ord']=$ord;
	}
	$_SESSION['rep_st']=$_POST['st'];
	$_SESSION['rep_en']=$_POST['en'];
	if (isset($_POST['rep_type'])) $_SESSION['rep_type']=$_POST['rep_type'];
	$rtype=$_SESSION['rep_type'];
	switch ($rtype) {
		case "r":
			$rep=getRefererReport();
			break;
	}
	$step=0;
}	
if ($step==0) {
	$st=$en=date("Y-m-d");
	if (isset($_SESSION['rep_st'])) {
		$st=$_SESSION['rep_st'];
		$en=$_SESSION['rep_en'];
	}
	$sdate= $cal_st->make_input_field(array('firstDay' => 1, 'showsTime' => false, 'showOthers' => true,'ifFormat' =>'%Y-%m-%d'),
		array('style' => 'font-size:11px;width:5em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center','name' => 'st', 'value' => $st));
	$edate= $cal_st->make_input_field(array('firstDay' => 1, 'showsTime' => false, 'showOthers' => true,'ifFormat' =>'%Y-%m-%d'),
		array('style' => 'font-size:11px;width:5em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center','name' => 'en', 'value' => $en));
	$rtype=$_SESSION['rep_type'];
	$rt="<select name=rep_type size=1 style=\"font-size:10px\">";
	$ra=array("By Referer:r");
	for ($i=0;$i<count($ra);$i++) {
		list($name,$t)=explode(":",$ra[$i],2);
		$rt.="<option value=\"$t\"";
		if ($t==$rtype) $rt.=" selected";
		$rt.=">$name</option>";
	}
	$rt.="</select>";
	print <<< END
<table width=100% cellpadding=3 cellspacing=0>
<form name=mpform method=post action=smreport.php?s=1>
<tr>
<td width=90% valign=top>
<span style="font-size:11px">Start Date: $sdate &nbsp;&nbsp;End Date: $edate&nbsp;&nbsp;&nbsp;Report Type: $rt</span>&nbsp;&nbsp;<input type=image src=/images/go_icon.gif title="Begin Report">
</td>
<td width=10% valign=top align=right><a href=index.php><img src=/images/uplevel.gif border=0 title="Up one level" alt="Up one level"></a>
</td>
</tr>
</form>
</table>
<hr>
$rep
<hr>
END;
	putMaintFooter();
	exit;
}
//
//	Get report by Referer
//
function getRefererReport() {
	global	$dh,$dh1,$startdate,$enddate;

	$ga=array();
	$t="";
	$sql="select * from sm_ref where '$startdate' <= ref_when && ref_when <= '$enddate' order by ref_when";
	$dh->Query($sql);
	$nr=$dh->NumRows();
	$c=explode(",","25%,75%");
	$t.=<<< END

<h4>URL's That Linked to www.findmyclass4.me</h4>
<table width=990 cellpadding=3 cellspacing=1 border=0><style>p {font-size:13px}</style>
<tr bgcolor="#2020f0">
<td align=left valign=top width=$c[0]><p style="color:white"><b>Access Time</b></p></td>
<td align=center valign=top width=$c[1]><p style="color:white"><b>Referer</b></p></td>
</tr>
END;
	for ($i=0;$i<$nr;$i++) {
		$r=$dh->FetchArray();
		$refwhen=$r['ref_when'];
		$dom=stripslashes($r['domain']);
		list($x,$y)=explode("://",$dom,2);
		list($bd,$x)=explode("/",$y,2);
//		$bd=basedomain($dom);
		if (isset($ga[$bd])) {
			$ga[$bd]++;
		} else {
			$ga[$bd]=1;
		}
		$t.=<<< END
<tr>
<td align=left valign=top width=$c[0]><p>$refwhen</p></td>
<td align=left valign=top width=$c[1]><p>$dom</p></td>
</tr>
END;
	}
	$t.=<<< END
</table>
<br><br>
<table width=400 cellspacing=2 cellpadding=2>
<tr>
<td width=70% valign=top><b>Referrer Domain</b></td>
<td width=30% valign=top><b>Referrals</b></td>
</tr>
END;
	arsort($ga);
	foreach ($ga as $key=>$val) {
		$t.= <<< END
<tr>
<td width=70% valign=top>$key</td>
<td width=30% valign=top>$val</td>
</tr>
END;
	}
	$t.="</table>";
	return $t;
}
