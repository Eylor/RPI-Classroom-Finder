<?php
//
//	Unfreeze Users
//
include("../session.php");
$hdr['title']="Reactivate Accounts";
include("../common.php");
isLoggedIn("am;dv");
$step=0;
$dh=new DB();
if (isset($_GET['s'])) {
	$step=$_GET['s'];
	$dh->Exec("update person set ta='0',ba='0' where id='$step'");
	header("location: index.php");
	exit;
}
//
//	Beginning of program
//
	$tt=time();
	$dh->Query("select id,email from person where ta>='$tt' order by email");
	$numrows=$dh->NumRows();
	$bgcolor="#00c0ff";
	list($c1,$c2)=explode(',',"60%,40%");
	putMaintHeader($hdr);
	print <<< END
<center>
<p align="center"><font size="6"><strong>Activate Frozen Account</strong></font></p><br>
<tr><td align=center><table width=500 cellpadding=0><tr><td width=30% colspan=2>
<p>If the user you are trying to activate does not appear in this table that simply means that their "frozen" period has already
expired and they are free to attempt another login.</p><br></td></tr>
END;
	flush();
	$toggle=0;
	for ($i=0;$i<$numrows;$i++) {
		$r=$dh->FetchArray();
		$sn[$i]=$r['email'];
		$id[$i]=$r['id'];
	}
	print <<< END
<tr><td width=$c1 valign=top bgcolor="$bgcolor"><p>&nbsp;Email</p></td>
<td align=center valign=top bgcolor="$bgcolor" width=$c2><p>Action</p></td>
</tr>
END;
	$toggle=0;
	for ($i=0,$j=count($sn);$i<$j;$i++) {
		$ls=$sn[$i];
		$uid=$id[$i];
		$bg="";
        	if ($toggle & 1) { $bg='#f5ede2'; }
		$toggle++;
		print <<< END
<tr><td align=left valign=top bgcolor="$bg" width=$c1><p>&nbsp;$ls</p></td>
<td align=center valign=top bgcolor="$bg" width=$c2><p><a href=unfreeze.php?s=$uid>Activate Account</a></p></td>
</tr>
END;
	}
print <<< END
</table></td></tr></table>
<p align=center><a href="index.php">Return to Main Menu</a></p></center>
END;
putMaintFooter();
?>
