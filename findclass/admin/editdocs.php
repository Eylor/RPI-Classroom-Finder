<?php
/*
	Back End Screen for Table docs
	Copyright 2013 by Michael Lewis.
	All Rights Reserved.

Edit history:
	Created 06/09/2013 13:08:27
*/
include("../session.php");
include("../common.php");
$utype=$_SESSION['type'];
$title="Maintain Document Library";
include_once("../cal/calendar.php");
$cal_loaded=new DHTML_Calendar('../cal/','en','calendar-win2k-2',true);
$hdr['html']=$cal_loaded->load_files();
$hdr['focus']="title";
$FOCUSFIELD="title";
$hdr['backimage']="fade.gif";
if (strpos(" am;ad;hc;ac",";")) {
	$usa=explode(";","am;ad;hc;ac");
} else {
	$usa[0]="am;ad;hc;ac";
}
if (!in_array($utype,$usa)) {
	header('location: index.php');
	exit;
}
$dh=new DB();
$cid="";
$pid=0;
$clid=0;
if ($_SESSION['cp']!="docs.php") {
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
	$dh->Exec("delete from docs where id='$pid'");
	header("location: docs.php?idx=$cid$return");
	exit;
}
$error="";
if ($step==1) {
	$title="";
	if (isset($_POST['title'])) $title=$_POST['title'];
	$filename="";
	$filename=getUploadedFile("$homedir/docs/","filename");
	$access="";
	if (isset($_POST['access'])) {
		if (count($_POST['access'])<2) {
			$access=$_POST['access'][0];
		} else {
			$access=join(';',$_POST['access']);
		}
	}
	$des="";
	if (isset($_POST['des'])) $des=$_POST['des'];
	if (isset($_POST['loaded'])) $loaded=$_POST['loaded'];
	if (isset($_POST['ok'])) $ok=$_POST['ok'];
	$title=htmlentities(addslashes($title),ENT_QUOTES);
	$filename=htmlentities(addslashes($filename),ENT_QUOTES);
	$access=htmlentities(addslashes($access),ENT_QUOTES);
	$des=htmlentities(addslashes($des),ENT_QUOTES);
	$loaded=date('Y-m-d H:i:s');
	$ok=$_SESSION['docrev'];
	if (strlen($title)==0) {
		$error.=" 'Document Title' is a required entry.";
	}
	if (strlen($filename)==0 && $pid==0) {
		$error.=" 'Name of file to load' is a required entry.";
	}	if (strlen($access)==0) {
		$error.=" 'Allow download to' is a required entry.";
	}
	if (strlen($error)==0) {
		if ($pid==0) {
			$dh->Exec("insert into docs (title,filename,access,des,loaded,ok) values ('$title','$filename','$access','$des','$loaded','$ok')");
			$pid=$dh->LastID();
		} else {
			$extra="";
			if (strlen($filename)) $extra.=",filename='$filename'";
			$dh->Exec("update docs set title='$title',access='$access',des='$des',loaded='$loaded'$extra where id='$pid'");
		}
		header("location: docs.php?idx=$cid$return");
		exit;
	}
}
$action="Add New";
if ($pid) $action="Edit";
if (strlen($error)==0) {
	if ($pid==0 && $clid==0) {
		$title="";
		$filename="";
		$access="?";
		$des="";
		$loaded="";
	} else {
		$wid=$pid;
		if ($wid==0) $wid=$clid;
		$dh->Query("select * from docs where id='$wid'");
		$r=$dh->FetchArray();
		$title=stripslashes($r['title']);
		$filename=stripslashes($r['filename']);
		$access=stripslashes($r['access']);
		$des=stripslashes($r['des']);
		$loaded=stripslashes($r['loaded']);
		$ok=stripslashes($r['ok']);
	}
} else {
	$error="<tr><td colspan=2><table width=100% border=1 cellpadding=3 callspacing=0><tr><td align=center><p><font color=red>$error</font></p></td></tr></table></td></tr>";
}
putMaintHeader($hdr);
$cw=explode(",","30%,70%");
print <<< END
</td></tr></table>
<form name=mpform action=editdocs.php?idx=$cid&s=1&id=$pid$return method=post enctype="multipart/form-data"><center><table border=0 cellpadding=8 cellspacing=0 width=1000>
<tr><td width=90%><h1>$action Downloadable Documents</h1></td>
<td width=10% align=right valign=top>
<a href=docs.php?idx=$cid$return><img src=/images/uplevel.gif border=0 title="Up one level" alt="Up one level"></a>
</td>
</tr>
$error
<tr><td colspan=2><table width=100% border=1 cellpadding=3 cellspacing=0>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Document Title</p></td><td width=$cw[1] valign=top><input type=text name=title value="$title" size=45></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Name of file to load</p></td><td width=$cw[1] valign=top><input type=file name=filename><input type=hidden name=MAX_FILE_SIZE value=8000000></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Allow download to</p></td><td width=$cw[1] valign=top>
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
	print ">".stripslashes($r['name'])."</option>\n";
}
print <<< END
</select>
</td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Description of file</p></td><td width=$cw[1] valign=top><textarea name=des rows=8 cols=51>$des</textarea></td></tr>
END;

$action="Add New Record";
if ($pid) {
	$action="Save Changes";
	print "<tr><td colspan=2 valign=top align=right><input type=checkbox name=delete_rec value=\"Y\">&nbsp; Delete this record?</td></tr>";
}
print <<< END
<tr><td colspan=2 align=center><input type=submit value="$action">&nbsp;&nbsp;&nbsp;<input type=button value="Cancel" onclick=self.location="docs.php?idx=$cid$return"></td></tr>
</table></td></tr>
</table></center></form>
END;
putMaintFooter();
//
//	Get uploaded file (if any)
//
function getUploadedFile($uploaddir,$fn) {
	if (strlen($_FILES[$fn]['name'])) {
		$filename=strtolower($_FILES[$fn]['name']);                     
		$filename=str_replace(array("[","*","?","'"," ",'"',"(",'%',"\\"),"_",$filename);
		$uploadfile=$uploaddir.$filename;
		switch($_FILES[$fn]['error']){
			case 1:
			case 2:
				echo "The file you are trying to upload is too big.";
				exit;
			break;
			case 3:
				echo "The file you are trying upload was only partially uploaded.";
				exit;
			break;
			case 4:
				echo "You must select an image for upload.";
				exit;
			break;
		}
		move_uploaded_file($_FILES[$fn]['tmp_name'], $uploadfile);
		chmod($uploadfile,0766);
		return $filename;
	}
	return "";
}
?>
