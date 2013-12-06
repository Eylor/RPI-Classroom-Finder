<?php
//
//	Main page for Find My Class 4.me
include("session.php");
include("common.php");
include("mobhdr.php");
include("mobile.php");
$SESSID=session_id();
$twidth=$mainwidth="100%";
if (!isset($_SESSION['is_mobile'])) {
	$_SESSION['is_mobile']=1;
	$md=new Mobile_Detect();
	if (!$md->isMobile()) {
		$_SESSION['is_mobile']=0;
	}
}
if ($_SESSION['is_mobile']==0) {
	$twidth="900";
	$mainwidth=' style="width:900px"';
}
$dh=new DB("findmyclass");
if (isset($_SERVER['HTTP_REFERER']) && strlen($_SERVER['HTTP_REFERER']) && stripos(" ".$_SERVER['HTTP_REFERER'],"https://www.pokerleague4.us")==0 && $_SERVER['HTTP_REFERER']!="www.pokerleague4.us") {
        $dom=addslashes($_SERVER['HTTP_REFERER']);
        $now=date("Y-m-d H:i:s");
        $dh->Exec("insert into sm_ref (domain,ref_when) values('$dom','$now')");
}
$icon=$tools="";
$ver="0.1.0a";
$yr=date("Y");
if ($yr != "2013") $yr="2013-$yr";
$uid="0";
$tddis=$logged=$addis="none";
$adlink=$nojoin=$notlogged="";
$signin=$welcome=<<< END
<span id="welcome" style="font-size:12px;border:1px solid black;padding:3px"><a href="#login" style="text-decoration:none"><b>Log In</a></b>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#signup" style="text-decoration:none"><b>Sign Up</a></b></span>
END;
if (isset($_SESSION['valid']) && $_SESSION['valid']>0) {
	$uid=$_SESSION['valid'];
	$logged="";
	$notlogged="none";
	if ($_SESSION['usertype']=="am" || $_SESSION['usertype']=="de") {
		$addis="";
		$nojoin="none";
		$adlink="index.php";
	}
	$h=$_SESSION['handle'];
	$welcome=<<< END
<span id="welcome" style="font-size:12px;border:1px solid black;padding:3px">Welcome <b>$h</b>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:logoffUser();" style="text-decoration:none"><b>Logout</a></b></span>
END;
}
$cnt=0;
$hdr=getMobileHeader();
if ($_SESSION['is_mobile']!=1) {
	$writeup=<<< END
<table width=$twidth cellpadding=2 cellspacing=2>
<tr><td width=152 valign=top><br><img src="images/signpost.png"></td>
<td valign=top><p>Welcome to the <i>Find My Class</i> app. This app is designed to quickly help you locate and navigate to any class on campus. To realize the full benefits of the app you should
sign-up for an account but it is not required.</p>
<p>Please check out the FAQs and the Help information in the <i>General</i> Menu below for any information you may need. Please don't hesitate to contact us by email should you have any questions 
or suggestions.</p>
</td></tr></table>
END;
} else {
	$writeup=<<< END
<table width=100% cellpadding=1 cellspacing=1><tr><td width=70 valign=top><img style="float:left;padding-right:4px;margin-top:16px" src="images/signpostm.png"></td>
<td valign=top><p style="font-size:11px"><p>Welcome to the <i>Find My Class</i> app. This app is designed to quickly help you locate and navigate to any class on campus.</p></td></tr></table>
END;
}
print <<< END
$hdr
</head>
<body>
<noscript>
<h2>This app requires javascript enabled. Please enable it in your browser.</h2>
<style>
div {display:none}
</style>
</noscript>
<style>
.modal {
    display:    none;
    position:   fixed;
    z-index:    1000;
    top:        0;
    left:       0;
    height:     100%;
    width:      100%;
    background: rgba( 255, 255, 255, .8 ) 
                url('images/spinner.gif') 
                50% 50% 
                no-repeat;
}
body.loading {
    overflow: hidden;   
}
body.loading .modal {
    display: block;
}
</style>
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css" />
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>
<script type="text/javascript">
var gotoPage="";
var nameStr="";
var nameTimeout=0;
var respText="";
var dataType;
var dataId;
var fullSite=0;
var pageVars = {}
var userId="$uid";
var SESSID='$SESSID';
var startTO;
var startThis;
var event=0;
var tokenSSKey;
var lat=0.0;
var lng=0.0;
var olat=0.0;
var olng=0.0;
var oID=0;
var mostRecent="";
var MAP;
var mapLat=0;
var mapLng=0;
var mapSport="find";
var initLat=0;
var initLng=0;
var curLat=0;
var curLng=0;
var dirID=0;
///
// insert mapping interface software here
///
function findThisClass(cid){
	alert("FINDING CLASS ");
	$.post('routeToClass.php','bid=' + cid);
	$.ajax({
	    type: 'POST',
	    url: 'routeToClass.php',
	    success: function(result) {
	        var entArr = jQuery.parseJSON(result);
		alert(entArr);
	    }
	});
	$.mobile.changePage("#showDirections");
}
function showClassDesc(cid) {
	$.post('getclassinfo.php','cid=' + cid,function(data) {
		$('#classInfo').html(data).trigger("create");
	});
	$.mobile.changePage("#showClassInfo");
}
function getClassNameResults() {
	if (nameStr==$('#className').val()) return;
	nameStr=$('#className').val();
	if (trim(nameStr)=="") return;
	$.post('findclass.php','tn=' + escape(nameStr),function(data) {
		$('#findClassList').html(data).trigger("create");
	});
	nameTimeout=setTimeout("getClassNameResults()",500);
}
function editDirInfo(did) {
	dirID=did;
	$.mobile.changePage("#editDirections");
}
function handleGetAlert(data) {
	$("#alertResults").html(data).trigger("create");
}
function signup4Alerts(sid,spid) {
	$.mobile.changePage("#alertSignup");
}
function handleDirData(data) {
	$('#walking-dir').html(data).trigger('create');
}
function handleUserDirData(data) {
	$('#dirList').html(data).trigger('create');
}
function errFunc() {
	alert("error on processing directions");
}
function findLocation(what,xlat,xlng,oid,spid) {
	olat=xlat;
	olng=xlng;
	oID=oid;
	spID=spid;
	navigator.geolocation.getCurrentPosition(foundLocation, noLocation);
	$('#walking-dir').html('');
}
function gotLocation(position) {
	lat = position.coords.latitude;
	lng = position.coords.longitude;
	
}
function getLocation() {
	navigator.geolocation.getCurrentPosition(gotLocation, noLocation);
}
function foundLocation(position) {
	lat = position.coords.latitude;
	lng = position.coords.longitude;
	$.mobile.changePage('#showDirections');
}
function noLocation() {
	alert("Error: Could not get your present position");
	lat=-1.0;
	lng=0.0;
}
function handleNoReturn(data) {
}
function scrWidth() {
	nn=(document.layers ? true : false);
	return (nn ? innerWidth : screen.availWidth);
}
function scrHeight() {
	nn=(document.layers ? true : false);
	return (nn ? innerHeight : screen.availHeight);
}
function popUp(URL) {
	day = new Date();
	id = day.getTime();
	w=1050;
	h=750;
	l=(scrWidth()-w-30)/2;
	t=(scrHeight()-h)/2-18;
	eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=' + w + ',height=' + h + ',left = ' + l + ',top =' + t)");
}
function setCursor(e) {
	e.style.cursor="pointer";
}
function clearCursor(e) {
	e.style.cursor="default";
}
function putError(t,msg) {
	initMsg(t + "ErrMsg");
	$("#" + t + "ErrMsg").message({type:"error", message:msg,theme: "f",dismiss:false});
	$("#" + t + "ErrMsg").message('show');
}
function putMsg(t,msg) {
	initMsg(t + "ErrMsg");
	$("#" + t + "ErrMsg").message({type:"info", message:msg,theme: "g",dismiss:false});
	$("#" + t + "ErrMsg").message('show');
}
function showSpinner() {
        $(startThis).addClass("loading"); 
}
$("body").on({
    ajaxStart: function() { 
    	startThis=this;
	startTO = setTimeout("showSpinner()", 200);
    },
    ajaxStop: function() { 
	clearTimeout(startTO);
        $(this).removeClass("loading"); 
    }    
});
function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}
function handleSummaryData(data) {
	respText=unescape(data);
	$('#' + dataType.toLowerCase() + 'SummaryText').html(data);
	$.mobile.changePage('#show' + dataType + 'Summary',{changeHash: false});
}
function errFunc(type) {
	alert("Error: Could not fetch " + type + " Data");
}
function getSummary(srchId,type) {
	dataType=type;
	dataId=srchId;
	$.get('getsummary.php','seid=' + SESSID + '&sid=' + srchId + '&type=' + type,handleSummaryData).error(errFunc);
}
$(document).bind("pagebeforechange", function (event, data) {
	if (typeof data.toPage == 'object' && data.toPage.attr('data-needs-auth') == 'true') {
		if (!sessionStorage.getItem("TokenSSKey")) {
			pageVars.returnAfterLogin = data;
			event.preventDefault();
			$.mobile.changePage("#login",{changeHash:false});
        	}
	}
});
function handleCOData(data) {
	$('#payOptions').html(data).trigger('create');
	$.mobile.changePage("#payForIt");
}
function delDirInfo(did) {
	$.post('deldirinfo.php','did=' + did,function(data) {
		history.back();
	});
}
function logoffUser() {
	if (userId!=0) {
		userId=0;
		$.get('logoff.php','seid=' + SESSID,function(data){});
	}
	sessionStorage.removeItem("TokenSSKey");
	$('.notLogged').show();
	$('.logged').hide();
	$('#welcomeBlock').html('$signin');
	$.mobile.changePage("#home");
}
function initMsg(msg) {
	tmp=$("#"+msg).html();
	if (tmp==null) tmp="";
	if (tmp.length>0) $("#"+msg).message('destroy');
}
function handleMyaData(data) {
	$("#myaOldPW").val("");
	$("#myaNewPW").val("");
	$("#myaReNewPW").val("");
	ca=data.split("|");
	$('#mEmailAddr').val(ca[0]);
	$('#mFirst').val(ca[1]);
	$('#mLast').val(ca[2]);
	$('#mCity').val(ca[3]);
	$('#mState').val(ca[4]);
	$('#mZip').val(ca[5]);
	$('#mCell').val(ca[6]);
	$.mobile.changePage("#myAccount");
}
function loadMyAccount() {
	$.post("getqa.php",'uid=' + userId,handleMyaData);
}
function displayImage(e,iname) {
//	e.preventDefault();
	if (iname=="/images/noimage.png") return;
	popUp(iname);
}
//
//	On ready init code
//
$(document).ready(function() {
	$("#className").keypress(function() {
		if (nameTimeout!=0) clearTimeout(nameTimeout);
		nameTimeout=setTimeout("getClassNameResults()",500);
	});
	$('#findABuilding').live('pagebeforeshow', function(){
		$.post('getbuilding.php','tn=' + escape(nameStr),function(data) {
			$('#findBuildingList').html(data).trigger("create");
		});
	});
	$('#doDirections').live('pagebeforeshow', function(){
		$.post('fetchdir.php','sid=' + dirSchoolID + '&spid=' + sportID + '&new=y',function(data) {
			if (data.indexOf('~')!=-1) {
				var ca=data.split('~');
				initLat=ca[0];
				initLng=ca[1];
				mapSport=ca[2];
				$('#directionResults').html(ca[3]).trigger('create');
///@@@				load_map('map',$('#wizMap'));
			} else {
				$('#directionResults').html(data).trigger('create');
			}
		});
	});
	$('#editDirections').live('pagebeforeshow', function(){
		$.post('fetchdir.php','did=' + dirID + '&spid=' + sportID + '&new=n',function(data) {
			if (data.indexOf('~')!=-1) {
				var ca=data.split('~');
				initLat=ca[0];
				initLng=ca[1];
				mapSport=ca[2];
///@@@				$('#editDirectionResults').html(ca[3]).trigger('create');
				if (initLat!=0 || initLng!=0) load_map('map',$('#editMap'));
			} else {
				$('#editDirectionResults').html(data).trigger('create');
			}
		});
	});
	$('#signup').live('pagebeforeshow', function(){
		$('#sHandle').val("");
		$('#sPassword').val("");
		$('#sEmailAddr').val("");
		$('#sRePassword').val("");
		$('#sFirst').val("");
		$('#sLast').val("");
		$('#sState').val('PA');
		$('#sCity').val('Stroudsburg');
		$('#sZip').val('18360');
		$('#sCell').val("");
	});
	$('#showUserDirections').live('pagebeforeshow', function(){
		$.post('getdir.php','lat=' + lat + '&lng=' + lng + '&olat=' + olat + '&olng=' + olng + '&oid=' + oID + '&spid=' + spID + '&nouser=1',handleUserDirData).error(errFunc);
	});
	$("#logErrMsg").message({type:"error",dismiss:false,theme:"f"});
	$("#logErrMsg").message("hide");
	$('form').submit(function(event) {
		event.preventDefault();
		var thisForm=$(this);
		var formUrl=thisForm.attr('action');
		var formName=thisForm.attr('name');
		var dataToSend=thisForm.serialize();
		var callBack=function(dataReceived) {
			switch(formName) {
				case "signupForm":
					if (dataReceived=="black") {
						putError("signup","<p>You have been banned from the site.</p>");
						return false;
					}
					if (dataReceived=="already") {
						putError("signup","<p>A user with that email address is already on file.</p>");
						return false;
					}
					if (dataReceived.substr(0,5)=="wrong") {
						putError("signup","<p>The answer to the question was incorrect.</p>");
						return false;
					}
					if (dataReceived=="weak") {
						putError("signup","<p>Password needs to have at least 1 numeric digit or special character.</p>");
						return false;
					}
					if (dataReceived=="badchar") {
						putError("signup","<p>Password cannot contain non-printing or punctuation characters.</p>");
						return false;
					}
					userId=parseInt(dataReceived);
					if (userId==0) {
						putError("signup","<p>An unexpected error occurred. Please try again.</p>");
						return false;
					}
					$('#signupErrMsg').message("hide");
					$('.notLogged').hide();
					$('.logged').show();
					$('.userIsTd').hide();
					$('.userIsAdmin').hide();
					$('.noJoin').show();
					if (dataReceived.indexOf("-")>0) {
						var ca=dataReceived.split('-');
						$('#welcomeBlock').html('<span id="welcome" style="font-size:12px;border:1px solid black;padding:3px">Welcome <b>' + ca[1] + '</b>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:logoffUser();"><b>Logout</a></b></span>');
					}
					sessionStorage.setItem('TokenSSKey', dataReceived);
					tokenSSKey=dataReceived;
				       	$.mobile.changePage("#home");
					break;
				case "alertForm":
					$('#alertErrMsg').message("hide");
					$('#alertSignup').dialog('close');
					break;
				case "directionForm":
					$('#directionErrMsg').message("hide");
					$('#directionResults').html(dataReceived).trigger('create');
					break;
				case "editDirectionForm":
					$('#editDirectionErrMsg').message("hide");
					$('#editDirectionResults').html(dataReceived).trigger('create');
					break;
				case "myaForm":
					$('#myaErrMsg').message("hide");
					if (dataReceived=="Bad") {
						putError("mya",'<p>Please reenter your old password.</p>');
						return false;
					}	
					$.mobile.changePage("#home");
					break;
				case "forgottenForm":
					if (dataReceived.length>0 && dataReceived != "Bad") {
						$('#forgottenErrMsg').message("hide");
						$('#logErrMsg').message("hide");
						putMsg("log","<p>Your password has been emailed to you</p>");
						$.mobile.changePage("#login");
						break;
					} else {
						putError("forgotten",'<p>Cannot find that email address on file</p>');
						return false;
					}		
					break;
				case 'loginForm':
					if (dataReceived.length>0 && dataReceived.substring(0,5)=="wait:") {
						putError("log","Your account has been frozen for " + dataReceived.substring(5) + " minutes");
						return false;
					}
					if (dataReceived.length>0 && parseInt(dataReceived)>0) {
						$('#logErrMsg').message("hide");
						userId=parseInt(dataReceived);
						$('.notLogged').hide();
						$('.logged').show();
						$('.userIsTd').hide();
						$('.userIsAdmin').hide();
						$('.noJoin').show();
						sessionStorage.setItem('TokenSSKey', dataReceived);
						if (dataReceived.indexOf("-")>0) {
							var ca=dataReceived.split('-');
							if (ca[2]!="") {
								if (ca[2]=="td" || ca[2]=="to") {
									$("#adminHref").attr('href',"/admin/pokeradmin.php");
									$('.userIsTd').show();
									$('.userIsAdmin').show();
									$('.noJoin').hide();
								}
								if (ca[2]=="am" || ca[2]=="de") {
									$("#adminHref").attr('href',"/admin/index.php");
									$('.userIsTd').hide();
									$('.noJoin').hide();
									$('.userIsAdmin').show();
								}
							}
						}
						$('#welcomeBlock').html('<span id="welcome" style="font-size:12px;border:1px solid black;padding:3px">Welcome <b>' + ca[3] + '</b>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:logoffUser();"><b>Logout</a></b></span>');
						if (gotoPage=="") {
						       	$.mobile.changePage("#home");
						} else {
						       	$.mobile.changePage(gotoPage);
						       	gotoPage="";
						}
						break;
					}
					putError("log",'<p>Cannot find user with those credentials. Please try again.<br><a href="#forgotten" data-rel="dialog">Forgot your password?</a></p>');
					return false;
					break;
			}
		};
		dataToSend += '&seid=' + SESSID;
		var rtnType="html";
		switch(formName) {
			case "signupForm":
				tmp=trim($('#sHandle').val());
				if (tmp.length<1) {
					putError("signup","<p>You must enter a <i>user name</i> for yourself. This is what people on the site will see.</p>");
					return false;
				}
				tmp=trim($('#sEmailAddr').val());
				if (tmp.length<6) {
					putError("signup","<p>You must enter a valid <i>email address</i>.</p>");
					return false;
				}
				tmp=trim($('#sCity').val());
				if (tmp.length<4) {
					putError("signup","<p>You must enter a <i>City</i>.</p>");
					return false;
				}
				tmp=trim($('#sZip').val());
				if (tmp.length<5) {
					putError("signup","<p>You must enter a <i>Zip Code</i>.</p>");
					return false;
				}
				tmp=trim($('#sPassword').val());
				if (tmp.length<6) {
					putError("signup","<p>You must enter a <i>password</i> of at least 6 characters.</p>");
					return false;
				}
				tmp1=trim($('#sRePassword').val());
				if (tmp!=tmp1) {
					putError("signup","<p>The two passwords you entered do not match.</p>");
					return false;
				}
				tmp=trim($('#sFirst').val());
				if (tmp.length<1) {
					putError("signup","<p>You must enter a <i>first name</i>.</p>");
					return false;
				}
				tmp=trim($('#sLast').val());
				if (tmp.length<1) {
					putError("signup","<p>You must enter a <i>last name</i>.</p>");
					return false;
				}
///				tmp=trim($('#sAnswer').val());
///				if (tmp.length<1) {
///					putError("signup","<p>You must enter an <i>answer</i> to the question.</p>");
////					return false;
////				}
				break;
			case "alertForm":
				$('#alertErrMsg').message("hide");
				rtnType="text";
				break;
			case "directionForm":
				$('#directionErrMsg').message("hide");
				rtnType="text";
				break;
			case "editDirectionForm":
				$('#directionErrMsg').message("hide");
				rtnType="text";
				break;
			case "myaForm":
				var tlen=0;
				var tmp;
///				tmp=trim($('#myaOldPW').val());
				tmp=trim($('#myaNewPW').val());
				if (tmp.length>0) {
					npw=trim($('#myaNewPW').val());
					rnpw=trim($('#myaReNewPW').val());
					if (npw.length==0 || rnpw.length==0 || npw!=rnpw) {
						putError("mya","<p>You must enter the new password twice and it must be the entered the same way</p>");
						return false;
					}
					if (npw.length<6) {
						putError("mya","<p>Passwords must be at least 6 characters in length</p>");
						return false;
					}
///					var mediumRegex = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");
///					if (!mediumRegex.test(npw)) {
///						putError("mya","<p>Password must contain a mix of letters and numbers</p>");
///						return false;
///					}
				}
				rtnType="text";
				break;
			case "loginForm":
				$("#loginEmailAddr").val("");
				$("#loginPassword").val("");
				$('#logErrMsg').message("hide");
				rtnType="text";
				break;
			case "forgottenForm":
				$('#forgottenErrMsg').message("hide");
				rtnType="text";
				break;
		}
		$.post(formUrl,dataToSend,callBack,rtnType);
		return false;
	});
});
</script>

