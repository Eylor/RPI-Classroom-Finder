if (0) {
$(document).bind("pagebeforeshow", function (event, data) {
	if (loggingIn==0 && typeof data.toPage!='object' && !(data.toPage == '#login' || data.toPage=='#logoff')) {
		if (userId==0) {
			handleUserData(0);
		} else {
			if (userId=="") {
				event.preventDefault();
				$.get('fincrime/getuid.php','x=' + SESSID,handleUserData).error(errUidFunc);
				return false;
			}
		}
	} 
});
