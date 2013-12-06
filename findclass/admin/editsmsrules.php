<?php
/*
	Back End Screen for Table rcv_rules
	Copyright 2009 by Michael Lewis.
	All Rights Reserved.

Edit history:
	Created 09/04/2009 13:13:50
*/
include("../session.php");
include("../common.php");
$utype=$_SESSION['type'];
$hdr['title']="Maintain SMS Receive Rules";
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
$dh=new DB("sms");
$cid="";
$pid=0;
$clid=0;
if ($_SESSION['cp']!="smsrules.php") {
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
	$dh->Exec("delete from rcv_rules where id='$pid'");
	header("location: smsrules.php?idx=$cid$return");
	exit;
}
$error="";
if ($step==1) {
	if (isset($_POST['owner'])) $owner=$_POST['owner'];
	$name="";
	if (isset($_POST['name'])) $name=$_POST['name'];
	$email_to="";
	if (isset($_POST['email_to'])) $email_to=$_POST['email_to'];
	$st_time="";
	if (isset($_POST['st_time_hour'])) $st_time=$_POST['st_time_hour'].":".$_POST['st_time_min'];
	$en_time="";
	if (isset($_POST['en_time_hour'])) $en_time=$_POST['en_time_hour'].":".$_POST['en_time_min'];
	$if_from="";
	if (isset($_POST['if_from'])) $if_from=$_POST['if_from'];
	$if_contains="";
	if (isset($_POST['if_contains'])) $if_contains=$_POST['if_contains'];
	$if_starts="";
	if (isset($_POST['if_starts'])) $if_starts=$_POST['if_starts'];
	$if_ends="";
	if (isset($_POST['if_ends'])) $if_ends=$_POST['if_ends'];
	$lst=$lcon=$len="O";
	if (isset($_POST['lcon'])) $log_contains=$_POST['lcon'];
	if (isset($_POST['lst'])) $log_starts=$_POST['lst'];
	if (isset($_POST['len'])) $log_ends=$_POST['len'];
	$owner=$_SESSION['valid'];
	$da=array('sun'=>"N",'mon'=>"N",'tue'=>"N",'wed'=>"N",'thu'=>"N",'fri'=>"N",'sat'=>"N");
	$nu=0;
	$nsu=0;
	$ua=array();
	$sua=array();
	$eua=array();
	foreach ($_POST as $k=>$v) {
		if (substr($k,0,2)=="g_") {
			$ua[]="+$v";
			$nu++;
		}
		if (substr($k,0,2)=="u_") {
			$ua[]=":$v";
			$nu++;
		}
		if (substr($k,0,3)=="sg_") {
			$sua[]="+$v";
			$snu++;
		}
		if (substr($k,0,3)=="su_") {
			$sua[]=":$v";
			$snu++;
		}
		if (substr($k,0,4)=="dow_") {
			list($x,$y)=explode("_",$k,2);
			$da[$y]="Y";
		}
		if (substr($k,0,3)=="eu_") {
			$eua[]=":$v";
		}
		if (substr($k,0,3)=="eg_") {
			$eua[]="+$v";
		}
	}
	$if_from="";
	if ($nu) $if_from=join(";",$ua);
	$name=htmlentities(addslashes($name),ENT_QUOTES);
	$email_to=htmlentities(addslashes($email_to),ENT_QUOTES);
	$sms_to="";
	if ($snu) $sms_to=join(";",$sua);
	$sms_to=addslashes($sms_to);
	$exc_from="";
	if (count($eua)) $exc_from=join(";",$eua);
	$exc_from=addslashes($exc_from);
	$st_time=htmlentities(addslashes($st_time),ENT_QUOTES);
	$en_time=htmlentities(addslashes($en_time),ENT_QUOTES);
	$if_from=addslashes($if_from);
	$if_contains=htmlentities(addslashes($if_contains),ENT_QUOTES);
	$if_starts=htmlentities(addslashes($if_starts),ENT_QUOTES);
	$if_ends=htmlentities(addslashes($if_ends),ENT_QUOTES);
	if (strlen($name)==0) {
		$error.=" 'Rule Name' is a required entry.";
	}
	if (strlen($error)==0) {
		$sun=$da['sun'];
		$mon=$da['mon'];
		$tue=$da['tue'];
		$wed=$da['wed'];
		$thu=$da['thu'];
		$fri=$da['fri'];
		$sat=$da['sat'];
		if ($pid==0) {
			$dh->Exec("insert into rcv_rules (owner,name,email_to,sms_to,st_time,en_time,if_from,if_contains,if_starts,if_ends,log_contains,log_starts,log_ends,sun,mon,tue,wed,thu,fri,sat,exc_from) values ('$owner','$name','$email_to','$sms_to','$st_time','$en_time','$if_from','$if_contains','$if_starts','$if_ends','$log_contains','$log_starts','$log_ends','$sun','$mon','$tue','$wed','$thu','$fri','$sat','$exc_from')");
			$pid=$dh->LastID();
		} else {
			$extra="";
			$sql="update rcv_rules set owner='$owner',name='$name',email_to='$email_to',sms_to='$sms_to',st_time='$st_time',en_time='$en_time',if_from='$if_from',if_contains='$if_contains',if_starts='$if_starts',if_ends='$if_ends',log_contains='$log_contains',log_starts='$log_starts',log_ends='$log_ends',sun='$sun',mon='$mon',tue='$tue',wed='$wed',thu='$thu',fri='$fri',sat='$sat',exc_from='$exc_from'$extra where id='$pid'";
			$dh->Exec($sql);
		}
		header("location: smsrules.php?idx=$cid$return");
		exit;
	}
}
$action="Add New";
if ($pid) $action="Edit";
if (strlen($error)==0) {
	if ($pid==0 && $clid==0) {
		$owner=$_SESSION['valid'];
		$name="";
		$email_to="";
		$sms_to="";
		$st_time="00:00";;
		$en_time="23:59";
		$if_from="";
		$if_contains="";
		$if_starts="";
		$if_ends="";
		$log_contains="O";
		$log_starts="O";
		$log_ends="O";
		$log_contains=$log_starts=$log_ends="O";
		$r=array();
		$r['sun']="Y";
		$r['mon']="Y";
		$r['tue']="Y";
		$r['wed']="Y";
		$r['thu']="Y";
		$r['fri']="Y";
		$r['sat']="Y";
		$exc_from="";
	} else {
		$wid=$pid;
		if ($wid==0) $wid=$clid;
		$dh->Query("select * from rcv_rules where id='$wid'");
		$r=$dh->FetchArray();
		$owner=stripslashes($r['owner']);
		$name=stripslashes($r['name']);
		$email_to=stripslashes($r['email_to']);
		$sms_to=stripslashes($r['sms_to']);
		$st_time=stripslashes($r['st_time']);
		$en_time=stripslashes($r['en_time']);
		$if_from=stripslashes($r['if_from']);
		$if_contains=stripslashes($r['if_contains']);
		$if_starts=stripslashes($r['if_starts']);
		$if_ends=stripslashes($r['if_ends']);
		$log_contains=$r['log_contains'];
		$log_starts=$r['log_starts'];
		$log_ends=$r['log_ends'];
		$exc_from=$r['exc_from'];
	}
} else {
	$error="<tr><td colspan=2><table width=100% border=1 cellpadding=3 callspacing=0><tr><td align=center><p><font color=red>$error</font></p></td></tr></table></td></tr>";
}
putMaintHeader($hdr);
$dxa=explode(",","sun,mon,tue,wed,thu,fri,sat");
for ($i=0;$i<count($dxa);$i++) {
	$fld=$dxa[$i];
	$dowv[$fld]="";
	if ($r[$fld]=="Y") {
		$dowv[$fld]=" checked";
	}
}
$w="10%";
$dowt=<<< END
<table width=300 cellpadding=0 cellspacing=0><tr>
<td width=$w align=center valign=top>SUN</td>
<td width=$w align=center valign=top>MON</td>
<td width=$w align=center valign=top>TUE</td>
<td width=$w align=center valign=top>WED</td>
<td width=$w align=center valign=top>THU</td>
<td width=$w align=center valign=top>FRI</td>
<td width=$w align=center valign=top>SAT</td>
</tr>
<tr>
END;
for ($i=0;$i<count($dxa);$i++) {
	$fld=$dxa[$i];
	$dowt.="<td width=$w align=center valign=top><input type=checkbox value=\"Y\" name=dow_$dxa[$i]$dowv[$fld]></td>";
}
$dowt.="</tr></table>";
$cw=explode(",","30%,70%");

	$ua=array();
	$sua=array();
	$eua=array();
	if (strpos($if_from,";")) {
		$ua=explode(";",$if_from);
	} else {
		if (strlen($if_from)) $ua[0]=$if_from;
	}
	if (strpos($sms_to,";")) {
		$sua=explode(";",$sms_to);
	} else {
		if (strlen($sms_to)) $sua[0]=$sms_to;
	}
	if (strpos($exc_from,";")) {
		$eua=explode(";",$exc_from);
	} else {
		if (strlen($exc_from)) $eua[0]=$exc_from;
	}
	$grp=getGroup(0);
	$to=getUser(0);
	$sgrp=getGroup(1);
	$sto=getUser(1);
	$eto=getUser(2);
	$if="$grp<tr><td colspan=4><hr></tr>$to";
	$sm="$sgrp<tr><td colspan=4><hr></tr>$sto";
	$exc="$eto";
	$lcon=$lst=$len=$achk=$ochk="";
	if ($log_contains=="A") {
		$achk=" checked";
	} else {
		$ochk=" checked";
	}
	$lcon=<<< END