<div data-role="page" id="home">
<div data-role="header" data-position="fixed" data-title="Home" data-theme="b">
<h1>Find My Class</h1>
</div><!-- /header -->
<div data-role="content">
<div id="welcomeBlock">$welcome</div>
$writeup
<div $mainwidth>
<ul data-role="listview" data-inset="true">
<li data-role="list-divider">Main Menu</li>
<li style="display:$logged" class="logged"><a href="#underConstruction" data-transition="slide">Find My Class!</a></li>
<li><a href="#findAClass" data-transition="slide">Find a Class!</a></li>
<li><a href="#findABuilding" data-transition="slide">Building Directions</a></li>
<li><a href="faq.php?v=$ver" data-transition="slide">FAQs</a></li>
<li style="display:$logged" class="logged"><a href="#uploadSchedule" data-transition="slide">Upload Schedule</a></li>
<li style="display:$logged" class="logged"><a href="#changeSchedule" data-transition="slide">Change Schedule</a></li>
<li style="display:$logged" class="logged"><a href='javascript:loadMyAccount();' data-transition="slide">My Account</a></li>
<li><a href="contact.php" data-transition="slide">Contact Us</a></li>
<li><a href="#tandc" data-transition="slide">Terms and Conditions</a></li>
<li><a href="privacy_policy.php" data-transition="slide">Privacy Policy</a></li>
<li style="display:$addis" class="logged"><a href="/admin/index.php" data-transition="slide" rel="external">Administration</a></li>
</ul> 
<p style="font-size:12px;margin-left:8px">Copyright $yr by The Wireless Routers<br>All Rights Reserved</p>
</div>
</div><!-- /content -->
</div><!-- /home page -->

