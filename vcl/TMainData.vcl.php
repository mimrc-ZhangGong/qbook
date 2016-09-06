<?php
if( !defined('IN') ) die('bad request');

define('SAE_MYSQL_HOST_M', '127.0.0.1');
define('SAE_MYSQL_PORT', '3306');
define('SAE_MYSQL_DB', 'passport');
define('SAE_MYSQL_USER', 'root');
define('SAE_MYSQL_PASS', '');

class TMainData
{
	public $conn;
	
	public function TMainData()
	{
		global $DBConnection;
		$DBConnection = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
		if(!$DBConnection)
		  die('Could not connect: ' . mysql_error());
		$db_selected = mysql_select_db(SAE_MYSQL_DB, $DBConnection);
		if (!$db_selected)
		  die ('Can\'t use database : ' . mysql_error());
		$this->conn = $DBConnection;
	}
}
?>