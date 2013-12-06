<?php

if (@$GLOBALS['NEWDBC']) require_once("new-dbc.php");
if (class_exists("DB")) 1;
else {

/*
	PHP Database access class
	Copyright 2004 by Xelent.net.
	All Rights Reserved.
*/
class DB {
	var $type;
	var $dsn;
	var $link;
	var $db;
	var $result;
	var $server;
	var $user;
	var $pass;
//
//	Create DB object.
//
function DB($db="") {
	include("localsettings.php");
	$this->type=1;
	$this->dsn="$server_name;$user;$password";
	$this->db=$database_name;
	if (strlen($db)) {
		$this->db=$db;
		switch(strtolower($db)) {
			case "email": break;
			case "findmyclass": break;
		}
	}
	if (strlen($this->dsn)>2) {
		list($this->server,$this->user,$this->pass)=explode(";",$this->dsn);
	} else {
		print "Error: No DSN defined.";
		exit;
	}
	switch($this->type) {
	case 1:
		$this->link=mysql_connect($this->server,$this->user,$this->pass) or die("Accessing ".$this->server.": ".mysql_error());
		mysql_select_db($this->db);
		break;
	}
}
//
//	Close connection
//
function Close() {
	switch($this->type) {
	case 1:
		mysql_close($this->link);
		break;
	}
}
//
//	Get Last ID
//
function LastID() {
	$res=$this->Query("select LAST_INSERT_ID()");
	$did=mysql_result($res,0,0);
	return $did;
}
//
//	Select DB for connection
//
function selectDB($database) {
	switch($this->type) {
	case 1:
		mysql_select_db($database,$this->link);
		break;
	}
}
//
//	Query
//
function Query($s) {
	switch($this->type) {
	case 1:
		$s=str_replace(" with(nolock) "," ",$s);
		$this->result=mysql_query($s,$this->link) or die(mysql_error());
		break;
	}
	return $this->result;
}
//
//	Execute SQL without returning results
//
function Exec($sql) {
	switch($this->type) {
	case 1:
		$sql=str_replace(" with(nolock) "," ",$sql);
		mysql_query($sql,$this->link) or die(mysql_error());
		return;
	}	
}
//
//	Return array sql data
//
function FetchArray() {
	switch($this->type) {
	case 1:
		$r=mysql_fetch_array($this->result);
		return $r;
	}	
}
//
//	Return row sql data
//
function FetchRow() {
	switch($this->type) {
	case 1:
		$r=mysql_fetch_row($this->result);
		return $r;
	}
}
//
//	Return num of results
//
function NumRows() {
	switch($this->type) {
	case 1:
		$r=mysql_num_rows($this->result);
		return $r;
	}
}
}

}
?>