<div data-role="dialog" id="forgotten" data-title="Forgot Password">
<div data-role="header" data-position="fixed" data-theme="b">
<h1>Recover Password</h1>
</div><!-- /header -->

<div data-role="content">
<div id="forgottenErrMsg"></div>
<form name="forgottenForm" action="forgotten.php">
<label for="forgottenEmailAddr">Email Address</label>
<input type=email id="forgottenEmailAddr" name="emailAddr" autofocus="autofocus"><br>
<input type="submit" value="Retrieve Password" data-inline="true" data-theme="e">
</form>
</div><!-- /content -->
</div><!-- /forgotten-->

<div data-role="dialog" id="login" data-title="Log In">
<div data-role="header" data-theme="b">
<h1>Login to Account</h1>
</div>
<div data-role="content">
<div id="logErrMsg"></div>
<form name="loginForm" action="moblogin.php">
<label for="loginEmailAddr">User Name or Email Address</label>
<input type=text id="loginEmailAddr" name="emailAddr" autofocus="autofocus"><br>
<label for="loginPassword">Password</label>
<input type=password id="loginPassword" name="password">
<input type="submit" value="Log In" data-inline="true" data-theme="e">
<br><a data-role="button" data-inline="true" data-mini="true" href="#forgotten" data-rel="dialog">Forgot your password?</a> <a data-role="button" data-inline="true" data-mini="true" href="#signup">Don't Have one?</a>
</form>
</div><!-- /content -->
</div><!-- /login -->

