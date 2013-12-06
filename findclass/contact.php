<?php
//
//	Display contact information
//	Copyright 2012 by ICG, Inc.
//	All Rights Reserved
//
include("session.php");
include("common.php");
$uid=0;
if (isset($_SESSION['valid'])) $uid=$_SESSION['valid'];
print <<< END
<div data-role="page" id="contact" data-title="Contact Us">
<div data-role="header" data-position="fixed" data-theme="b">
<a href="#home" data-icon="home" data-iconpos="notext"></a>
<h1>Contact Us</h1>
</div><!-- /header -->
<div data-role="content">
<h3>email: support@findmyclass4.me</h3>
</div><!-- /content -->
</div><!-- /contact us -->
END;

