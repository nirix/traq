<?php
/**
 * Meridian
 * Copyright (C) 2010-2011 Jack Polgar
 * 
 * This file is part of Meridian.
 * 
 * Meridian is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 * 
 * Meridian is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Meridian. If not, see <http://www.gnu.org/licenses/>.
 */

require 'mysqli_query.php';
require 'mysqli_statement.php';

/**
 * MySQL Impoved database handler
 * @package Meridian
 * @subpackage Database
 */
class DB_MySQLi
{
	private static $_instance;
	public $prefix;
	
	public function __construct(array $config)
	{
		self::$_instance = $this;
		$this->prefix = @$config['prefix'];
		$this->connect($config['host'], $config['user'], $config['pass'])->selectDb($config['dbname']);
		return $this;
	}
	
	public function connect($host, $user, $pass)
	{
		$this->link = mysqli_connect($host, $user, $pass) or $this->halt();
		return $this;
	}
	
	public function selectDb($dbname)
	{
		mysqli_select_db($this->link, $dbname) or $this->halt();
		return $this;
	}
	
	public function select($cols = array('*'))
	{
		return new MySQLi_Query("SELECT", (is_array($cols) ? $cols : func_get_args()));
	}
	
	public function delete()
	{
		return new MySQLi_Query("DELETE", array());
	}
	
	public function insert(array $data)
	{
		return new MySQLi_Query("INSERT INTO", $data);
	}
	
	public function query($query)
	{
		$this->last_query = $query;
		$result = mysqli_query($this->link, (string) $query) or $this->halt();
		return new MySQLi_Statement($result);
	}
	
	public function real_escape_string($string)
	{
		return mysqli_real_escape_string($this->link, $string);
	}
	public function res($string)
	{
		return $this->real_escape_string($string);
	}
	
	public static function getInstance()
	{
		return self::$_instance;
	}
	
	public function link()
	{
		return $this->link;
	}
	
	public function halt()
	{
		Meridian::error('Database Error', mysqli_error($this->link).'<blockquote>'.$this->last_query.'</blockquote>');
	}
}