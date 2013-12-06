<?php 
//
//	Send SMS messages using SMS gateway
//	Copyright 2009 by Michael A. Lewis
//	All Rights Reserved
//
include("../session.php");
include("../common.php");
$step=0;
if (isset($_GET['s'])) $step=$_GET['s'];
$hdr['title']="Send SMS Message";
$owner=$_SESSION['valid'];
$ga=array();
$ua=array();
$dh=new DB("sms");
$usesig=1;
$fid="";
$u="";
$msg="";
$from="";
if ($step==1) {
	$nu=0;
	$msg=trim($_POST['msg']);
	$fid="";
	if (isset($_POST['who_from'])) $fid=$_POST['who_from'];
	$usesig=0;
	if (isset($_POST['usesig'])) $usesig=1;
	foreach ($_POST as $k=>$v) {
		if (substr($k,0,2)=="g_") {
			$ga[]="+$v";
			$nu++;
		}
		if (substr($k,0,2)=="u_") {
			$ua[]=":$v";
			$nu++;
		}
	}
	$u=str_replace(array("-","("," ",")"),"",trim($_POST['users']));
	if (strlen($u)) {
		$tua=array();
		if (strpos($u,";")) {
			$tua=explode(";",$u);
		} else {
			$tua[]=trim($u);
		}
		for ($i=0;$i<count($tua);$i++) {
			$us=trim($tua[$i]);
			$ua[]=$us;
			$nu++;
		}
	}
	if (strlen($msg) && $nu && $fid!="") {
		$dh->Query("select * from sms.cred where id='$fid'");
		$r=$dh->FetchArray();
		$uname=stripslashes($r['username']);
		$pw=stripslashes($r['password']);
		$to="";
		$sep="";
		for ($i=0;$i<count($ga);$i++) {
			$to.="$sep$ga[$i]";
			$sep=";";
		}
		for ($i=0;$i<count($ua);$i++) {
			$to.="$sep$ua[$i]";
			$sep=";";
		}
		$data=array(
		  "username" => $uname,
		  "password" => $pw,
		  "msg"      => $msg,
		  'silent'   => $usesig,
		  'to'       => $to,
		  "SUBMIT" => 'true'
		);
		$cookie="sms.txt";
		$useragent="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)";
		print postInfo("https://nd.xelent.net/utils/smsput.php",$data);
		putMaintHeader($hdr);
		print <<< END
<center>
<table width=400 cellpadding=4 cellspacing=0 border=1><tr>
<td><h2>Message Sent!</h2>The message should arrive with 45 seconds depending on traffic loading.<p><a href=sms.php class=active>Click here to continue</a>.</p></td></tr>
</table></center>
END;
		putFooter();
		exit;
	}
	$step=0;
}
if ($step==0) {
	if (isset($_POST['msg'])) {
	}
	$hdr['focus']="msg";
	putMaintHeader($hdr);
	$c=explode(",","30%,70%");
	$dh->Query("select * from sms.cred order by username");
	$k=0;
	$npr=3;
	$wid=floor(100/$npr);
	$nr=$dh->NumRows();
	for ($i=0;$i<$nr;$i++) {
		$r=$dh->FetchArray();
		$uname=stripslashes($r['username']);
		$name=stripslashes($r['name']);
		$uid=$r['id'];
		if ($k==0) $from.="<tr>";
		$from.="<td valign=top width=$wid%><input type=radio name=who_from value=\"$uid\"";
		if ($fid==$uid) $from.=" checked";
		$from.=">&nbsp;$uname ($name)</td>";
		$k++;
		if ($k==$npr) {
			$k=0;
			$from.="</tr>";
		}
	}
	if ($k) {
		for (;$k<$npr;$k++) {
			$from.="<td width=$wid%>&nbsp;</td>";
		}
		$from.="</tr>";
	}
	$grp="";
	$dh->Query("select * from sms.groups where owner='$owner' order by name");
	$k=0;
	$npr=4;
	$wid=floor(100/$npr);
	$nr=$dh->NumRows();
	for ($i=0;$i<$nr;$i++) {
		$r=$dh->FetchArray();
		$name=stripslashes($r['name']);
		$uid=$r['id'];
		if ($k==0) $from.="<tr>";
		$grp.="<td valign=top width=$wid%><input type=checkbox name=g_$i value=\"$name\">&nbsp;$name</td>";
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
	$to="";
	$dh->Query("select * from sms.user where owner='$owner' order by lastname");
	$k=0;
	$npr=4;
	$wid=floor(100/$npr);
	$nr=$dh->NumRows();
	for ($i=0;$i<$nr;$i++) {
		$r=$dh->FetchArray();
		$lname=stripslashes($r['lastname']);
		$fname=stripslashes($r['firstname']);
		$phone=stripslashes($r['phone']);
		if (strlen($fname)) {
			$name="$lname, $fname";
			$vname="$lname,$fname";
		} else {
			$vname=$name="$lname";
		}
		$uid=$r['id'];
		if ($k==0) $to.="<tr>";
		$to.="<td valign=top width=$wid%><input type=checkbox name=u_$i value=\"$vname\">&nbsp;$name</td>";
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
	$chksig="";
	if ($usesig) $chksig=" checked";
	print <<< END
<h1>Send SMS Message</h1><br>
<center><table width=95% cellpadding=2 cellspacing=0 border=1>
<form name=mpform action="sms.php?s=1" method=post>
<tr>
<td width=$c[0] align=right valign=top><p>Message to Send</p></td>
<td width=$c[1] valign=top><textarea name=msg rows=3 cols=57>$msg</textarea></td>
</tr>
<tr>
<td width=$c[0] align=right valign=top><p>Sign the Sent Message?</p></td>
<td width=$c[1] valign=top><input type=checkbox value=1 name=usesig$chksig>&nbsp;<font size=1>(check to append your name to the message)</font></td>
</tr>
<tr>
<td width=$c[0] align=right valign=top><p>Select Account to Send From</p></td>
<td width=$c[1] valign=top><table width=100% cellpadding=0 cellspacing=0>$from</table></td>
</tr>
<tr>
<td width=$c[0] align=right valign=top><p>Select Group to Send To</p></td>
<td width=$c[1] valign=top><table width=100% cellpadding=0 cellspacing=0>$grp</table></td>
</tr>
<tr>
<td width=$c[0] align=right valign=top><p>Select Users to Send To</p></td>
<td width=$c[1] valign=top><table width=100% cellpadding=0 cellspacing=0>$to</table></td>
</tr>
<tr>
<td width=$c[0] align=right valign=top><p>Enter Number(s) to Send To<br><font size=1>(separated by semi-colons)</font></p></td>
<td width=$c[1] valign=top><input type=text name=users value="$u" size=80></td>
</tr>
<tr>
<td width=100% align=center colspan=2><input type=submit value="Send Message Now">&nbsp;&nbsp;&nbsp;<input type=button value="Cancel" onclick=self.location="index.php"></td>
</tr>
</form>
</table></center>
END;
	putFooter();
	exit;
}
//
//	Get a page from a site
//
function webPage($url) {
	global $useragent,$cookie;
	
	$cr = curl_init($url);
	curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);       // Get returned value as string (dont put to screen)
	curl_setopt($cr, CURLOPT_USERAGENT, $useragent);      // Spoof the user-agent to be the browser that the user is on (and accessing the php $
	curl_setopt($cr, CURLOPT_COOKIEJAR, $cookie);	      // Use cookie.txt for STORING cookies
	curl_setopt($cr, CURLOPT_COOKIEFILE, $cookie);        // Use cookie.txt for STORING cookies
//	curl_setopt($cr, CURLOPT_PROXY, "http://192.168.1.95:8080)");   // use a proxy
	curl_setopt($cr, CURLOPT_TIMEOUT,30);
	$page = curl_exec($cr);
	curl_close($cr);
	return $page;
}
//
//	Post Information to a site
//
function postInfo($url,$data) {
	global $useragent,$cookie;

	$cr = curl_init($url);
	curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);       // Get returned value as string (dont put to screen)
	curl_setopt($cr, CURLOPT_USERAGENT, $useragent);      // Spoof the user-agent to be the browser that the user is on (and accessing the php $
	curl_setopt($cr, CURLOPT_COOKIEJAR, $cookie);	      // Use cookie.txt for STORING cookies
	curl_setopt($cr, CURLOPT_COOKIEFILE, $cookie);        // Use cookie.txt for STORING cookies
	curl_setopt($cr, CURLOPT_POST, true);                 // Tell curl that we are posting data
	curl_setopt($cr, CURLOPT_POSTFIELDS, $data);          // Post the data in the array above
	curl_setopt($cr, CURLOPT_TIMEOUT,30);
	curl_setopt($cr, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($cr, CURLOPT_SSL_VERIFYHOST,  false);
//	curl_setopt($cr, CURLOPT_PROXY, "http://192.168.1.95:8080)");   // use a proxy
	$output = curl_exec($cr);
	curl_close($cr);
	return $output;
}
?>
