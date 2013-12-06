<?php
/*
	Back End Screen for Table building
	Copyright 2013 by Michael Lewis.
	All Rights Reserved.

Edit history:
	Created 06/12/2013 13:23:38
*/
include("../session.php");
include("../common.php");
$utype=$_SESSION['type'];
$title="Maintain Building Information";
include_once("makethumb.php");
$hdr['focus']="human_name";
$hdr['html']=<<< END
<script>
function setCursor(e) {
	e.style.cursor="pointer";
}
function clearCursor(e) {
	e.style.cursor="default";
}
function gotLocation(position) {
	lat = position.coords.latitude;
	lng = position.coords.longitude;
	e=document.getElementById("latFld");
	e.value=lat;
	e=document.getElementById("lngFld");
	e.value=lng;
}
function getLocation() {
	navigator.geolocation.getCurrentPosition(gotLocation, noLocation);
}
function noLocation() {
	alert("Error: Could not get your present position");
	lat=-1.0;
	lng=0.0;
}
</script>
END;
$FOCUSFIELD="human_name";
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
if ($_SESSION['cp']!="building.php") {
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
	$dh->Exec("delete from building where id='$pid'");
	header("location: building.php?idx=$cid$return");
	exit;
}
$error="";
if ($step==1) {
	$human_name="";
	if (isset($_POST['human_name'])) $human_name=$_POST['human_name'];
	$name="";
	if (isset($_POST['name'])) $name=$_POST['name'];
	$notes="";
	if (isset($_POST['notes'])) $notes=$_POST['notes'];
	$lat="";
	if (isset($_POST['lat'])) $lat=$_POST['lat'];
	$lng="";
	if (isset($_POST['lng'])) $lng=$_POST['lng'];
	$grid="";
	if (isset($_POST['grid'])) $grid=$_POST['grid'];
	$img="";
	$img=getUploadedFile("$homedir/gallery/","img");
	if (strlen($img)) {
		$size=getimagesize("$homedir/gallery/$img");
		if ($size[0]>120) {
			$h=floor(120/$size[0]*$size[1]);
			makeThumb("$homedir/gallery/$img",120,$h,30);
			if ($size[0]>600) {
				$h=floor(600/$size[0]*$size[1]);
				makeThumb("$homedir/gallery/$img",600,$h,100,"");
			}
		}
	}
	$human_name=htmlentities(addslashes($human_name),ENT_QUOTES);
	$name=htmlentities(addslashes($name),ENT_QUOTES);
	$notes=htmlentities(addslashes($notes),ENT_QUOTES);
	$lat=htmlentities(addslashes($lat),ENT_QUOTES);
	$lng=htmlentities(addslashes($lng),ENT_QUOTES);
	$grid=htmlentities(addslashes($grid),ENT_QUOTES);
	if ($lat=="") $lat=0.0;
        if ($lng=="") $lng=0.0;
        if ($grid=="") $grid=0.0;
	$img=htmlentities(addslashes($img),ENT_QUOTES);
	if (strlen($human_name)==0) {
		$error.=" 'Building Name' is a required entry.";
	}
	if (strlen($name)==0) {
		$error.=" 'Shortened Name' is a required entry.";
	}
	if (strlen($error)==0) {
		if ($pid==0) {
			$dh->Exec("insert into building (human_name,name,notes,lat,lng,grid,img) values ('$human_name','$name','$notes','$lat','$lng','$grid','$img')");
			$pid=$dh->LastID();
		} else {
			$extra="";
			if (strlen($img)) $extra.=",img='$img'";
			$dh->Exec("update building set human_name='$human_name',name='$name',notes='$notes',lat='$lat',lng='$lng',grid='$grid'$extra where id='$pid'");
		}
		header("location: building.php?idx=$cid$return");
		exit;
	}
}
$action="Add New";
if ($pid) $action="Edit";
if (strlen($error)==0) {
	if ($pid==0 && $clid==0) {
		$human_name="";
		$name="";
		$notes="";
		$lat="";
		$lng="";
		$grid="";
		$img="";
	} else {
		$wid=$pid;
		if ($wid==0) $wid=$clid;
		$dh->Query("select * from building where id='$wid'");
		$r=$dh->FetchArray();
		$human_name=stripslashes($r['human_name']);
		$name=stripslashes($r['name']);
		$notes=stripslashes($r['notes']);
		$lat=stripslashes($r['lat']);
		$lng=stripslashes($r['lng']);
		$grid=stripslashes($r['grid']);
		$img=stripslashes($r['img']);
	}
} else {
	$error="<tr><td colspan=2><table width=100% border=1 cellpadding=3 callspacing=0><tr><td align=center><p><font color=red>$error</font></p></td></tr></table></td></tr>";
}
putMaintHeader($hdr);
$cw=explode(",","30%,70%");
print <<< END
</td></tr></table>
<form name=mpform action=editbuilding.php?idx=$cid&s=1&id=$pid$return method=post enctype="multipart/form-data"><center><table border=0 cellpadding=8 cellspacing=0 width=1000>
<tr><td width=90%><h1>$action Buildings</h1></td>
<td width=10% align=right valign=top>
<a href=building.php?idx=$cid$return><img src=/images/uplevel.gif border=0 title="Up one level" alt="Up one level"></a>
</td>
</tr>
$error
<tr><td colspan=2><table width=100% border=1 cellpadding=3 cellspacing=0>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Building Name</p></td><td width=$cw[1] valign=top><input type=text name=human_name value="$human_name" size=64></td></tr>
<tr><td width=$cw[0] align=right valign=top><p><font color=red>*&nbsp;</font>Shortened Name</p></td><td width=$cw[1] valign=top><input type=text name=name value="$name" size=64></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Notes</p></td><td width=$cw[1] valign=top><textarea name=notes rows=6 cols=72>$notes</textarea></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Lat</p></td><td width=$cw[1] valign=top><input type=text name=lat id="latFld" value="$lat" size=15><img src="/images/globe.png" style="float:right" onclick="getLocation();" onmouseover="setCursor(this);" onmouseout="clearCursor(this);" title="Click to Set LAT/LNG to current location"></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Lng</p></td><td width=$cw[1] valign=top><input type=text name=lng id="lngFld" value="$lng" size=15></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Grid Position</p></td><td width=$cw[1] valign=top><input type=text name=grid value="$grid" size=15></td></tr>
<tr><td width=$cw[0] align=right valign=top><p>Building Image</p></td><td width=$cw[1] valign=top><input type=file name=img><input type=hidden name=MAX_FILE_SIZE value=8000000></td></tr>
END;

$action="Add New Record";
if ($pid) {
	$action="Save Changes";
	print "<tr><td colspan=2 valign=top align=right><input type=checkbox name=delete_rec value=\"Y\">&nbsp; Delete this record?</td></tr>";
}
print <<< END
<tr><td colspan=2 align=center><input type=submit value="$action">&nbsp;&nbsp;&nbsp;<input type=button value="Cancel" onclick=self.location="building.php?idx=$cid$return"></td></tr>
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
		return "/gallery/$filename";
	}
	return "";
}
?>
