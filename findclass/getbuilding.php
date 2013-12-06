<?php
//
//	Return names for search
//
include("session.php");
include("common.php");
$dh=new DB("findmyclass");
$dh1=new DB("findmyclass");
$txt="";
$mobile=0;
$twid="900px";
if ($_SESSION['is_mobile']>0) {
        $mobile=1;
        $twid="100%";
}
$dh->Query("select * from building order by name");
if ($dh->NumRows()) {
        $txt=<<< END
<div style="width:$twid">
<ul data-role="listview" data-inset="true" data-theme="d" data-divider-theme="b">
<li data-role="list-divider">Select Building</li>
END;
	for ($i=0;$i<$dh->NumRows();$i++) {
		$r=$dh->FetchArray();
		$cid=$r['id'];
		$name=stripslashes($r['name']);
		$img=stripslashes($r['img']);
		$hname=stripslashes(trim($r['human_name']));
		$notes=stripslashes($r['notes']);
		if (strlen($hname)) $name="$hname ($name)";
		if ($img=="") {
			$img="/images/noimage.png";
		} else {
			list($x,$y)=explode(".",$img,2);
			$iname=substr($x,0,-1).".".$y;
		}
		$txt.=<<< END
<li><a style="text-decoration: none;" href="javascript:findThisClass($cid,000);" title="Tap or Click to display directions"><img src="$img"><h4>$name</h4><p>$notes</p></a></li>
END;
	}
	$txt.=<<<END
</ul></div>
<script>
$('img.ui-li-thumb').click(function(e){
	e.stopPropagation();
	alert(this.src);
	displayImage(this,this.src);
});
</script>
END;
}
print $txt;
