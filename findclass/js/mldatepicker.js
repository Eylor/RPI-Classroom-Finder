//
//	Homegrown date picker
//	Copyright 2012 by Michael A. Lewis
//	All Rights Reserved
//
var Month;
var Year;
var Day;
var dateField;
var dateMonth;
function setDay(day,prev) {
	Day=day;
	if (prev==-1) prevMonth();
	if (prev==1) nextMonth();
	if (prev==0) drawMonth();
}
function prevMonth() {
	Month--;
	if (Month<0) {
		Month=11;
		Year--;
	}
	drawMonth();
}
function nextMonth() {
	Month++;
	if (Month>11) {
		Month=0;
		Year++;
	}
	drawMonth();
}
function getLastDay(mon) {
	var lma=[31,0,31,30,31,30,31,31,30,31,30,31];
	var day=lma[mon];
	if (day==0) {
		day=28;
		if (Year %4 == 0 && Year % 100!=0) day=29;
	}
	return day;
}
function drawMonth() {
	var theFirst = new Date(Year,Month,1);
	var dow=theFirst.getDay();
	var dowArray=["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
	var monthArray=["January","February","March","April","May","June","July","August","September","October","November","December"];
	var mName=monthArray[Month];;
	var mon=Month-1;
	var year=Year;
	if (mon<0) mon=0;
	var lastDay=getLastDay(mon,year);
	var lastDayCur=getLastDay(Month,Year);
	if (Day>lastDayCur) Day=lastDayCur;
	var startDay=0;
	var lastWeek=0;
	dateField.val((Month+1) + "/" + Day + "/" + Year);
	day=1;
	if(dow>0) {
		startDay=1;
		day=lastDay+1-dow;
	}
	var t='<table width=279 id="calendar" cellspacing="0" cellpadding="0"><tr><th align=center colspan=7><img class=nav src="images/law10.png" title="Previous Month" onclick="prevMonth();"><span style="font-size:15px">' + mName + " " + Year + '</span><img class=nav src="images/raw10.png" title="Next month"  onclick="nextMonth();"></th></tr><tr>';
	for (i=0;i<7;i++) {
		t+= '<th>' + dowArray[i] + "</th>";
	}
	t+="</tr>";
	for (i=0;i<6;i++) {
		t+="<tr>";
		for (j=0;j<7;j++) {
			if (startDay>0 || lastWeek>0) {
				if (startDay>0) {
					t+='<td class="calPrev" onclick="setDay(' + day + ',-1);">' + day + "</td>";
				} else {
					t+='<td class="calPrev" onclick="setDay(' + day + ',1);">' + day + "</td>";
				}
			} else {
				var cls="calNorm";
				if (day==Day) cls="calSel";
				t+='<td class="' + cls + '" onclick="setDay(' + day + ',0);">' + day + "</td>";
			}
			day++;
			if (startDay>0) {
				if (day>lastDay) {
					startDay=0;
					day=1;
				}
			} else {
				if (day>lastDayCur) {
					if (lastWeek>0 || j==6) break;
					lastWeek=1;
					day=1;
				}
			}
		}
		t+="</tr>";
		if (day>lastDayCur || lastWeek>0) break;
	}
	t+="</table>";
	dateMonth.html(t).trigger('create');
}
function initDate(fld,mon,year,day,dm) {
	dateField=$(fld);
	dateMonth=$(dm);
	if (mon==0) {
		var today=new Date();
		Day=today.getDate();
		Year=today.getFullYear();
		Month=today.getMonth();
		setDay(Day,0);
	} else {
		Month=mon;
		Year=year;
		setDay(day,0);
	}
}
