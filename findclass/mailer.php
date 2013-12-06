<?php
/*
function SendMail($From, $FromName, $To, $ToName, $Subject, $Text, $Html, $AttmFiles)
$From      ... sender mail address like "my@address.com"
$FromName  ... sender name like "My Name"
$To        ... recipient mail address like "your@address.com"
$ToName    ... recipients name like "Your Name"
$Subject   ... subject of the mail like "This is my first testmail"
$Text      ... text version of the mail
$Html      ... html version of the mail
$AttmFiles ... array containing the filenames to attach like array("file1","file2")
*/

function SendMail($From,$FromName,$To,$ToName,$Subject,$Text,$Html,$AttmFiles){
	$OB="----=_OuterBoundary_000";
	$IB="----=_InnerBoundery_001";
	$Html=$Html?$Html:preg_replace("/\n/","<br>",$Text)  or die("neither text nor html part present.");
	$Text=$Text?$Text:"Sorry, but you need an html mailer to read this mail.";
	$From or die("sender address missing");
	$To or die("recipient address missing");

	$headers ="MIME-Version: 1.0\r\n"; 
	$headers.="Return-Path: mail.xelent.net\r\n";
	$headers.="From: ".$FromName." <".$From.">\n"; 
	$headers.="Reply-To: ".$FromName;
	$headers.=" <$From>\n";
	$headers.="X-Priority: 3\n"; 
	$headers.="X-MSMail-Priority: High\n"; 
	$headers.="X-Mailer: xelent.net Mailer\n"; 
	$headers.="Content-Type: multipart/mixed;\n\tboundary=\"".$OB."\"\n";
//Messages start with text/html alternatives in OB
	$Msg ="This is a multi-part message in MIME format.\n";
	$Msg.="\n--".$OB."\n";
	$Msg.="Content-Type: multipart/alternative;\n\tboundary=\"".$IB."\"\n\n";

//plaintext section 
	$Msg.="\n--".$IB."\n";
	$Msg.="Content-Type: text/plain;\n\tcharset=\"iso-8859-1\"\n";
	$Msg.="Content-Transfer-Encoding: quoted-printable\n\n";
// plaintext goes here
	$Msg.=$Text."\n\n";

// html section 
	$Msg.="\n--".$IB."\n";
	$Msg.="Content-Type: text/html;\n\tcharset=\"iso-8859-1\"\n";
	$Msg.="Content-Transfer-Encoding: base64\n\n";
// html goes here 
	$Msg.=chunk_split(base64_encode($Html))."\n\n";

// end of IB
	$Msg.="\n--".$IB."--\n";

// attachments
if($AttmFiles){
	foreach($AttmFiles as $AttmFile){
		$patharray = explode ("/", $AttmFile); 
		$FileName=$patharray[count($patharray)-1];
		$Msg.= "\n--".$OB."\n";
		$Msg.="Content-Type: application/octetstream;\n\tname=\"".$FileName."\"\n";
		$Msg.="Content-Transfer-Encoding: base64\n";
		$Msg.="Content-Disposition: attachment;\n\tfilename=\"".$FileName."\"\n\n";

 //file goes here
		$fd=fopen ($AttmFile, "r");
		$FileContent=fread($fd,filesize($AttmFile));
		fclose ($fd);
		$FileContent=chunk_split(base64_encode($FileContent));
		$Msg.=$FileContent;
		$Msg.="\n\n";
	}
}

//message ends
	$Msg.="\n--".$OB."--\n";
	mail($ToName." <".$To.">",$Subject,$Msg,$headers); 
//syslog(LOG_INFO,"Mail: Message sent to $ToName <$To>");
}
?>

