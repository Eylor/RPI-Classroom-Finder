<?php
//
// Send out the bug report in an email
//
$msg = $_POST['msg'];
$subject = "Bug Report Sent On ";
$date = date('m/d/Y h:i:s a', time());
$subject += $date;
mail("FindMyClass4.me@gmail.com",$subject,$msg);
echo "Mail Sent.";