<div data-role="page" id="doDirections" data-title="User Directions">
<div data-role="header" data-theme="b">
<a href="#home" data-icon="home" data-iconpos="notext"></a>
<h1>User Directions</h1>
</div>
<div data-role="content">
<form name="directionForm" action="">
<div id="directionResults"></div>
<div id="directionErrMsg"></div>
</form>
</div><!-- /content -->
</div><!-- /doDirections -->

<div data-role="page" id="editDirections" data-title="Edit Directions">
<div data-role="header" data-theme="b">
<a href="#home" data-icon="home" data-iconpos="notext"></a>
<h1>Edit Directions</h1>
</div>
<div data-role="content">
<form name="editDirectionForm" action="">
<div id="editDirectionResults"></div>
<div id="editDirectionErrMsg"></div>
</form>
</div><!-- /content -->
</div><!-- /editDirections -->

<div data-role="dialog" id="showDirections" data-title="Directions">
<div data-role="header" data-position="fixed" data-theme="b">
<h1>Outdoor Directions</h1>
</div><!-- /header -->
<div data-role="content">
<div id="walking-dir"></div>
</div><!-- /content -->
</div><!-- /showDirections-->

<div data-role="dialog" id="showUserDirections" data-title="Directions">
<div data-role="header" data-position="fixed" data-theme="b">
<h1>Directions</h1>
</div><!-- /header -->
<div data-role="content">
<div id="dirList"></div>
</div><!-- /content -->
</div><!-- /showUserDirections-->

