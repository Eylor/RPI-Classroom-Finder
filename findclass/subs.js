var helpWin;
var newWin;

function over() {
        srcElement=window.event.srcElement;
        srcElement.style.color="rgb(0,0,255)";
}

function out() {
        srcElement=window.event.srcElement;
        srcElement.style.color="rgb(0,0,0)";
}

function scrWidth() {
	nn=(document.layers ? true : false);
	return (nn ? innerWidth : screen.availWidth);
}

function scrHeight() {
	nn=(document.layers ? true : false);
	return (nn ? innerHeight : screen.availHeight);
}

function popUpW(URL, w, h) {
	day = new Date();
	id = day.getTime();
	l=(scrWidth()-w-30)/2;
	t=(scrHeight()-h)/2-18;
	eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=' + w + ',height=' + h + ',left = ' + l + ',top =' +  t)");
}

function popUpFull(URL, w, h) {
	day = new Date();
	id = day.getTime();
	l=(scrWidth()-w-30)/2;
	t=(scrHeight()-h)/2-18;
	eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=1,scrollbars=1,location=1,statusbar=1,status=yes,menubar=1,resizable=1,width=' + w + ',height=' + h + ',left = ' + l + ',top =' +  t)");
}

function popUpFullScr(URL) {
	day = new Date();
	id = day.getTime();
	w=scrWidth();
	h=scrHeight();
	t=0;
	l=0;
	eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=1,scrollbars=1,location=1,statusbar=1,status=yes,menubar=1,resizable=1,width=' + w + ',height=' + h + ',left = ' + l + ',top =' +  t)");
}

function popUpHelp(fn,w,h,s) {
	l=(scrWidth()-w-63)/2;
	t=(scrHeight()-h)/2;
	URL="help/" + fn + ".html";
	if (helpWin == null || helpWin.closed) {
		helpWin=open(URL,'output','scrollbars=' + s + ',toolbar=0,location=0,statusbar=0,menubar=0,resizable=1,width=' + 
		w + ',height=' + h + ',left = ' + l + ',top =' +  t);
	}
	else {
		helpWin.location.href=URL;
	}
	helpWin.focus();
}

function newWindow(file,w,h,s) {
	if (w>0) {
		l=(scrWidth()-w-63)/2;
	}
	else {
		l=scrWidth();
	}
	if (h>0) {
		t=(scrHeight()-h-100)/2;
	}
	else {
		t=scrHeight();
	}
	newWin=open(file,'output','scrollbars=' + s + 
',toolbar=1,location=0,statusbar=0,menubar=1,resizable=1,width=' + 
		w + ',height=' + h + ',left = ' + l + ',top =' +  t);
	newWin.focus();
}

function newBrowse(file,w,h,s) {
	if (w>0) {
		l=(scrWidth()-w-63)/2;
	}
	else {
		l=scrWidth();
	}
	if (h>0) {
		t=(scrHeight()-h)/2;
	}
	else {
		t=scrHeight();
	}
	newBro=open(file,'output','scrollbars=' + s + ',toolbar=0,location=0,statusbar=0,menubar=0,resizable=1,width=' + 
		w + ',height=' + h + ',left = ' + l + ',top =' +  t);
	newBro.focus();
}
function showtip(current,e,txt) {
	if (document.layers) {
		theString="<DIV CLASS='ttip'>"+txt+"</DIV>";
		document.tooltip.document.write(theString);
		document.tooltip.document.close();
		document.tooltip.left=e.pageX+14;
		document.tooltip.top=e.pageY+2;
		document.tooltip.visibility="show";
 	}
	else {
        	if(document.getElementById) {
			elm=document.getElementById("tooltip");
			elml=current;
			elm.innerHTML=txt;
			elm.style.height=elml.style.height;
			elm.style.top=parseInt(e.y+elml.offsetHeight-80);
			elm.style.left=parseInt(e.x-80);
			elm.style.visibility = "visible";
		}
	}
}
function hidetip(){
	if (document.layers) {
		document.tooltip.visibility="hidden";
	}
	else {
		if(document.getElementById) {
			elm.style.visibility="hidden";;
		}
	} 
}
