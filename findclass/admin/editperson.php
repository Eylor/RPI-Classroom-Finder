<?php
/*
	Back End Screen for Table person
	Copyright 2005 by Michael Lewis.
	All Rights Reserved.

Edit history:
	Created 09/11/2005 12:02:47
*/
include("../session.php");
include("../common.php");
$utype=$_SESSION['usertype'];
$hdr['title']="Maintain Web Site User File";
include_once("../cal/calendar.php");
$cal_enddate=new DHTML_Calendar('../cal/','en','calendar-win2k-2',true);
$hdr['html']=$cal_enddate->load_files();
$hdr['focus']="password";
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
$dh=new DB("findmyclass");
$cid="";
$pid=0;
$clid=0;
if ($_SESSION['cp']!="person.php") {
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
	$dh->Exec("delete from person where id='$pid'");
	header("location: person.php?idx=$cid$return");
	exit;
}
$error="";
if ($step==1) {
	$password="";
	if (isset($_POST['password'])) $password=$_POST['password'];
	$lastname="";
	if (isset($_POST['lastname'])) $lastname=$_POST['lastname'];
	$lastname=ucfirst(strtolower($lastname));
	$firstname="";
	if (isset($_POST['firstname'])) $firstname=$_POST['firstname'];
	$firstname=ucfirst(strtolower($firstname));
	$active="";
	if (isset($_POST['active'])) $active=$_POST['active'];
	$email="";
	if (isset($_POST['email'])) $email=$_POST['email'];
	$type="";
	if (isset($_POST['type'])) $type=$_POST['type'];
	$address="";
	if (isset($_POST['address'])) $address=$_POST['address'];
	$city="";
	if (isset($_POST['city'])) $city=$_POST['city'];
	$state="";
	if (isset($_POST['state'])) $state=$_POST['state'];
	$postal_code="";
	if (isset($_POST['postal_code'])) $postal_code=$_POST['postal_code'];
	$phone1="";
	if (isset($_POST['phone1'])) $phone1=$_POST['phone1'];
	$phone2="";
	if (isset($_POST['phone2'])) $phone2=$_POST['phone2'];
	$startdate="";
	if (isset($_POST['startdate'])) $startdate=$_POST['startdate'];
	$enddate="";
	if (isset($_POST['enddate'])) $enddate=$_POST['enddate'];
	if (isset($_POST['lastactivity'])) $lastactivity=$_POST['lastactivity'];
	$lastlogged="";
	if (isset($_POST['lastlogged'])) $lastlogged=$_POST['lastlogged'];
	if (isset($_POST['ta'])) $ta=$_POST['ta'];
	if (isset($_POST['ba'])) $ba=$_POST['ba'];
	if (isset($_POST['lastviewed'])) $lastviewed=$_POST['lastviewed'];
	$pagesize="";
	if (isset($_POST['pagesize'])) $pagesize=$_POST['pagesize'];
	$updocs="";
	if (isset($_POST['updocs'])) $updocs=$_POST['updocs'];
	$canpost="";
	if (isset($_POST['canpost'])) $canpost=$_POST['canpost'];
	$galrev="";
	if (isset($_POST['galrev'])) $galrev=$_POST['galrev'];
	$docrev="";
	if (isset($_POST['docrev'])) $docrev=$_POST['docrev'];
	$handle="";
	if (isset($_POST['handle'])) $handle=$_POST['handle'];
	$password=htmlentities(addslashes($password),ENT_QUOTES);
	$lastname=htmlentities(addslashes($lastname),ENT_QUOTES);
	$firstname=htmlentities(addslashes($firstname),ENT_QUOTES);
	$active=htmlentities(addslashes($active),ENT_QUOTES);
	$email=htmlentities(addslashes($email),ENT_QUOTES);
	$handle=htmlentities(addslashes($handle),ENT_QUOTES);
	$type=htmlentities(addslashes($type),ENT_QUOTES);
	$address=htmlentities(addslashes($address),ENT_QUOTES);
	$city=htmlentities(addslashes($city),ENT_QUOTES);
	$state=htmlentities(addslashes($state),ENT_QUOTES);
	$postal_code=htmlentities(addslashes($postal_code),ENT_QUOTES);
	$phone1=htmlentities(addslashes($phone1),ENT_QUOTES);
	$phone2=htmlentities(addslashes($phone2),ENT_QUOTES);
	$startdate=htmlentities(addslashes($startdate),ENT_QUOTES);
	$enddate=htmlentities(addslashes($enddate),ENT_QUOTES);
	$lastactivity=htmlentities(addslashes($lastactivity),ENT_QUOTES);
	$lastlogged=htmlentities(addslashes($lastlogged),ENT_QUOTES);
	$ta=htmlentities(addslashes($ta),ENT_QUOTES);
	$ba=htmlentities(addslashes($ba),ENT_QUOTES);
	$lastviewed=htmlentities(addslashes($lastviewed),ENT_QUOTES);
	$pagesize=htmlentities(addslashes($pagesize),ENT_QUOTES);
	$updocs=htmlentities(addslashes($updocs),ENT_QUOTES);
	$canpost=htmlentities(addslashes($canpost),ENT_QUOTES);
	$galrev=htmlentities(addslashes($galrev),ENT_QUOTES);
	$docrev=htmlentities(addslashes($docrev),ENT_QUOTES);
	$dh->Query("select id from person where `handle`='$handle'");
	$r=$dh->FetchArray();
	if ($dh->NumRows() && $r['id']!=$pid) $error.=" $handle is already on file as an user. Please use a different user name.";
	if (strlen($password)==0) {
		$error.=" 'Password' is a required entry.";
	} else {
		if (strlen($password)<6) $error.=" 'Password' must be at least 6 characters in length.";
	}
//	$special=0;
//	for ($i=0,$j=strlen($password);$i<$j;$i++) {
//		$c=substr($password,$i,1);   
//		if ($c==" " || !(strpos(" abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",$c))) {
//			$special=1;
//			break;
//		}
//	}
//	if ($special==0) $error.=" Passwords cannot be only letters in field password.";
	if (strlen($lastname)==0) {
		$error.=" 'Last Name' is a required entry.";
	}
	if (strlen($firstname)==0) {
		$error.=" 'First Name' is a required entry.";
	}
	if (strlen($email)==0) {
		$error.=" 'Email Address' is a required entry.";
	}
//	if (strlen($address)==0) {
//		$error.=" 'Address' is a required entry.";
//	}
//	if (strlen($city)==0) {
//		$error.=" 'city' is a required entry.";
//	}
//	if (strlen($postal_code)==0) {
//		$error.=" 'Postal Code' is a required entry.";
//	}
//	if (strlen($phone1)==0) {
//		$error.=" 'Telephone Number' is a required entry.";
//	}
	if (strlen($email) && validemail($email)==0) $error.=" Field email does not contain a valid email address.";
	if (strlen($type)==0) {
		$error.=" 'User Type' is a required entry.";
	}
	$credits=0.0;
	$invoice="N";
	$creditlevel=0.00;
	if (isset($_POST['invoice'])) $invoice=$_POST['invoice'];
	$credits=$_POST['credits'];
	$balance=$_POST['balance'];
	$creditlevel=$_POST['credit_level'];
	if (strlen($error)==0) {
		$password=encStr($password);
		if ($pid==0) {
			$dh->Exec("insert into person (password,lastname,firstname,active,email,type,address,city,state,postal_code,phone1,phone2,startdate,enddate,lastactivity,lastlogged,ta,ba,lastviewed,pagesize,updocs,canpost,galrev,docrev,balance,credits,invoice,credit_level,handle) values ('$password','$lastname','$firstname','$active','$email','$type','$address','$city','$state','$postal_code','$phone1','$phone2','$startdate','$enddate','$lastactivity','$lastlogged','$ta','$ba','$lastviewed','$pagesize','$updocs','$canpost','$galrev','$docrev','$balance','$credits','$invoice','$creditlevel','$handle')");
			$pid=$dh->LastID();
		} else {
			$extra="";
			$dh->Exec("update person set invoice='$invoice',balance='$balance',credits='$credits',credit_level='$creditlevel',password='$password',lastname='$lastname',firstname='$firstname',active='$active',email='$email',type='$type',address='$address',city='$city',state='$state',postal_code='$postal_code',phone1='$phone1',phone2='$phone2',startdate='$startdate',enddate='$enddate',lastactivity='$lastactivity',lastlogged='$lastlogged',ta='$ta',ba='$ba',lastviewed='$lastviewed',pagesize='$pagesize',updocs='$updocs',canpost='$canpost',galrev='$galrev',docrev='$docrev',handle='$handle'$extra where id='$pid'");
		}
		header("location: person.php?idx=$cid$return");
		exit;
	}
}
$action="Add New";
if ($pid) $action="Edit";
if (strlen($error)==0) {
	if ($pid==0 && $clid==0) {
		$password="";
		$lastname="";
		$firstname="";
		$active="Y";
		$email="";
		$handle="";
		$type="sa";
		$address="";
		$city="";
		$state="PA";
		$postal_code="";
		$phone1="";
		$phone2="";
		$startdate=date('Y-m-d');
		$enddate="";
		$lastactivity="";
		$lastlogged="";
		$ta=0;
		$ba=0;
		$lastviewed=0;
		$pagesize=20;
		$updocs="Y";
		$canpost="Y";
		$galrev="Y";
		$docrev="Y";
		$credits=0.0;
		$credit_level=0.00;
		$invoice="N";
		$balance=0.00;
	} else {
		$wid=$pid;
		if ($wid==0) $wid=$clid;
		$dh->Query("select * from person where id='$wid'");
		$r=$dh->FetchArray();
		$password=stripslashes(decStr($r['password']));
		$lastname=stripslashes($r['lastname']);
		$firstname=stripslashes($r['firstname']);
		$active=stripslashes($r['active']);
		$email=stripslashes($r['email']);
		$handle=stripslashes($r['handle']);
		$type=stripslashes($r['type']);
		$address=stripslashes($r['address']);
		$city=stripslashes($r['city']);
		$state=stripslashes($r['state']);
		$postal_code=stripslashes($r['postal_code']);
		$phone1=stripslashes($r['phone1']);
		$phone2=stripslashes($r['phone2']);
		$startdate=stripslashes($r['startdate']);
		$enddate=stripslashes($r['enddate']);
		$lastactivity=stripslashes($r['lastactivity']);
		$lastlogged=stripslashes($r['lastlogged']);
		$ta=stripslashes($r['ta']);
		$ba=stripslashes($r['ba']);
		$lastviewed=stripslashes($r['lastviewed']);
		$pagesize=stripslashes($r['pagesize']);
		$updocs=stripslashes($r['updocs']);
		$canpost=stripslashes($r['canpost']);
		$galrev=stripslashes($r['galrev']);
		$docrev=stripslashes($r['docrev']);
		$balance=$r['balance'];
		$creditlevel=$r['credit_level'];
		$credits=$r['credits'];
		$invoice=$r['invoice'];
	}
} else {
	$error="<tr><td colspan=2><table width=100% border=1 cellpadding=3 callspacing=0><tr><td align=center><p><font color=red>$error</font></p></td></tr></table></td></tr>";
}
putMaintHeader($hdr);
$chkinv=$chkactive=$chkupdocs=$chkcanpost=$chkgalrev=$chkdocrev="";
if ($invoice=="Y") $chkinv=" checked";
if ($active=="Y") $chkactive=" checked";
if ($updocs=="Y") $chkupdocs=" checked";
if ($canpost=="Y") $chkcanpost=" checked";
if ($galrev=="Y") $chkgalrev=" checked";
if ($docrev=="Y") $chkdocrev=" checked";
$cw=explode(",","35%,65%");
$cal_startdate=new DHTML_Calendar('../cal/','en','calendar-win2k-2',true);
print <<< END
<form name=mpform action=editperson.php?idx=$cid&s=1&id=$pid$return method=post enctype="multipart/form-data"><center><table border=0 cellpadding=8 cellspacing=0 width=740>
<tr><td width=90%><h1>$action Users</h1></td>
<td width=10% align=right valign=top>
<a href=person.php?idx=$cid$return><img src=images/uplevel.gif border=0 onMouseover="showtip(this,event,'Up one level');" onMouseOut=hidetip();></a>
</td>
</tr>
$error
<tr><td colspan=2><table width=100% border=1 cellpadding=3 cellspacing=0>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Password</p></td><td width=$cw[1] valign=top><input type=text name=password value="$password" size=20> <font size=1>(Must be at least 7 character one of which cannot be a letter)</font></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Last Name</p></td><td width=$cw[1] valign=top><input type=text name=lastname value="$lastname" size=40></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>First Name</p></td><td width=$cw[1] valign=top><input type=text name=firstname value="$firstname" size=40></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Active</p></td><td width=$cw[1] valign=top><input type=checkbox name=active$chkactive value="Y"> <font size=1>(Uncheck to deny logon to the site)</font></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Email Address</p></td><td width=$cw[1] valign=top><input type=text name=email value="$email" size=45></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Handle</p></td><td width=$cw[1] valign=top><input type=text name=handle value="$handle" size=45></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>User Type</p></td><td width=$cw[1] valign=top>
END;
$dh->Query("select * from usertypes order by name");
$nr=$dh->NumRows();
$size=5;
if ($nr<5) $size=$nr;
if ($size==0) $size=1;
print "<select name=type size=$size>";
if (isset($v)) unset($v);
if (isset($tax)) unset($tax);
if (strpos($type,";")) {
	$tax=explode(";",$type);
} else {
	$tax[0]=$type;
}
for ($i=0;$i<$nr;$i++) {
	$r=$dh->FetchArray();
	print "<option value=\"".$r['typecode'].'"'; 
	if (in_array($r['typecode'],$tax)) print " selected";
	print ">".$r['name']."</option>\n";
}
print <<< END
</select>
</td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Address</p></td><td width=$cw[1] valign=top><input type=text name=address value="$address" size=45></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>City</p></td><td width=$cw[1] valign=top><input type=text name=city value="$city" size=45></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>State</p></td><td width=$cw[1] valign=top>
END;
print statepick("state",$state);
print <<< END
</td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Zip Code</p></td><td width=$cw[1] valign=top><input type=text name=postal_code value="$postal_code" size=10></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Telephone Number</p></td><td width=$cw[1] valign=top><input type=text name=phone1 value="$phone1" size=14></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Cell Phone Number</p></td><td width=$cw[1] valign=top><input type=text name=phone2 value="$phone2" size=14></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Start Date</p></td><td width=$cw[1] valign=top>
END;
print $cal_startdate->make_input_field(array('firstDay' => 1, 'showsTime' => false, 'showOthers' => true,'ifFormat' =>'%Y-%m-%d'),
array('style' => 'width: 6em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center','name' => 'startdate', 'value' => $startdate));
print <<< END
</td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Account Expires</p></td><td width=$cw[1] valign=top>
END;
print $cal_enddate->make_input_field(array('firstDay' => 1, 'showsTime' => false, 'showOthers' => true,'ifFormat' =>'%Y-%m-%d'),
array('style' => 'width: 6em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center','name' => 'enddate', 'value' => $enddate));
print <<< END
</td></tr>
<input type=hidden name=lastactivity value="$lastactivity"><tr><td width=$cw[0] align=right valign=top><p>Last Logged On</p></td><td width=$cw[1] valign=top><p>$lastlogged</p><input type=hidden name=lastlogged value="$lastlogged"></td></tr>
<input type=hidden name=ta value="$ta"><input type=hidden name=ba value="$ba"><input type=hidden name=lastviewed value="$lastviewed">
<tr><td colspan=2 align=center><p><font size=3>Configuration Parameters</font></p></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Result # of Rows</p></td><td width=$cw[1] valign=top><input type=text name=pagesize value="$pagesize" size=2></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Can Upload Documents?</p></td><td width=$cw[1] valign=top><input type=checkbox name=updocs$chkupdocs value="Y"></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Can Post Messages?</p></td><td width=$cw[1] valign=top><input type=checkbox name=canpost$chkcanpost value="Y"></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Uploaded Documents Require Review?</p></td><td width=$cw[1] valign=top><input type=checkbox name=docrev$chkdocrev value="Y"></td></tr>
<tr><td colspan=2 align=center><p><font size=3>Accounting Parameters</font></p></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Can Buy on Credit?</p></td><td width=$cw[1] valign=top><input type=checkbox name=invoice$chkinv value="Y">&nbsp;&nbsp;&nbsp;
Credit limit: <input type=text size=6 name=credit_level value="$creditlevel"></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Account Balance</p></td><td width=$cw[1] valign=top><input type=text name=balance value="$balance" size=8></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Number of credits</p></td><td width=$cw[1] valign=top><input type=text name=credits value="$credits" size=8></td></tr>
END;
$action="Add New Record";
if ($pid) {
	$action="Save Changes";
	print "<tr><td colspan=2 valign=top align=right><input type=checkbox name=delete_rec value=\"Y\">&nbsp; Delete this record?</td></tr>";
}
print <<< END
<tr><td colspan=2 align=center><input type=submit value="$action">&nbsp;&nbsp;&nbsp;<input type=button value="Cancel" onclick=self.location="person.php?idx=$cid$return"></td></tr>
</table></td></tr>
</table></center></form>
END;
putMaintFooter();
?>

