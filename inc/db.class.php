<?php
/**
 * Traq 2
 * Copyright (C) 2009, 2010 Jack Polgar
 *
 * This file is part of Traq.
 * 
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 * 
 * Traq is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Traq. If not, see <http://www.gnu.org/licenses/>.
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
	
	/**
	 * Query
	 * Query the selected Database.
	 * @param string $query The query to run.
	 */
	public function query($query)
	{
		$result = mysql_query($query,$this->link) or $this->halt($query);
		$this->last_query = $query;
		$this->query_count++;
		return $result;
	}
	
	/**
	 * Fetch Array
	 * Returns an array that corresponds to the fetched row.
	 */
	public function fetcharray($result)
	{
		$result = mysql_fetch_array($result); // or $this->halt();
		return $result;
	}
	
	/**
	 * Escape String
	 * Escapes the string.
	 * @deprecated
	 */
	public function escapestring($string)
	{
		return mysql_escape_string($string);
	}
	
	/**
	 * Escape String Shortcut
	 * Shortcut for escapestring
	 */
	public function es($string)
	{
		return $this->escapestring($string);
	}
	
	/**
	 * Insert ID
	 * Used to get the last inserted row ID.
	 */
	public function insertid()
	{
		return mysql_insert_id();
	}
	
	/**
	 * Num Rows
	 * Get number of rows in result.
	 */
	public function numrows($result)
	{
		return mysql_num_rows($result);
	}
	
	/**
	 * Real Escape String
	 * Escapes the string, making it safe for use in queries.
	 */
	public function res($string)
	{
		return mysql_real_escape_string($string,$this->link);
	}
	
	/**
	 * Query First
	 * Returns an array of the first row from the query result.
	 * @param string $query The query.
	 */
	public function queryfirst($query)
	{
		return $this->fetcharray($this->query($query));
	}
	
	// MySQL Error number
	private function errno()
	{
		return mysql_errno($this->link);
	}
	
	// MySQL Error
	private function error()
	{
		return mysql_error($this->link);
	}
	
	// Used to display the error
	private function halt($query=NULL)
	{
		error('Database','#'.$this->errno().': '.$this->error());
	}
}
?>