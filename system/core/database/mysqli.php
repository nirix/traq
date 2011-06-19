<?php
/**
 * Avalon
 * Copyright (C) 2011 Jack Polgar
 * 
 * @license http://opensource.org/licenses/BSD-3-Clause BSD License
 */

require 'mysqli_query.php';
require 'mysqli_statement.php';

class Avalon_MySQLi
{
	private static $_instance;
	public $prefix;

	public function __construct(array $config)
	{
		self::$_instance = $this;
		$this->prefix = @$config['prefix'];
		$this->connect($config['host'], $config['user'], $config['pass'])->selectDb($config['name']);
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

	public function realEscapeString($string)
	{
		return mysqli_real_escape_string($this->link, $string);
	}
	public function res($string)
	{
		return $this->realEscapeString($string);
	}
	
	public function insertId()
	{
		return mysqli_insert_id($this->link);
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
		Avalon::error("Database Error", mysqli_errno($this->link) . ": " . mysqli_error($this->link) . "<br />" . $this->last_query);
	}
}