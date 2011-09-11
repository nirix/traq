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
	public $last_query;
	
	public function __construct(array $config)
	{
		self::$_instance = $this;
		$this->prefix = @$config['prefix'];
		$this->connect($config['host'], $config['user'], $config['pass'])->select_db($config['name']);
		return $this;
	}

	public function connect($host, $user, $pass)
	{
		$this->link = mysqli_connect($host, $user, $pass) or $this->halt();
		return $this;
	}

	public function select_db($dbname)
	{
		mysqli_select_db($this->link, $dbname) or $this->halt();
		return $this;
	}

	public function select($cols = array('*'), $after_exec = false)
	{
		if (!is_array($cols)) {
			$cols = array($cols);
		}
		return new MySQLi_Query("SELECT", $cols, $after_exec);
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
		$this->last_query = (string) $query;
		$result = mysqli_query($this->link, (string) $query) or $this->halt();
		return new MySQLi_Statement($result);
	}

	public function real_escape_string($string)
	{
		return mysqli_real_escape_string($this->link, $string);
	}
	public function res($string)
	{
		return $this->realEscapeString($string);
	}
	
	public function insert_id()
	{
		return mysqli_insert_id($this->link);
	}
	
	public static function get_instance()
	{
		return self::$_instance;
	}

	public function link()
	{
		return $this->link;
	}
	
	public function halt()
	{
		Error::halt("Database Error", mysqli_errno($this->link) . ": " . mysqli_error($this->link) . "<br />" . $this->last_query);
	}
}