<div data-role="dialog" id="tandc" data-title="Terms & Conditions">
<div data-role="header" data-position="fixed" data-theme="b">
<h1>Terms & Conditions</h1>
</div><!-- /header -->
<div data-role="content">
<p>$website grants User(you) a non-exclusive, non-transferable, revocable license to $website. User acknowledges that User has been granted this license because of User's 
representations regarding its authorized use of $website.</p>
<p>$website does not warrant the accuracy, completeness, timeliness, currentness, merchantability or fitness for a particular purpose of $website. $website and its representatives shall not be liable 
for, and User agrees not to sue for, any claim relating to $website's procuring, compiling, collecting, interpreting, reporting, communicating, or delivering $website.<p>
<p>User is not an agent or representative of $website and will not represent that it is to any third party.</p>
<p>The terms in your User Agreement and these Terms relating to disclaimer of warranties, access and use of $website, audit, limitation of liability, indemnification, your release of 
claims, and payment for $website shall survive any termination.</p>
<p>Agreements with $website may be modified only by a written amendment signed by both parties.</p>
<p>User agrees that any breach by User of its agreements with $website would cause $website irreparable harm and that, in addition to money damages, $website shall be entitled to injunctive relief, 
without having to post a bond.</p>
<p>Since information contained in $website is available to other Users, any such information retrieved from $website systems is not the confidential information of User. In the event a 
conflict arises between either the terms in your User Agreement or these Terms and any other agreement, the terms in the User Agreement and/or these Terms shall prevail.</p>
<p>To the extent that any $website rely upon or use information from any third-party sources, then those sources shall be third-party beneficiaries with all rights and privileges of $website. $website, 
and any such sources (as third-party beneficiaries), are entitled to enforce your User Agreement and these Terms directly against you.</p>
<p>Your User Agreement and these Terms shall be governed by Pennsylvania law, without reference to its choice of law rules. Venue for all actions shall be in the court system in and 
around Stroudsburg, PA. The prevailing party in any action shall be entitled to reasonable attorneys' fees and costs.</p>
<h4>Data Security Requirements</h4>
<p>User must <ol>
<li>not discuss passwords by telephone with any caller, even if the caller claims to be an employee of $website;</li>
<li>not share their account login credentials with any third party.</li>
</ol>
<p>The terms and conditions relating to Data Security Requirements are subject to change without notice.</p>
</div><!-- /content -->
</div><!-- /tandc-->

