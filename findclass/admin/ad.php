<?php
//
//	Main page for HS Sports App
//	Copyright 2012 by Michael Lewis
//	All Rights Reserved
//
include("../session.php");
include("../common.php");
include("../mobhdr.php");
include("../mobile.php");
include("gsports.php");
$SESSID=session_id();
if (!isset($_SESSION['valid'])) exit;
$yr=date("Y");
if ($yr != "2012") $yr="2012-$yr";
$uid=$_SESSION['valid'];
$utype=$_SESSION['type'];
$sid=$_SESSION['sid'];
if ($uid==0 || $utype!="ad" || $sid==0) exit;
$sportlist=getSportsForSchool($sid);
$hdr=getMobileHeader();
$logged="";
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
                url('http://i.stack.imgur.com/FhHRx.gif') 
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
<script type="text/javascript">
var dirSchoolID=0;
var numCB=0;
var numFlt=$cntflt;
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
var schoolID=0;
var sportID=0;
var lat=0.0;
var lng=0.0;
var olat=0.0;
var olng=0.0;
var oID=0;
var scheduleID=0;
var mostRecent="";
var MAP;
var mapLat=0;
var mapLng=0;
var mapSport="soccer";
var initLat=0;
var initLng=0;
var curLat=0;
var curLng=0;
var sID=0;
var spID=0;
var dirID=0;
function addSchNotes(schid) {
	scheduleID=schid;
	$.mobile.changePage("#schNotes");
}	
function editDirInfo(did) {
	dirID=did;
	$.mobile.changePage("#editDirections");
}
function saveDirInfo(sid,spid,newflag) {
	if (newflag==0) {
		$.post('savedirinfo.php',{
			sid:sid,spid:spid,nlat:curLat,nlng:curLng,ilat:initLat,ilng:initLng,addr:escape($('#wizAddress').val()),dn: escape($('#dirNotes').val())}, function(data) {
			// nothing to do here
		});
	} else {
		$.post('savedirinfo.php',{
			did:sid,sid:schoolID,spid:sportID,nlat:curLat,nlng:curLng,ilat:initLat,ilng:initLng,addr:escape($('#editAddr').val()),dn: escape($('#editNotes').val())}, function(data) {
			// nothing to do here
		});
	}
	history.back();
}
function save_plot (f) {
	var lat=$('#wizLat').val();
	var lng=$('#wizLng').val();
	alert('Saving: ' + [lat,lng]);
}
function load_map (m, f) {
  var mdiv = f;
  MAP = mdiv;

  mdiv.EPSG4326 = new OpenLayers.Projection("EPSG:4326");
  mdiv.EPSG900913 = new OpenLayers.Projection("EPSG:900913");

  mdiv.map = new OpenLayers.Map(mdiv.attr('id'), {
    controls: [
      new OpenLayers.Control.PanZoom(),
      new OpenLayers.Control.Navigation()
    ],
    units: "m",
    maxResolution: 156543.0339,
    numZoomLevels: 20,
    displayProjection: mdiv.EPSG4326
  });

  mdiv.map.div = mdiv;

  mdiv.ithreat = new OpenLayers.Layer.OSM("GIM", "/tile-server.php?mode=mapnik&z=\${z}&x=\${x}&y=\${y}", {
    type: 'png',
    keyid: "ithreat",
    displayOutsideMaxExtent: true,
    wrapDateLine: true,
    layerCode: "I"
  });
  init_map_functions(mdiv);
  mdiv.map.addLayer(mdiv.ithreat);
  mdiv.move_to(initLat,initLng, 15);
  mdiv.asset_layer = mdiv.create_asset_layer();
  mdiv.map.addLayer(mdiv.asset_layer);
  mdiv.asset_mover = mdiv.create_asset_mover(mdiv.asset_layer);  
  mdiv.map.addControl(mdiv.asset_mover);
  mdiv.asset_mover.activate();

  mdiv.asset = mdiv.create_asset();
  mdiv.asset_layer.addFeatures([mdiv.asset]);

  return;
}
function init_map_functions (mdiv) {
  mdiv.layers = {};
  mdiv.create_asset_layer = function () {
    var lay = new OpenLayers.Layer.Vector('asset', {
      styleMap: new OpenLayers.Style({
        externalGraphic: "\${icon}",
        graphicWidth: "\${w}",
        graphicHeight: "\${h}",
        graphicOpacity: 0.75,
        graphicXOffset: "\${x}",
        graphicYOffset: "\${y}",
        graphicZIndex: "\${z}"
      }),
      rendererOptions: {zIndexing: true}
    });

    lay.setVisibility(true);

    return lay;
  };

mdiv.create_asset_mover = function (l) {
	var mover = new OpenLayers.Control.DragFeature(l);
	curLat=initLat;
	curLng=initLng;	
	mover.onStart=function (obj,loc) {
      		var newpos = MAP.real_latlng(MAP.map.getLonLatFromPixel(loc));
		curLat=newpos.lat;
		curLng=newpos.lon;
	};
	mover.onComplete=function (obj,loc) {
      		var newpos = MAP.real_latlng(MAP.map.getLonLatFromPixel(loc));
		var lat=newpos.lat;
		var lng=newpos.lon;
		if (lat!=curLat || lng!=curLng) {
			curLat=lat;
			curLng=lng;
		}
	};
	mover.onDrag = function (obj, loc) {
      		var newpos = MAP.real_latlng(MAP.map.getLonLatFromPixel(loc));
		mapLat=newpos.lat;
		mapLng=newpos.lon;
	};
	return mover;
};

  mdiv.create_asset = function () {
    var pt = this.map.getCenter();
    var obj = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Point(pt.lon, pt.lat));
    obj.attributes.icon = '/images/icon_' + mapSport + '.png';
    obj.attributes.w = 32;
    obj.attributes.h = 32;
    obj.attributes.x = -20;
    obj.attributes.y = -24;
    return obj;
  };

  mdiv.real_latlng = function (pt) {
    return pt.clone().transform(this.map.getProjectionObject(), this.EPSG4326);
  };

  mdiv.map_latlng = function (pt) {
    return pt.clone().transform(this.EPSG4326, this.map.getProjectionObject());
  };

  mdiv.map.zoomToMaxExtent = function (options) {
    var restricted = options ? options.restricted : true;
    var maxExtent = this.getMaxExtent({restricted: restricted});
    this.zoomToExtent(maxExtent);
    this.zoomIn();
  };

  mdiv.move_to = function (lat, lng, zoom) {
    if (zoom == null) zoom = this.map.getZoom();
    this.map.setCenter(this.map_latlng(new OpenLayers.LonLat(lng, lat)), zoom);
  };

}
function auto_plot(f, how) {
	if (how == 'address') {
		var addr=$('#wizAddress').val();
		if (addr.length<5) return;
		$.post('geoaddr2latlng.php',{address: escape(addr)},function(data) {
				var ca=data.split('|');
				mapLat=1 * ca[0];
				mapLng=1 * ca[1];
				MAP.asset.move(MAP.map_latlng(new OpenLayers.LonLat(mapLng, mapLat)));
				MAP.move_to(mapLat, mapLng, 15);
		},
		'text');
	}
}

