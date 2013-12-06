<?php
//
//	Toggle product item on menu
//	Copyright 2010 by Michael Lewis
//	All Rights Reserved
//
if (!isset($_GET['seid'])) exit;
include("../dbc.php");
$dh=new DB();
$dh1=new DB();
$mid=$_GET['mid'];
$pid=$_GET['pid'];
$cid=$_GET['cid'];
$scid=$_GET['scid'];
$dh->Query("select id from product2menu where prod_id='$pid' && menu_id='$mid'");
if ($dh->NumRows()) {
	list($id)=$dh->FetchRow();
	$dh->Exec("delete from product2menu where id='$id'");
	print "0";
	exit;
}
$dh->Exec("insert into product2menu (prod_id,menu_id,cat_id,subcat_id) values('$pid','$mid','$cid','$scid')");
print "1";
exit;