<div data-role="page" id="myAccount" data-title="My Account" data-needs-auth="true">
<div data-role="header" data-theme="b">
<a href="#home" data-icon="home" data-iconpos="notext"></a>
<h1>My Account</h1>
</div>
<div data-role="content">
<p>Only enter password information if you want to change your existing password. Otherwise leave the fields blank. Passwords must be at least 6 digits in length.</p>
<form name="myaForm" action="savemya.php" method="post">
<label for="myaOldPW">Current Password</label>
<input type=password name="myaOldPW" id="myaOldPW" value="" autofocus="autofocus">
<label for="myaNewPW">New Password</label>
<input type=password name="myaNewPW"  id="myaNewPW" value="">
<label for="myaReNewPW">Reenter New Password</label>
<input type=password name="myaReNewPW" id="myaReNewPW" value="">
<hr>
<label for="mEmailAddr">Email Address</label>
<input type=email id="mEmailAddr" name="mEmailAddr"">
<label for="mFirst">First Name</label>
<input type=text id="mFirst" name="mFirst">
<label for="mLast">Last Name</label>
<input type=text id="mLast" name="mLast">
<label for="mCell">Cell Phone (for alerts)</label>
<input type=text id="mCell" name="mCell">
<label for="mCity">City Name</label>
<input type=text id="mCity" name="mCity">
<label for="mState">State</label>
<input type=text id="mState" name="mState">
<label for="mZip">Zip Code</label>
<input type=text id="mZip" name="mZip">