OpenLayers.Util.onImageLoadError = function() { this.src = "/images/empty-maptile.png"; };

OpenLayers.IMAGE_RELOAD_ATTEMPTS = 12;

OpenLayers.Control.PanZoom.prototype.draw = function (px) {
  OpenLayers.Control.prototype.draw.apply(this, arguments);
  this.buttons = [];

  px = this.position.clone();
  var centered = new (OpenLayers.Pixel)(px.x, px.y);
  var sz = new (OpenLayers.Size)(18, 18);

  this._addButton("zoomin", "zoom-plus-mini.png", centered, sz);
  this._addButton("zoomworld", "zoom-world-mini.png", centered.add(0, sz.h), sz);
  this._addButton("zoomout", "zoom-minus-mini.png", centered.add(0, sz.h*2), sz);
  return this.div;
}
function handleDirections(sid,spid) {
	dirSchoolID=sid;
	sportID=spid;
	$.mobile.changePage("#doDirections");
}
function handleGetScores(data) {
	$("#enterResults").html(data).trigger("create");
}
function getScores(schid) {
	scheduleID=schid;
	$.mobile.changePage("#enterScores");
}
function handleGetAlert(data) {
	$("#alertResults").html(data).trigger("create");
}
function signup4Alerts(sid,spid) {
	schoolID=sid;
	sportID=spid;
	$.mobile.changePage("#alertSignup");
}
function setCB(v,pre) {
	var numEnt=numCB;
	if (pre=="flt") numEnt=numFlt;
	for (i=0;i<numEnt;i++) {
		fld='#' + pre + '-' + i;
		cc = $(fld).attr('checked') == undefined  ? false : true;
		if (v) {
			if (!cc) $(fld).click().checkboxradio("refresh");
		} else {
			if (cc) $(fld).click().checkboxradio("refresh");
		}
	}
}
function addToSchedule(sid,spid) {
	sID=sid;
	spID=spid;
	$.mobile.changePage("#addGame");
}
function submitChange(schid) {
	scheduleID=schid;
	$.mobile.changePage("#fixit");
}
function fetchSports(sid) {
	schoolID=sid;
	$.mobile.changePage("#defineSports");
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
	$.mobile.changePage('#showUserDirections');
}
function getLocation(what,xlat,xlng,oid,spid) {
	olat=xlat;
	olng=xlng;
	oID=oid;
	spID=spid;
	navigator.geolocation.getCurrentPosition(gotLocation, noLocation);
	$('#showDirections').dialog('close');
	$('#dirList').html('');
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
function errFunc(type) {
	alert("Error: Could not fetch " + type + " Data");
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
function handleGetSchedule(data) {
	$('#scheduleResults').html(data).trigger('create');
}
function showAtAGlance(sid,spid) {
	schoolID=sid;
	sportID=spid;
	$.mobile.changePage('#atAGlance');
}
function showSchedule(sid,spid) {
	schoolID=sid;
	sportID=spid;
	$.mobile.changePage('#displaySchedule');
}
function handleSportsData(data) {
	$('#schoolSports').html(data).trigger('create');
}
function selectSchool(sid,newflag) {
	schoolID=sid;
	$.cookie('s_id',sid,{expires:10*365});
	if (mostRecent=="") {
		mostRecent=sid;
	} else {
		if (mostRecent.indexOf(";")==-1) {
			if (mostRecent!=sid) mostRecent=mostRecent + ";" + sid;
		} else {
			cnt=1;
			mra=mostRecent.split(";");
			mostRecent=sid;
			sep=";";
			for (i=0;i<mra.length;i++) {
				if (sid==mra[i]) continue;
				mostRecent=mostRecent + sep + mra[i];
				cnt++;
				if (cnt>6) break;
			}
		}	
	}
	$.cookie('most_rec',mostRecent,{expires:10*365});
	$('#findMenu').trigger('collapse');
	loadSports();
	loadMostRecent();
	if (newflag==0) $('#sportsMenu').trigger('expand');
}
function delDirInfo(did) {
	$.post('deldirinfo.php','did=' + did,function(data) {
		history.back();
	});
}
function removeSport(sid,spid) {
	schoolID=sid;
	sportID=spid;
	$.post('removesport.php','sid=' + sid + '&spid=' + spid,function(data) {
		loadSports();
	});
}
function loadMostRecent() {
	mostRecent=$.cookie('most_rec');
	$.post('getmostrecent.php','mr=' + mostRecent,function(data) {
		ca=data.split('|');
		mostRecent=ca[0];
		$.cookie('most_rec',mostRecent,{expires:10*365});
		$('#mostRecentResults').html(ca[1]).trigger('create');
	});
}
function loadSports() {
	schoolID=$.cookie('s_id');
	if (schoolID==null || schoolID==0) {
		$('#schoolSports').html('<p>No school selected yet.</p>');
		return;
	}
	$.post('getsports.php','sid=' + schoolID,handleSportsData);
}
function logoffUser() {
	if (userId!=0) {
		userId=0;
		$.get('logoff.php','seid=' + SESSID,function(data){});
	}
	sessionStorage.removeItem("TokenSSKey");
	$('.notLogged').show();
	$('.logged').hide();
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
	$.mobile.changePage("#myAccount");
}
function loadMyAccount() {
	$.post("getqa.php",'&uid=' + userId,handleMyaData);
}
//
//	On ready init code
//
$(document).ready(function() {
	$('#schNotes').live('pagebeforeshow', function(){
		$('#scID').val(scheduleID);
		$.post('getschnotes.php','schid=' + scheduleID,function(data) {
			$("#scNotes").val(data);
		});
	});
	$('#defineSports').live('pagebeforeshow', function(){
		$.post('getsportslist.php','sid=' + schoolID,function(data) {
			$("#sportsList").html(data).trigger("create");
		});
	});
	$('#enterScores').live('pagebeforeshow', function(){
		$.post('getscores.php','schid=' + scheduleID,function(data) {
			handleGetScores(data);
		});
	});
	$('#alertSignup').live('pagebeforeshow', function(){
		$.post('getalert.php','spid=' + sportID + '&sid=' + schoolID,function(data) {
			handleGetAlert(data);
		});
	});
	$('#addGame').live('pagebeforeshow', function(){
		$.post('getschools.php',function(data) {
			var da=data.split('~');
			var ta=da[0].split("/");
			initDate("#aDate",ta[0]-1,ta[2],ta[1],"#acalMonth");
			$("#addGameResults").html(da[1]).trigger("create");
			$("#addSID").val(sID);
			$("#addSPID").val(spID);
		});
	});
	$('#fixit').live('pagebeforeshow', function(){
		$.post('getfixit.php','id=' + scheduleID,function(data) {
			var da=data.split('~');
			var ta=da[0].split("/");
			initDate("#fDate",ta[0]-1,ta[2],ta[1],"#calMonth");
			$("#fixitResults").html(da[1]).trigger("create");
		});
	});
	$('#displaySchedule').live('pagebeforeshow', function(){
		$.post('getschedule.php','sid=' + schoolID + '&spid=' + sportID,function(data) {
			handleGetSchedule(data);
		});
	});
	$('#atAGlance').live('pagebeforeshow', function(){
		$.post('atg.php','sid=' + schoolID + '&spid=' + sportID,function(data) {
			$('#atgResults').html(data).trigger('create');;
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
				load_map('map',$('#wizMap'));
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
				$('#editDirectionResults').html(ca[3]).trigger('create');
				if (initLat!=0 || initLng!=0) load_map('map',$('#editMap'));
			} else {
				$('#editDirectionResults').html(data).trigger('create');
			}
		});
	});
	$('#showDirections').live('pagebeforeshow', function(){
		$.post('getdir.php','lat=' + lat + '&lng=' + lng + '&olat=' + olat + '&olng=' + olng + '&oid=' + oID + '&spid=' + spID,handleDirData).error(errFunc);
	});
	$('#showUserDirections').live('pagebeforeshow', function(){
		$.post('getdir.php','lat=' + lat + '&lng=' + lng + '&olat=' + olat + '&olng=' + olng + '&oid=' + oID + '&spid=' + spID + '&nouser=1',handleUserDirData).error(errFunc);
	});
	$("#logErrMsg").message({type:"error",dismiss:false,theme:"f"});
	$("#logErrMsg").message("hide");
	$("#getschErrMsg").message({type:"error",dismiss:false,theme:"f"});
	$("#getschErrMsg").message("hide");
	$('form').submit(function(event) {
		event.preventDefault();
		var thisForm=$(this);
		var formUrl=thisForm.attr('action');
		var formName=thisForm.attr('name');
		var dataToSend=thisForm.serialize();
		var callBack=function(dataReceived) {
			switch(formName) {
				case "searchSchoolForm":
					if (dataReceived.length>0) {
						if (dataReceived=="nf") {
							putError("search",'No schools found matching your criteria. Try again with something more general such as a zip code</p>');
							return false;
						}
						$('#searchErrMsg').message("hide");
						$('#schoolSearchResults').html(dataReceived).trigger('create');
						return false;
					}
					break;
				case "enterForm":
					$('#enterScores').dialog('close');
					break;
				case "signupForm":
					if (dataReceived=="black") {
						putError("signup","<p>You have been banned from the site.</p>");
						return false;
					}
					if (dataReceived=="already") {
						putError("signup","<p>A user with that email address is already on file.</p>");
						return false;
					}
					if (dataReceived=="wrong") {
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
					sessionStorage.setItem('TokenSSKey', dataReceived);
////					if (dataReceived.indexOf("-0")>0) {
////					       	$.mobile.changePage("#home");
////						break;
////					}
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
				case "addGameForm":
					$('#addGame').dialog('close');
					break;
				case "fixitForm":
					$("#fixit").dialog('close');
					break;
				case "myaForm":
					if (dataReceived.length>0 && dataReceived == "Ok") {
						$('#myaErrMsg').message("hide");
						$.mobile.changePage("#home");
						$('#general').trigger('expand');
						break;
					} else {
						putError("mya",'<p>Please reenter your old password.</p>');
						return false;
					}	
					break;
				case "getschForm":
					$("#getschInfoMsg").html(dataReceived).trigger('create');
					loadSports();
					$.mobile.changePage("#home");
/////					$('#sportsMenu').trigger('expand');
					break;
				case "specschForm":
					loadSports();
					$.mobile.changePage("#home");
					$('#general').trigger('expand');
					break;
				case "schNotesForm":
					$('#schNotes').dialog('close');
					break;	
				case "abuseForm":
					$('#abuseErrMsg').message("hide");
					$('#fileAbuse').dialog('close');
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
						sessionStorage.setItem('TokenSSKey', dataReceived);
						if (dataReceived.indexOf("-")>0) {
							var ca=dataReceived.split('-');
							var m=ca[2];
							if (ca[3]!="") {
								if (ca[3]=="ad") $("#adminHref").attr('href',"/admin/ad.php");
							}
							if (m.length) {
								$.cookie('most_rec',m,{expires:10*365});
								if (m.indexOf(";")==-1) {
									schoolID=m;
								} else {
									ca=m.split(";");
									schoolID=ca[0];
								}
								selectSchool(schoolID,1);
							}
						}
					       	$.mobile.changePage("#home");
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
			case "enterForm":
				if ($('#eLocked').val()=="locked") {
					$('#enterScores').dialog('close');
					break;
				}
				break;
			case "signupForm":
				tmp=trim($('#sEmailAddr').val());
				if (tmp.length<6) {
					putError("signup","<p>You must enter a valid <i>email address</i>.</p>");
					return false;
				}
				tmp=trim($('#sPassword').val());
				if (tmp.length<7) {
					putError("signup","<p>You must enter a <i>password</i> of at least 7 characters.</p>");
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
				tmp=trim($('#sAnswer').val());
				if (tmp.length<1) {
					putError("signup","<p>You must enter an <i>answer</i> to the question.</p>");
					return false;
				}
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
			case "addGameForm":
				$('#addGameErrMsg').message("hide");
				rtnType="text";
				break;
			case "fixitForm":
				$('#fixitErrMsg').message("hide");
				rtnType="text";
				break;
			case "searchSchoolForm":
				tmp=trim($('#searchSch').val());
				if (tmp.length==0) {
					putError("search","<p>You must enter <i>something</i> to search for.</p>");
					return false;
				}
				$('#searchErrMsg').message("hide");
				rtnType="text";
				break;
			case "schNotesForm":
				rtnType="text";
				break;
			case "abuseForm":
				tmp=trim($('#abuseReason').val());
				if (tmp.length==0) {
					putError("abuse","<p>You must enter <i>something</i> as a reason for reporting this entry.</p>");
					return false;
				}
				$('#abuseDID').val(dirID);
				$('#abuseErrMsg').message("hide");
				rtnType="text";
				break;
			case "myaForm":
				var tlen=0;
				var tmp;
				tmp=trim($('#myaOldPW').val());
				if (tmp.length>0) {
					npw=trim($('#myaNewPW').val());
					rnpw=trim($('#myaReNewPW').val());
					if (npw.length==0 || rnpw.length==0 || npw!=rnpw) {
						putError("mya","<p>You must enter the new password twice and it must be the entered the same way</p>");
						return false;
					}
					if (npw.length<7) {
						putError("mya","<p>Passwords must be at least 7 characters in length</p>");
						return false;
					}
					var mediumRegex = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");
					if (!mediumRegex.test(npw)) {
						putError("mya","<p>Password must contain a mix of letters and numbers</p>");
						return false;
					}
				}
				rtnType="text";
				break;
			case "loginForm":
				$('#logErrMsg').message("hide");
				rtnType="text";
				break;
			case "getschForm":
				$('#getschErrMsg').message("hide");
				rtnType="text";
				break;
			case "specschForm":
				$('#specschErrMsg').message("hide");
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
	$('.getschButton').bind('click',function() {
		$("#getschSID").val(schoolID);
	});
	$('.specschButton').bind('click',function() {
		$("#specschSID").val(schoolID);
	});
	loadSports();
	loadMostRecent();
});
</script>
<div data-role="page" id="home">
<div data-role="header" data-position="fixed" data-title="Home" data-theme="a">
<h1>SportsNet</h1>
</div><!-- /header -->
<div data-role="content">
<div data-role="collapsible-set">
<div id='pickSport' data-role="collapsible" data-theme="e">
<h3>Select Sport</h3>
<div id="sportsList">$sportlist</div>
</div>
<div id='schedMenu' data-role="collapsible" data-theme="e">
<h3>Scheduling Options</h3>
<ul data-role="listview" data-inset="true">
<li>Edit Current Schedule</li>
</ul>
</div>
<div id='sportsMenu' data-role="collapsible" data-theme="b">
<h3>Sports & Schedules</h3>
<div id="schoolSports">No School Selected Yet</div>
</div>
<div id="general" data-role="collapsible" data-theme="b">
<h3>General</h3>
<ul data-role="listview" data-inset="true">
<li style="display:$logged" class="logged"><a href='javascript:loadMyAccount();' data-transition="slide">My Account</a></li>
<li style="display:$logged" class="logged"><a href="#specifySports" data-transition="slide">Specify Sports</a></li>
<li style="display:$notlogged" class="notLogged"><a href="#signup" data-transition="slide">Sign Up</a></li>
<li><a href="faq.php?v=$ver" data-transition="slide">FAQs</a></li>
<li><a href="help.php?v=$ver" data-transition="slide">Help</a></li>
<li style="display:$logged" class="logged"><a href="javascript:logoffUser();" data-transition="slide">Logoff</a></li>
<li><a href="aboutus.php" data-transition="slide">About Us</a></li>
<li><a href="contactus.php" data-transition="slide">Contact Us</a></li>
<li><a href="#tandc" data-transition="slide">Terms and Conditions</a></li>
<li><a href="privacy_policy.php" data-transition="slide">Privacy Policy</a></li>
</ul>
</div>
</div>
<p>Copyright $yr by Michael A. Lewis<br>All Rights Reserved</p>
</div><!-- /content -->
</div><!-- /home page -->
END;
