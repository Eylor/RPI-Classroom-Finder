<?php
//
//	Display aboutus information
//
$ver=$_GET['v'];
print <<< END
<div data-role="page" id="aboutus" data-title="About Us">
<div data-role="header" data-position="fixed" data-theme="b">
<a href="#home" data-icon="home" data-iconpos="notext"></a>
<h1>About Us</h1>
</div><!-- /header -->
<div data-role="content">
<p>We are RPI students who developed this project for use by the RPI community.</p>
</div><!-- /content -->
</div><!-- /about us-->
END;

