<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

class Database
{
	private $link = NULL;
	private $last_query = NULL;
	private $query_count = 0;
	
	/**
	 * Construct
	 * Easily connect to the database.
	 */
	public function __construct($server='',$user='',$pass='',$dbname='')
	{
		if(!empty($server))
		{
			$this->connect($server,$user,$pass);
			$this->selectdb($dbname);
		}
	}
	
	/**
	 * Destruct.
	 * Auto-close the connection.
	 */
	public function __destruct()
	{
		$this->close();
	}
	
	/**
	 * Connect
	 * Connect to the MySQL server.
	*/
	public function connect($server,$user,$pass)
	{
		$this->link = mysql_connect($server,$user,$pass) or $this->halt();
	}
	
	public function close()
	{
		mysql_close($this->link);
	}
	
	/**
	 * Select Database
	 * Select the database to use.
	 */
	public function selectdb($dbname)
	{
		mysql_select_db($dbname,$this->link);
	}
	
	public function query($query)
	{
		$result = mysql_query($query,$this->link) or $this->halt($query);
		$this->last_query = $query;
		$this->query_count++;
		return $result;
	}
	
	public function fetcharray($result)
	{
		$result = mysql_fetch_array($result); // or $this->halt();
		return $result;
	}
	
	public function es($string)
	{
		return $this->escapestring($string);
	}
	
	public function escapestring($string)
	{
		return mysql_real_escape_string($string);
	}
	
	public function numrows($result)
	{
		return mysql_num_rows($result);
	}
	
	public function queryfirst($query)
	{
		return $this->fetcharray($this->query($query));
	}
	
	private function errno()
	{
		return mysql_errno($this->link);
	}
	
	private function error()
	{
		return mysql_error($this->link);
	}
	
	private function halt($query=NULL)
	{
		error('Database','#'.$this->errno().': '.$this->error());
	}
}
?>