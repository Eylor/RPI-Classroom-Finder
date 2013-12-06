<?php
//
//	Display FAQ information
//
if (!isset($_GET['v'])) exit;
$ver=str_replace(array(';','\r','\n','!','<','>','+','&','|'),"",$_GET['v']);
include("session.php");
include("common.php");
$uid=0;
if (isset($_SESSION['valid'])) $uid=$_SESSION['valid'];
$icon=$whatcart="";
print <<< END
<div data-role="page" id="faq" data-title="FAQs">
<div data-role="header" data-position="fixed" data-theme="b">
<a href="#home" data-icon="home" data-iconpos="notext"></a>
<h1>FAQs</h1>
</div><!-- /header -->
<div data-role="content">
<div data-role="collapsible-set">
<div data-role="collapsible" data-theme="b">
<h3>Getting Started</h3>
<h4>How Do I Get Started</h4>
Sign up, upload your CRNs for your schedule and click Find my Class
You can also just search for a class or search for a building
<h4>How Do I Find a Class?</h4>
Type your class into the search bar with either the prefix, crn, title, or professor
</div>
<div data-role="collapsible" data-theme="b">
<h3>Schedule Stuff</h3>
<h4>How Do I Upload My Class Schedule?</h4>
Type in your Crns
<h4>How Do I Change My Schedule?</h4>
</div>
<div data-role="collapsible" data-theme="b">
<h3>General</h3>
<h4>Sometimes things don't work, why not?</h4>
You are using version $ver of our application. This is a beta version and may present you 
with errors, not work etc. It is under active development and things that work one moment may not work the next. That being said, any and all feedback is appreciated.
<h4>Everything is messed up and ugly after I refresh the page. Why?</h4>
Don't refresh the page! Our software is mobile-oriented and attempts to mimic desktop and native mobile apps using a standard web page. We use very sophisticated techniques to do this. A 
trade-off of this is that refreshing a page screws everything up. Sorry. Instead of refreshing a page, tap or click the back button in your browser and then reselect whatever you wanted to refresh. Note that you <i>can</i> refresh the home page 
and it will look nice, but that's about it.
<h4>The activity spinner goes forever. WTH?</h4>
This means there has been an internal error or (less likely) or that you refreshed the page (more likely). See previous question and answer. Some pages are merely ugly when refreshed, others 
deal with the refresh less nicely. The only page you can really safely refresh is the home page.
</div>
</div>
</div><!-- /content -->
</div><!-- /faq-->

END;

