<?php 
//
//	Session handler with MySQL Backbone
//	You Must include this file BEFORE any session variable references. It does the SESSION_START() call
//	for you.
//
//	configuration information
//
$m_host = "localhost";		//MySQL Host 
$m_user = "root";		//MySQL User 
$m_pass = "";			//MySQL Pass 
$m_db   = "sessions";		//MySQL Database 

$session_connection="";
$session_expire=86400*30;
$session_read=array();
//
//	Open function; Opens/starts session 
//	Opens a connection to the database and stays open until specifically closed 
//	This function is called first and with each page load */ 
// 
function open ($s,$n) { 
	global $session_connection, $m_host, $m_user, $m_pass, $m_db; 
	$session_connection = mysql_connect($m_host,$m_user,$m_pass); 
	mysql_select_db($m_db,$session_connection); 
	return true; 
} 
//
//	Read function; downloads data from repository to current session 
//	Queries the mysql database and returns data. 
//	This function is called after 'open' with each page load.
//
function read ($id) { 
	global $session_connection,$session_read,$session_expire;
	$query = "SELECT data FROM sessions.sessions WHERE id='$id'"; 
	$res = mysql_query($query,$session_connection); 
	if(mysql_num_rows($res) != 1) return "";
	$session_read = mysql_fetch_assoc($res); 
	$expire = time() + $session_expire;
	mysql_query("update sessions.sessions set access='$expire' where id='$id'",$session_connection);
	return stripslashes($session_read["data"]); 
} 
//
//	Write function; uploads data from current session to repository 
//	Inserts/updates mysql records of current session. Called after 'read' 
//	with each page load
//
function write ($id,$data) { 
	if (!$data) return false;
  	global $session_connection, $session_read, $session_expire; 

	$expire = time() + $session_expire; 
//  $data = mysql_real_escape_string($data); 
	$data=addslashes($data);
	if($session_read) {
		$query = "UPDATE sessions.sessions SET data='$data', access='$expire' WHERE id='$id'"; 
	} else {
		$query = "INSERT INTO sessions.sessions (id,access,data) values ('$id','$expire','$data')"; 
	}
	mysql_query($query,$session_connection); 
	return true; 
} 
// 
//	Close function; closes session 
//	 - closes mysql connection
//
function close () { 
	global $session_connection; 
	mysql_close($session_connection); 
	return true; 
} 
//
//	destroy function; deletes session data 
//	deletes records of current session. called ONLY when function 'session_destroy()' 
//	is called
//
function destroy ($id) { 
	global $session_connection; 
	$query = "DELETE FROM sessions.sessions WHERE id='$id'"; 
	mysql_query($query,$session_connection); 
	return true; 
} 
// 
//	gc function; cleans expired sessions 
//	deletes all rows where access < time(); called with a $gc_probability chance of executing
//
function gc ($expire) { 
	global $session_connection; 
	$query = "DELETE FROM sessions.sessions WHERE access < ".time(); 
	mysql_query($query,$session_connection); 
} 
// 
//	Set custom handlers 
//
session_set_save_handler ("open", "close", "read", "write", "destroy", "gc"); 
session_start(); 

