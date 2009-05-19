<?php
/**
 * Database Class
 * @author Jack Polgar <xocide@gmail.com>
 * @copyright Copyright (c)2009 Jack Polgar
 * @version 1.0
 * Copyright (c) 2009, Jack Polgar
 * All rights reserved.
 */
class Database {
	private $link = NULL;
	public $prefix = '';
	
	public function __construct() {
	}
	
	public function connect($host,$user,$pass) {
		$this->link = @mysql_connect($host,$user,$pass) or $this->halt();
	}
	
	public function selectdb($name) {
		return @mysql_select_db($name,$this->link) or $this->halt();
	}
	
	public function query($query) {
		$this->lastquery = $query;
		$result = @mysql_query($query) or $this->halt();
		return $result;
	}
	
	public function numrows($result) {
		$count = mysql_num_rows($result);
		return $count;
	}
	
	public function fetcharray($result) {
		$rows = mysql_fetch_array($result);
		return $rows;
	}
	
	public function escapestring($string) {
		return mysql_escape_string($string);
	}
	
	public function insertid() {
		return mysql_insert_id();
	}
	
	public function error() {
		return mysql_error();
	}
	public function errno() {
		return mysql_errno();
	}
	private function halt() {	
		error("Database","Error ".$this->errno().": <code>".$this->error()."<br />".$this->lastquery."</code>");
	}
}
?>