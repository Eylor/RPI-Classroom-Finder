<?php
//
//	Output the mobile page header
//
function getMobileHeader() {
	$txt=<<< END
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Find My Class For Me</title>
<meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1, maximum-scale=2, minimum-scale=.25">
<link rel="stylesheet" href="/css/theme-f.css" />
<link rel="stylesheet" href="/css/theme-g.css" />
<link rel="stylesheet" href="/css/mldatepicker.css" />
<link rel="stylesheet" href="/jquery.mobile-1.2.0-beta.1.min.css" />
<script src="/js/jquery.min.js"></script>
<script src="/js/mldatepicker.js"></script>
<base href="http://www.findmyclass4.me/">
<script src="/js/jquery.mobile-1.2.0-beta.1.min.js"></script>
<script type="text/javascript" src="/js/jquery.mobile.message.min.js"></script>
<script type="text/javascript" src="/js/cookie.js"></script>
<!--<script src="http://www.openlayers.org/dev/OpenLayers.js"></script>
<script src="http://www.openstreetmap.org/openlayers/OpenStreetMap.js"></script>-->
<style>
.ui-header .ui-title,.ui-footer .ui-title {
        margin-right: 0px !important; margin-left: 0px !important;
}
</style>
END;
	return $txt;
}