<input type=radio name=lcon value="A"$achk>&nbsp;And&nbsp;&nbsp;<input type=radio name=lcon value="O"$ochk>&nbsp;Or&nbsp;&nbsp;&nbsp;
END;
	$achk=$ochk="";
	if ($log_starts=="A") {
		$achk=" checked";
	} else {
		$ochk=" checked";
	}
	$lst=<<< END
<input type=radio name=lst value="A"$achk>&nbsp;And&nbsp;&nbsp;<input type=radio name=lst value="O"$ochk>&nbsp;Or&nbsp;&nbsp;&nbsp;
END;
	$achk=$ochk="";
	if ($log_ends=="A") {
		$achk=" checked";
	} else {
		$ochk=" checked";
	}
	$len=<<< END
<input type=radio name=len value="A"$achk>&nbsp;And&nbsp;&nbsp;<input type=radio name=len value="O"$ochk>&nbsp;Or&nbsp;&nbsp;&nbsp;
END;
print <<< END
</td></tr></table>
<form name=mpform action=editsmsrules.php?idx=$cid&s=1&id=$pid$return method=post enctype="multipart/form-data"><center><table border=0 cellpadding=8 cellspacing=0 width=1000>
<tr><td width=90%><h1>$action SMS Receive Rules</h1></td>
<td width=10% align=right valign=top>
<a href=smsrules.php?idx=$cid$return><img src=/images/uplevel.gif border=0 title="Up one level" alt="Up one level"></a>
</td>
</tr>
$error
<tr><td colspan=2><table width=100% border=1 cellpadding=3 cellspacing=0>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Rule Name</p></td><td width=$cw[1] valign=top><input type=text name=name value="$name" size=64></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Who to Email</p></td><td width=$cw[1] valign=top><input type=text name=email_to value="$email_to" size=100></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Who to Text</p></td><td width=$cw[1] valign=top><table width=100% cellpadding=0 cellspacing=0>$sm</table></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Days of Week Rule is Active</p></td><td width=$cw[1] valign=top>$dowt</td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Start Time Rule in Effect</p></td><td width=$cw[1] valign=top>
END;
print timepick("st_time",$st_time);
print <<< END
</td></tr>
<tr><td width=$cw[0] align=right valign=top><p>End Time Rule in Effect</p></td><td width=$cw[1] valign=top>
END;
print timepick("en_time",$en_time);
print <<< END
</td></tr>
<tr><td colspan=2 align=center><p><font size=3>Define Rule Parameters Below</font></p></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>If Text From...</p></td><td width=$cw[1] valign=top><table width=100% cellpadding=0 cellspacing=0>$if</table></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>But Exempt These...</p></td><td width=$cw[1] valign=top><table width=100% cellpadding=0 cellspacing=0>$exc</table></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>$lcon If Text Contains...</p></td><td width=$cw[1] valign=top><input type=text name=if_contains value="$if_contains" size=64> <font size=1>(separate multiple entries with a "|" character)</font></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>$lst If Text Starts With...</p></td><td width=$cw[1] valign=top><input type=text name=if_starts value="$if_starts" size=64> <font size=1>(separate multiple entries with a "|" character)</font></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>$len If Text Ends With...</p></td><td width=$cw[1] valign=top><input type=text name=if_ends value="$if_ends" size=64> <font size=1>(separate multiple entries with a "|" character)</font></td></tr>
END;

