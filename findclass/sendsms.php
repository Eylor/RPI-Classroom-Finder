<?php
	
	$cookie="sms.txt";
	$useragent="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)";
//
//	Send a SMS message ($msg) to a list of groups ($ga array) and/or Users ($ua array) from user name ($username)
//	$usesig=1 if use the signature line
//	
function sendSMS($username,$msg,$usesig,$ga,$ua) {
	$dh=new DB("sms");
	$dh->Query("select * from sms.cred where username='$username'");
	if ($dh->NumRows()==0) return "No Such User";
	$r=$dh->FetchArray();
	$uname=stripslashes($r['username']);
	$pw=stripslashes($r['password']);
	$to="";
	$sep="";
	for ($i=0;$i<count($ga);$i++) {
		$to.="$sep$ga[$i]";
		$sep=";";
	}
	for ($i=0;$i<count($ua);$i++) {
		$to.="$sep$ua[$i]";
		$sep=";";
	}
	$data=array(
	  "username" => $uname,
	  "password" => $pw,
	  "msg"      => $msg,
	  'silent'   => $usesig,
	  'to'       => $to,
	  "SUBMIT" => 'true'
	);
	return postInfo("https://nd.xelent.net/utils/smsput.php",$data);
}

//
//	Get a page from a site
//
function webPage($url) {
	global $useragent,$cookie;
	
	$cr = curl_init($url);
	curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);       // Get returned value as string (dont put to screen)
	curl_setopt($cr, CURLOPT_USERAGENT, $useragent);      // Spoof the user-agent to be the browser that the user is on (and accessing the php $
	curl_setopt($cr, CURLOPT_COOKIEJAR, $cookie);	      // Use cookie.txt for STORING cookies
	curl_setopt($cr, CURLOPT_COOKIEFILE, $cookie);        // Use cookie.txt for STORING cookies
//	curl_setopt($cr, CURLOPT_PROXY, "http://192.168.1.95:8080)");   // use a proxy
	curl_setopt($cr, CURLOPT_TIMEOUT,30);
	$page = curl_exec($cr);
	curl_close($cr);
	return $page;
}
//
//	Post Information to a site
//
function postInfo($url,$data) {
	global $useragent,$cookie;

	$cr = curl_init($url);
	curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);       // Get returned value as string (dont put to screen)
	curl_setopt($cr, CURLOPT_USERAGENT, $useragent);      // Spoof the user-agent to be the browser that the user is on (and accessing the php $
	curl_setopt($cr, CURLOPT_COOKIEJAR, $cookie);	      // Use cookie.txt for STORING cookies
	curl_setopt($cr, CURLOPT_COOKIEFILE, $cookie);        // Use cookie.txt for STORING cookies
	curl_setopt($cr, CURLOPT_POST, true);                 // Tell curl that we are posting data
	curl_setopt($cr, CURLOPT_POSTFIELDS, $data);          // Post the data in the array above
	curl_setopt($cr, CURLOPT_TIMEOUT,30);
	curl_setopt($cr, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($cr, CURLOPT_SSL_VERIFYHOST,  false);
//	curl_setopt($cr, CURLOPT_PROXY, "http://192.168.1.95:8080)");   // use a proxy
	$output = curl_exec($cr);
	curl_close($cr);
	return $output;
}
?>

