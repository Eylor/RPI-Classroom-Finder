<?php
//
//	Privacy policy statement
//
include("session.php");
include("common.php");
$uid=0;
if (isset($_SESSION['valid'])) $uid=$_SESSION['valid'];
$icon=$whatcart="";
$CNS="$website";
$CNSU=strtoupper($CNS);
$RM="25px";
$LM="25px";
if ($_SESSION['is_mobile']>0) {
	$RM="5px";
	$LM="5px";
}
print <<< END
<div data-role="page" id="privacyPolicy" data-title="Privacy Policy">
<div data-role="header" data-position="fixed" data-theme="b">
<a href="#home" data-icon="home" data-iconpos="notext"></a>
<h1>$website Privacy Policy</h1>
<style>
.item{margin-left:$LM;margin-right:$RM;text-indext:0px;}
</style>
</div><!-- /header -->
<div data-role="content">
<center><table width=95% cellpadding=0 cellspacing=0>
<br><tr><td><html><table width="100%">
      <tr>
        <td><b><font face="Arial" color="#679b99">COMMITMENT TO YOUR PRIVACY</font></b>
<br>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>This site is owned and 
        operated by $CNS. 
        Your privacy on the Internet is of the utmost importance to us. At $CNS, 
        we want to make your experience online satisfying and safe.</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>Because we gather certain 
        types of information about our users, we feel you should fully 
        understand the terms and conditions surrounding the capture and use of 
        that information. This privacy statement discloses what information we 
        gather and how we use it.</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>INFORMATION $CNSU GATHERS 
        AND TRACKS</b></font></p>
        <p class=item>
        <b><font face="Arial" color="#679b99" size="2">$CNS</font></b><font face="Arial" color="#679b99" size="2"><b> 
        gathers two types of information about users:</b></font></p>
          <ul>
            <li>
            <font face="Arial" color="#679b99" size="2"><b>Information that 
            users provide through optional, voluntary submissions. These are 
            voluntary submissions in order to use our system.</b></font></li>
            <li>
            <font face="Arial" color="#679b99" size="2"><b>Information $CNS 
            gathers through aggregated tracking information derived mainly by 
            tallying page views and queries throughout our sites. This information allows us 
            to better tailor our content and to help better 
            understand the requirements of our users. $CNS does not divulge any information about an individual user to a 
            non-affiliated third party.</b></font></li>
          </ul>
        <p class=item>
        <font face="trebuchet ms, Arial, Helvetica"><b>
        <font face="Arial" color="#679b99" size="2">$CNS</font></b><font face="Arial" color="#679b99" size="2"><b> 
        Gathers User Information In The Following Processes:</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>Optional Voluntary 
        Information</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>From time to time $CNS 
        may alert users of the web site to policy changes or general information 
        through Email or other means.</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>Children</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>Consistent with the 
        Federal Children's Online Privacy Protection Act of 1998 (COPPA), it is required that parents or legal guardians sign-up children 
under the age of 13 for use of this site.</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>Usage tracking</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>$CNS tracks user traffic 
        patterns throughout all of our sites.</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>$CNS sometimes tracks and 
        catalogs the search terms that users enter in our various Search functions. 
        We use tracking information to determine site usage and in some cases for billing information</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>Cookies</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>We may place a text file 
        called a &quot;cookie&quot; in the browser files of your computer. The cookie 
        itself does not contain Personal Information although it will enable us 
        to relate your use of this site to information that you have 
        specifically and knowingly provided. But the only personal information a 
        cookie can contain is information you supply yourself. A cookie can't 
        read data off your hard disk or read cookie files created by other 
        sites. $CNS uses cookies to track user traffic patterns (as described 
        above).</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>You can refuse cookies by 
        turning them off in your browser. If you've set your browser to warn you 
        before accepting cookies, you will receive the warning message with each 
        cookie. You MUST have cookies turned on to use this site.</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>USE OF INFORMATION</b></font></p>
        <p class=item>
        <b><font face="Arial" color="#679b99" size="2">$CNS</font></b><font face="Arial" color="#679b99" size="2"><b> 
        uses any information voluntarily given by our users to enhance their 
        experience in our network of sites, whether to provide interactive or 
        personalized elements on the sites or to better prepare future content 
        based on the interests of our users.</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>As stated above, we use 
        information that users voluntarily provide in order to send out general 
        information as well as policy changes.</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>$CNS creates aggregate 
        reports on user traffic patterns for our own internal 
        use. This allows us to more effectively manage the site. We will not 
        disclose any information about any individual user except to comply with 
        applicable law or valid legal process or to protect the personal safety 
        of our users or the public.</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>SHARING OF THE 
        INFORMATION</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>$CNS uses the 
        above-described information to tailor our content to suit your needs and 
        help us better understand our audience's demographics. This is essential 
        to providing the best possible service that we can. We will not share 
        information about individual users with any non-affiliated third party, 
        except to comply with applicable law or valid legal process or to 
        protect the personal safety of our users or the public.</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>SECURITY</b></font></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b>$CNS uses industry 
        standard password protection and other standard system level protections 
        to protect your personal information. Our security and privacy policies 
        are periodically reviewed and enhanced as necessary and only authorized 
        individuals have access to the information provided by our customers.&nbsp;</b></font></p>
        <p class=item><b>
        <font face="Arial" color="#679b99" size="2">YOUR CONSENT</font></b></p>
        <p class=item>
        <font face="Arial" color="#679b99" size="2"><b><br>
        By using this site, you consent to the collection and use of this 
        information by $CNS. If we decide to change our privacy policy, we will 
        post those changes on this page so that you are always aware of what 
        information we collect, how we use it, and under what circumstances we 
        disclose it.</b></font></p>

<p class=item>
<font face="Arial" color="#679b99" size="2"><b>INTERNATIONAL DATA TRANSFERS:</p></p>
<p class=item>
<font face="Arial" color="#679b99" size="2">Information that we collect may be stored and processed in and transferred between any of 
the countries in which we operate in order to enable us to use the 
information in accordance with this Privacy Policy.</p>
<p class=item>
<font face="Arial" color="#679b99" size="2">If you are in the European Economic Area (EEA), information which you provide will be 
transferred to countries (including the United States) which do not have data 
protection laws equivalent to those in force in the EEA.</p>
<p class=item>
<font face="Arial" color="#679b99" size="2">You expressly agree to such transfers, storage and processing of personal 
information.</p>
<p class=item>
<font face="Arial" color="#679b99" size="2">$CNS reserves the right to amend this Privacy Policy at any time without notice. If we 
decide to change this Privacy Policy, we will post those changes here so that 
you will always know how we will use the information that is collected about you.</p>
</td></tr></table>
</center>
</div><!-- /content -->
</div><!-- /privacypolicy-->
END;