$action="Add New Record";
if ($pid) {
	$action="Save Changes";
	print "<tr><td colspan=2 valign=top align=right><input type=checkbox name=delete_rec value=\"Y\">&nbsp; Delete this record?</td></tr>";
}
print <<< END
<tr><td colspan=2 align=center><input type=submit value="$action">&nbsp;&nbsp;&nbsp;<input type=button value="Cancel" onclick=self.location="smsrules.php?idx=$cid$return"></td></tr>
</table></td></tr>
</table></center></form>
END;
putMaintFooter();
//
//	Return group table entries
//
function getGroup($uf) {
	global $dh,$ua,$sua,$eua,$sms_to,$if_from,$owner;
	
	$grp="<tr><td colspan=4>Groups</td></tr>";
	$dh->Query("select * from sms.groups where owner='$owner' order by name");
	$k=0;
	$npr=4;
	$wid=floor(100/$npr);
	$nr=$dh->NumRows();
	switch ($uf) {
		case 0:
			$pre="g_";
			break;
		case 1: 
			$pre="sg_";
			break;
		case 2:
			$pre="eg_";
			break;
	}
	for ($i=0;$i<$nr;$i++) {
		$r=$dh->FetchArray();
		$gname=stripslashes($r['name']);
		$uid=$r['id'];
		if ($k==0) $grp.="<tr>";
		$chk="";
		switch ($uf) {
			case 0:
				$base=100000;
				if (in_array("+$gname",$ua)) $chk=" checked";
				break;
			case 1: 
				if (in_array("+$gname",$sua)) $chk=" checked";
				$base=200000;
				break;
			case 2:
				$base=300000;
				if (in_array("+$gname",$eua)) $chk=" checked";
				break;
		}
		$ii=$base+$i;
		if (strlen($chk)) {
			$grp.="<td valign=top width=$wid%><span id=fn_$ii style=\"background-color:yellow\"><input type=checkbox name=$pre$i value=\"$gname\" checked onclick=\"doBack(this,'fn_$ii');\">&nbsp;$gname</span></td>";
		} else {
			$grp.="<td valign=top width=$wid%><span id=fn_$ii><input type=checkbox name=$pre$i value=\"$gname\" onclick=\"doBack(this,'fn_$ii');\">&nbsp;$gname</span></td>";
		}
		$k++;
		if ($k==$npr) {
			$k=0;
			$grp.="</tr>";
		}
	}
	if ($k) {
		for (;$k<$npr;$k++) {
			$grp.="<td width=$wid%>&nbsp;</td>";
		}
		$grp.="</tr>";
	}
	return $grp;
}
//
//      Return group table entries
//
function getUser($uf) {
        global $dh,$ua,$sua,$eua,$sms_to,$if_from,$owner;

	$to="<tr><td colspan=4>Users</td></tr>";
	$dh->Query("select * from sms.user where owner='$owner' order by lastname");
	$k=0;
	$npr=4;
	$wid=floor(100/$npr);
	$nr=$dh->NumRows();
	switch ($uf) {
		case 0:
			$pre="u_";
			$base=100;
			break;
		case 1: 
			$pre="su_";
			$base=1000;
			break;
		case 2:
			$pre="eu_";
			$base=10000;
			break;
	}
	for ($i=0;$i<$nr;$i++) {
		$r=$dh->FetchArray();
		$lname=stripslashes($r['lastname']);
		$fname=stripslashes($r['firstname']);
		$phone=stripslashes($r['phone']);
		if (strlen($fname)) {
			$fullname="$lname, $fname";
			$vname="$lname,$fname";
		} else {
			$vname=$fullname="$lname";
		}
		$uid=$r['id'];
		if ($k==0) $to.="<tr>";
		$chk="";
		switch($uf) {
			case 0:
				if (in_array(":$vname",$ua)) $chk=" checked";
				break;
			case 1:
				if (in_array(":$vname",$sua)) $chk=" checked";
				break;
			case 2:
				if (in_array(":$vname",$eua)) $chk=" checked";
				break;
		}
		$ii=$base+$i;
		if (strlen($chk)) {
			$to.="<td valign=top width=$wid%><span id=fn_$ii style=\"background-color:yellow\" onclick=\"doBack(this,'fn_$ii');\"><input type=checkbox name=$pre$i value=\"$vname\" checked>&nbsp;$fullname</span></td>";
		} else {
			$to.="<td valign=top width=$wid%><span id=fn_$ii><input type=checkbox name=$pre$i value=\"$vname\" onclick=\"doBack(this,'fn_$ii');\">&nbsp;$fullname</span></td>";
		}
		$k++;
		if ($k==$npr) {
			$k=0;
			$to.="</tr>";
		}
	}
	if ($k) {
		for (;$k<$npr;$k++) {
			$to.="<td width=$wid%>&nbsp;</td>";
		}
		$to.="</tr>";
	}
	return $to;
}

