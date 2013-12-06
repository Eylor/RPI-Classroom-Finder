<?php
//
//	Misc helper routines
//
include_once("localsettings.php");
include_once("new-dbc.php");
//
function isLoggedIn($level) {
	if (!isset($_SESSION['valid']) || $_SESSION['valid']==0) {
		header("Location: /moblogin.php?goto=".$_SERVER['PHP_SELF']);
		exit;
	}
	if ($level=="?") return;
	if (strpos(" $level",$_SESSION['usertype'])==0) {
		$hdr['title']="Invalid access";
		putHeader($hdr);
		print "<center><h3>You do not have access privileges to this function.</h3></center>";
		putFooter();
		exit;
	}
	return;
}
function isAllowed($pr) {
	isLoggedIn($pr);
	return;
}
function canUse($pr) {
	$p[0]=$_SESSION['tools'];
	if (strpos($p[0],";")!=false) {
		$p=explode(';',$p[0]);
	}
	foreach($p as $pn) {
		if (strtolower($pr)==strtolower($pn)) {
			return 1;
		}
	}
	return 0;
}
function validemail($addr) {
        if (strlen($addr)>0) {
                return preg_match_all('/
                        ^
                        [^@\s]+
                        @
                        (
                                [-a-z0-9]+
                                \.
                        )+
                        (
                                [a-z]{2}
                                |com|net
                                |edu|org
                                |gov|mil
                                |int|biz
                                |pro
                                |info|arpa
                                |aero|coop
                                |name
                                |museum
                                |xxx
                        )
                        $
                        /ix',$addr,$matches);
        }
        return 0;
}
function lookup($dir,$fn) {
	$d=opendir($dir.'/') or die("Cannot open the directory");
	while (false != ($f=readdir($d))) {
		if ($f==$fn) {
			closedir($d);
			return 1;
		}
	}
	return 0;
}
function statepick($name,$value) {
	$s=explode(',',"AK,AL,AR,AS,AZ,CA,CO,CT,DC,DE,FL,GA,GU,HI,IA,ID,IL,IN,KS,KY,LA,MA,MD,ME,MH,MI,MN,MO,MP,MS,MT,NC,ND,NE,NH,NJ,NM,NV,NY,OH,OK,OR,PA,PR,PW,RI,SC,SD,TN,TX,UT,VA,VI,VT,WA,WI,WV,WY");
	$t='<select id='.$name.'  name="'.$name.'">';
	for ($i=0,$j=count($s);$i<$j;$i++) {
		$t.='<option value="'.$s[$i].'"';
		if ($value==$s[$i]) { $t.=" selected"; }
		$t.='>'.$s[$i].'</option>';
	}
	$t.="</select>\n";
	return $t;
}
function datepick($name,$dt,$days,$base,$last,$noyear) {
	$m=explode(',',"January,February,March,April,May,June,July,August,September,October,November,December");
	if ($noyear==0) {
		$d=explode('-',$dt);
	}
	else {
		list($d[1],$d[2])=explode('-',$dt);
		$days=3;
	}
	$t="";
	if ($days>1) {
		$t.='<select name="'.$name.'_mon">';
		for ($i=1;$i<13;$i++) {
			$ii=$i;
			if ($i<10) { $ii="0$i"; }
			$t.='<option value="'.$ii.'"';
			if (isset($d[1])) {
				if ($d[1]==$i) { $t.=" selected"; }
			}
			$t.='>'.$m[$i-1].'</option>';
		}
		$t.='</select>&nbsp;&nbsp;';
	}
	else {
		$t.='<input type="hidden" name="'.$name.'_mon" value="0">';
	}
	if ($days>2) {
		$t.='<select name="'.$name.'_day">';
		for ($i=1;$i<32;$i++) {
			$ii=$i;
			if ($i<10) { $ii="0$i"; }
			$t.='<option value="'.$ii.'"';
			if (isset($d[2])) {
				if ($d[2]==$i) { $t.=" selected"; }
			}
			$t.='>'.$i.'</option>';
		}
	}
	else {
		$t.='<input type="hidden" name="'.$name.'_day" value="1">';
	}
	$t.='</select>&nbsp;&nbsp;';
	if ($noyear==0) {
		$t.='<select name="'.$name.'_year">';
		if ((0+$d[0])==0 || strlen($d[0])==0) {
			$t.='<option value="0000" selected>Never</option>';
		}
		for ($i=$base;$i<=$last;$i++) {
			$t.='<option value="'.$i.'"';
			if ($d[0]==$i) { $t.=' selected'; }
			$t.='>'.$i.'</option>';
		}
		$t.="</select>";
	}
	return $t;
}
function newTimepick($name,$dt,$size=1,$st="0:00",$et="24:00") {
	$d=explode(":",$dt);
	list($sh,$sm)=explode(":",$st);
	list($eh,$em)=explode(":",$et);
	$sh=0+$sh;
	$eh=0+$eh;
	$sm=0+$sm;
	$em=0+$em;
	$t="<select id=$name name=\"$name\"_time size=$size>";
	for ($i=$sh;$i<$eh;$i++) {
		for ($j=0;$j<60;$j=$j+=15) {
			$sel="";
			$ml=floor($j/15)*15;
			$mh=ceil(($j+15)/15)*15;
			if (0+$d[0]==$i && 0+$d[1]>=$ml && 0+$d[1]<$mh) $sel=" selected";
			$ampm="am";
			$hr=$i;
			if ($i>=12) {
				$ampm="pm";
				if ($i>12) $hr=$i-12;
			} else {
				if ($i==0) {
					$hr=12;
				}
			}
			$t.="<option value=".sprintf("%02d:%02d",$i,$j)."$sel>$hr:".sprintf("%02d",$j)."$ampm</option>";
		}
	}
	$t.="</select>";
	return $t;		
}
function timepick($name,$dt) {
	$m=explode(',',":00,:15,:30,:45,:59");
	$mv=explode(',',"00,15,30,45,59");
	$d=explode(':',$dt);
	$t="";
	$t.='<select name="'.$name.'_hour">';
	for ($i=0;$i<24;$i++) {
		$t.='<option value="'.sprintf("%02d",$i).'"';
		if (isset($d[0])) {
			if ($d[0]==$i) $t.=" selected";
		}
		$t.='>'.$i.'</option>';
	}
	$t.='</select>&nbsp;&nbsp;';
	$t.='<select name="'.$name.'_min">';
	if (!isset($d[1])) $d[1]="00";
	for ($i=0,$j=count($m);$i<$j;$i++) {
		$t.='<option value="'.$mv[$i].'"';
		if ($mv[$i]=="59") {
			if ($d[1]==$mv[$i]) $t.=" selected";
		} else {
			$x=0+$mv[$i];
			$y=15+$mv[$i];
			if ($x != 59 && $x<=(0+$d[1]) && $y>=(0+$d[1])) {
				$t.=" selected";
			}
		}
		$t.='>'.$m[$i].'</option>';
	}
	$t.='</select>&nbsp;&nbsp;';
	return $t;
}
//
//	Sort a multidimensional array by a column
//
function colSort($a,$col,$flags){
	for($i=0,$j=count($a);$i<$j;$i++){
		$sortarr[]=$a[$i][$col];
	}
    array_multisort($sortarr,$flags,$a,$flags);
	return($a);
}
//
//	Display a current error box and wait for input
//
function alert($msg,$action) {
	$error="";
	if (substr($msg,0,2)=="E:") {
		$error="Error: ";
		$msg=substr($msg,2);
	}
	print <<< END
<center><table width=340 border=1 cellpadding=5 cellspacing=0><tr><td><p><font size=4>$erro</font>$msg</p></td></tr></table><br>
$action
END;
}
//
//	Display a header for the screen
//
function putHeader($hdr) {
	global $homedir,$schoolname,$homewebsite;
	include("$homedir/mobhdr.php");
}
//
//	Display a pop-up header for the screen
//
function putPopUpHeader($hdr) {
	global $homedir,$schoolname,$homewebsite;
	include("$homedir/popupheader.php");
}
//
//	Display a header for maintenance programs
//
function putMaintHeader($hdr) {
	global $homedir;	
	include("$homedir/admin/maint_header.php");
}
//
//	Display a footer for maintenance programs
//
function putMaintFooter() {
	global $homedir,$copyright;
	include("$homedir/admin/maint_footer.php");
}
//
//	Display a footer for main programs
//
function putFooter() {
	global $homedir,$copyright;
///	include("$homedir/footer.php");
}
//
//      get the error html for forms        
//
function getErrorHTML($error) {
        $errorstr="";
        if (count($error)) {
                $errorstr="<center><table width=100% border=1 cellpadding=4 cellspacing=0><tr><td bgcolor=\"#ffe0e0\"><p><b>The following must be corrected before you can continue:</b><ul>";
                for ($i=0,$j=count($error);$i<$j;$i++) {
                        $errorstr.="<li>".$error[$i]."</li>";
                }
                $errorstr.="</ul></td></tr></table></center>";          
        }
        return $errorstr;
}
//
//	Format MYSQL Date into human
//
function fmtDateTime($d,$f="ytd") {
	$f=strtolower($f);
	if (strlen($d)==0 || substr($d,0,10)=="0000-00-00") return "";
	$t="";
	$sec=0;
	$year=0;
	$time=0;
	$date=0;
	if (strpos(" ".$f,"s")) $sec=1;
	if (strpos(" ".$f,"y")) $year=1;
	if (strpos(" ".$f,"d")) $date=1;
	if (strpos(" ".$f,"t")) $time=1;
	if ($date) {
		$t=substr($d,5,2)."/".substr($d,8,2);
		if ($year) $t.="/".substr($d,0,4);
		if ($time) $t.=" ";
	}
	if ($time) {
		$hr=0+substr($d,11,2);
		$ampm="am";
		if ($hr>11) $ampm="pm";
		if ($hr>12) {
			$hr-=12;
			$hr=sprintf("%02d",$hr);
		}
		$t.=sprintf("%02d:",$hr).substr($d,14,2);
		if ($sec) {
			$t.=":".substr($d,17,2);
		} else {
			$t.=$ampm;
		}
	}
	return $t;
}
function sanitize($t) {
	str_replace(array("'",'"',';'),"",$t);
	return $t;
}
//
//	Convert normal time to military
//
function normal2Military($time) {
	list($hr,$x)=explode(":",$time,2);
	$hr=0+$hr;
	list($mn,$suf)=explode(" ",$x,2);
	$sub=strtolower($suf);
	if ($hr==12 && $suf=="am") $hr=0;
	if ($suf=="pm" && $hr<12) $hr+=12;
	return sprintf("%02d",$hr).":$mn";
}
//
//	Convert military time to normal
//
function military2Normal($time) {
	list($hr,$mn)=explode(":",$time,2);
	$hr=0+$hr;
	if ($hr==0) {
		$suf="am";
		$hr=12;
	} else {
		if ($hr<12) {
			$suf="am";
		} else {
			$suf="pm";
			if ($hr>12) $hr-=12;
		}
	}
	return sprintf("%02d",$hr).":$mn $suf";
}
//
//	Encrypt a string
//
function encStr($s1) {
	$ns="";
	$key=85;
	$now=date("Y-m-d H:i:s");
        $pad=sha1("SBu$s1").sha1($now).md5("hoW$s1").substr(md5($now),0,14);
	for ($i=0;$i<strlen($s1);$i++) {
		$x=ord(substr($s1,$i,1))^$key;
		$key=$key^255;
		$hc=dechex($x);
		if (strlen($hc)==1) $hc="0$hc";
		$ns.=$hc;
	}
	$len=dechex(strlen($ns)+11);
	if ($len>126) $len=126;
	if (strlen($len)==1) $len="0$len";
	return substr($pad,0,126-strlen($ns)).$ns.$len;
}
//
//	decrypt a string
//
function decStr($s1) {
	$ns="";
	if ($s1=="") return $ns;
	$key=85;
	$len=hexdec(substr($s1,-2,2))-11;
	if ($len<0) $len==0;
	$s1=substr($s1,-$len-2,$len);
	for ($i=0;$i<strlen($s1)/2;$i++) {
		$x=hexdec(substr($s1,$i*2,2))^$key;
		$key=$key^255;
		if ($x<32) break;
		$c=chr($x);
		$ns.=$c;
	}
	return $ns;
}
//
//	Convert time to days, hours, minutes, secs
//
function cvtTime($t) {
	$txt="";
	$days=floor($t/86400);
	$t-=$days*86400;
	$hours=floor($t/3600);
	$t-=$hours*3600;
	$mins=floor($t/60);
	$t-=$mins*60;
	if ($days) {
		$txt="$days day";
		if ($days!=1) $txt.="s";
		$txt.=", ";
	}
	if ($hours) {
		$txt.="$hours hr";
		if ($hours!=1) $txt.="s";
		$txt.=", ";
	}
	if ($mins) {
		$txt.="$mins min";
		if ($mins!=1) $txt.="s";
		$txt.=", ";
	}
	$txt.="$t sec";
	if ($t!=1) $txt.="s";
	return $txt;
}
