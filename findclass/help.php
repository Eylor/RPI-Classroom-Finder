<?php
//
//	Display help information
//
include("session.php");
include("common.php");
$uid=0;
if (isset($_SESSION['valid'])) $uid=$_SESSION['valid'];
$icon="sCart";
$whatcart="Shopping Cart";
$cstr="Checkout";
print <<< END
<div data-role="page" id="help" data-title="Help Information">
<div data-role="header" data-position="fixed" data-theme="b">
<a href="#home" data-icon="home" data-iconpos="notext"></a>
<h1>Help Information</h1>
</div><!-- /header -->
<div data-role="content">
<h4>Logging Out</h4>
<p>You can logout by selecting the <i>General</i> menu option on the main screen and then tap/click the <i>Logoff</i> option. You must then login again before you can perform any other 
functions.</p>
<h4>How Do I Do Stuff</h4>
<p>Tapping or clicking <i>FAQ</i> button on the home page has lots of information about how to do things.</p>
</div><!-- /content -->
</div><!-- /help-->
END;

