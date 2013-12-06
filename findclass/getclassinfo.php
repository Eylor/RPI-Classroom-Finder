<?php
//
//	Return names for search
//
include("session.php");
include("common.php");
if (!isset($_POST['cid'])) exit;
$cid=str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_POST['cid']);
$dh=new DB("findmyclass");
$dh1=new DB("findmyclasss");
$txt="";
$mobile=0;
$twid="100%";
if ($_SESSION['is_mobile']>0) {
        $mobile=1;
}
$dh->Query("select * from class where id='$cid'");
$r=$dh->FetchArray();
$cid=$r['id'];
$name=stripslashes($r['title']);
$desc=stripslashes($r['des']);
$crn=stripslashes($r['crn']);
$cn=stripslashes($r['course_num']);
$classtype=stripslashes($r['class_type']);
$prefix=stripslashes($r['prefix']);
$ins=stripslashes($r['instructor']);
$meets=trim(stripslashes($r['meets']));
$cnotes=stripslashes($r['notes']);
$meets=stripslashes($r['meets']);
$dh1->Query("select room_id from class2room where class_id='$cid'");
list($rid)=$dh1->FetchRow();
$dh1->Query("select building_id,name,notes from room where id='$rid'");
list($bid,$rname,$rnotes)=$dh1->FetchRow();
$rname=stripslashes($rname);
$rnotes=stripslashes($rnotes);
$dh1->Query("select name,lat,lng,grid,notes from building where id='$bid'");
list($bname,$lat,$lng,$grid,$bnotes)=$dh1->FetchRow();
$bname=stripslashes($bname);
$bnotes=stripslashes($bnotes);


print <<< END
<h3>$name</h3><p>$prefix-$cn ($classtype) $bname $rname<br>$desc</p>
END;
if (strlen($ins)>0) {
	print <<< END
<p>Instructor: $ins</p>
END;
}
if (strlen($meets)>0) {
	$m="";
	$sep="";
	$ma=array("M"=>"Monday","T"=>"Tuesday","W"=>"Wednesday","R"=>"Thursday","F"=>"Friday");
	if (strtolower($meets)=="tba") {
		$m="TBA";
	} else {
		for ($i=0;$i<strlen($meets);$i++) {
			$c=substr($meets,$i,1);
			if ($c==" ") continue;
			$m.="$sep".$ma[$c];
			$sep=", ";
		}
	}
	print <<< END
<p>Meets: $m</p>
END;
}
if (strlen($cnotes)>0) {
	print <<< END
<h4>Notes</h4>
<p>$cnotes</p>
END;
}
/*
if (strlen($bnotes)>0) {
	print <<< END
<h4>Building Notes</h4>
<p>$bnotes</p>
END;
}

if (strlen($rnotes)>0) {
	print <<< END
<h4>Room Notes</h4>
<p>$rnotes</p>
END; 
}
*/
	print <<< END
<button type="button" onclick="javascript:findThisClass($bid,$cid);">Find this class now!</button>
END;

/*	print <<< END
<a href="javascript:indoorTest($bid,$cid);">Indoor testing!</a>
END;
*/
