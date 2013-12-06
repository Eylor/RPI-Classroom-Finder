<?php
//
//	Get a page from a site
//
function webPage($url) {
	$cookie="goog.txt";
	$useragent="Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 5.1; SV1; .NET CLR 3.5.50727; .NET CLR 4.0.04506.30)";
	
	$pa=array("208.77.220.135","85.17.58.23","50.7.19.82","*");
	$cr = curl_init($url);
	curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);       // Get returned value as string (dont put to screen)
	curl_setopt($cr, CURLOPT_USERAGENT, $useragent);      // Spoof the user-agent to be the browser that the user is on (and accessing the php $
	curl_setopt($cr, CURLOPT_COOKIEJAR, $cookie);	      // Use cookie.txt for STORING cookies
	curl_setopt($cr, CURLOPT_COOKIEFILE, $cookie);        // Use cookie.txt for STORING cookies
	$proxy=$pa[floor(mt_rand(0,count($pa)-1))];
	if (0 && $proxy!="*") {
		curl_setopt($cr, CURLOPT_PROXY, "http://$proxy:1080)");   // use a proxy
		curl_setopt($cr, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
	}
	curl_setopt($cr, CURLOPT_TIMEOUT,30);
	$page = curl_exec($cr);
	curl_close($cr);
	return $page;
}
//
//	Post Information to a site
//
function postInfo($url,$data) {
	$cookie="goog.txt";
	$useragent="Mozilla/5.0 (compatible; MSIE 7.0; Windows NT 5.1; SV1; .NET CLR 3.5.50727; .NET CLR 4.0.04506.30)";

	$pa=array("208.77.220.135","85.17.58.23","50.7.19.82","*");
	$cr = curl_init($url);
	curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);       // Get returned value as string (dont put to screen)
	curl_setopt($cr, CURLOPT_USERAGENT, $useragent);      // Spoof the user-agent to be the browser that the user is on (and accessing the php $
	curl_setopt($cr, CURLOPT_COOKIEJAR, $cookie);	      // Use cookie.txt for STORING cookies
	curl_setopt($cr, CURLOPT_COOKIEFILE, $cookie);        // Use cookie.txt for STORING cookies
	curl_setopt($cr, CURLOPT_POST, false);  		// Tell curl that we are posting data
	curl_setopt($cr, CURLOPT_POSTFIELDS, $data);          // Post the data in the array above
	$proxy=$pa[floor(mt_rand(0,count($pa)-1))];
	if (0 && $proxy!="*") {
		curl_setopt($cr, CURLOPT_PROXY, "http://$proxy:1080)");   // use a proxy
		curl_setopt($cr, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
	}
	$output = curl_exec($cr);
	curl_close($cr);
}