<div id="myaErrMsg"></div>
<input type="submit" value="Update Information" data-theme="e" data-inline="true">
</form>
</div><!-- /content -->
</div><!-- /myaForm -->

<div data-role="page" id="underconstruction" data-title="Under Construction">
<div data-role="header" data-position="fixed" data-theme="b">
<a href="#home" data-icon="home" data-iconpos="notext"></a>
<h1>Under Construction</h1>
</div><!-- /header -->
<div data-role="content">
<h3>Under Construction</h3>
</div><!-- /content -->
</div><!-- /underconstruction -->

<div data-role="dialog" id="alertSignup" data-title="Alert Signup">
<div data-role="header" data-theme="b">
<h1>Signup for Alerts</h1>
</div>
<div data-role="content">
<form name="alertForm" action="alert.php" method="post">
<div id="alertResults"></div>
</form>
</div><!-- /content -->
</div><!-- /alert signup -->

<div data-role="dialog" id="showClassInfo" data-title="Class Information">
<div data-role="header" data-theme="b">
<h1>Class Information</h1>
</div>
<div data-role="content">
<div id="classInfo"></div>
</div><!-- /content -->
</div><!-- /showclassinfo -->

<div data-role="dialog" id="signup" data-title="Signup for an Account">
<div data-role="header" data-theme="b">
<h1>Signup for Account</h1>
</div>
<div data-role="content">
<form name="signupForm" action="signup.php" method="post">
<label for="sHandle">User Name</label>
<input type=text id="sHandle" name="sHandle" autofocus="autofocus">
<label for="sEmailAddr">Email Address</label>
<input type=email id="sEmailAddr" name="sEmailAddr"">
<label for="sPassword">Password</label>
<input type=password id="sPassword" name="sPassword">
<label for="sRePassword">Reenter Password</label>
<input type=password id="sRePassword" name="sRePassword">
<label for="sFirst">First Name</label>
<input type=text id="sFirst" name="sFirst">
<label for="sLast">Last Name</label>
<input type=text id="sLast" name="sLast">
<label for="sCell">Cell Phone (for alerts)</label>
<input type=text id="sCell" name="sCell">
<label for="sCity">City Name</label>
<input type=text id="sCity" name="sCity">
<label for="sState">State</label>
<input type=text id="sState" name="sState">
<label for="sZip">Zip Code</label>
<input type=text id="sZip" name="sZip">
<div id="signupErrMsg"></div>
<input type="submit" value="Sign Up Now!" data-theme="e" data-inline="true">
</form>
</div><!-- /content -->
</div><!-- /signup for account -->

<div data-role="page" id="findAClass" data-title="Find My Class">
<div data-role="header" data-theme="b">
<a href="#home" data-icon="home" data-iconpos="notext"></a>
<h1>Find a Class</h1>
</div>
<div data-role="content"$mainwidth>
<label for="className">Class Name</label>
<input type=text id="className" name="className" autofocus="autofocus">
<p style="font-size:11px">Enter enough of the class title, course number, or prefix so that the class shows up in the list below or enter the CRN. Then tap or click the class you want from the results list</p>
<div id="findClassList"></div></div>
</div><!-- /content -->
</div><!-- /findaclass -->

<div data-role="page" id="findABuilding" data-title="Find a Building">
<div data-role="header" data-theme="b">
<a href="#home" data-icon="home" data-iconpos="notext"></a>
<h1>Find a Building</h1>
</div>
<div data-role="content"$mainwidth>
<p>Select the building you want directions to by tapping or clicking on it.</p>
<div id="findBuildingList"></div></div>
</div><!-- /content -->
</div><!-- /findaclass -->

<div data-role="page" id="uploadSchedule" data-title="Upload Schedule">
<div data-role="header" data-theme="b">
<a href="#home" data-icon="home" data-iconpos="notext"></a>
<h1>Upload Schedule</h1>
</div>
<div data-role="content"$mainwidth>
<p>Select the building you want directions to by tapping or clicking on it.</p>
<div id="findBuildingList"></div></div>           
</div><!-- /content -->
</div><!-- /findaclass -->

<div id="spinner" class="modal"></div>
END;
include("mfooter.php");
