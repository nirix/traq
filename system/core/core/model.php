<?php
/**
 * Avalon
 * Copyright (C) 2011 Jack Polgar
 * 
 * @license http://opensource.org/licenses/BSD-3-Clause BSD License
 */

/**
 * Base model class
 * @author Jack Polgar
 * @since 0.1
 * @package Avalon
 * @subpackage Database
 */
class Model
{
	public static $db;
	protected static $_name;
	protected static $_primary_key = 'id';
	protected static $_has_many;
	protected static $_belongs_to;
	private $_columns = array();
	
	/**
	 * Used to build to assign the row data to the class as variables.
	 * @param array $data The row data
	 * @author Jack Polgar
	 * @since 0.1
	 */
	public function __construct($data = null)
	{
		// Loop through the data and make it accessible
		// via $model->column_name
		$this->_data = $data;
		if ($data !== null) {
			foreach ($data as $column => $value) {
				$this->$column = $value;
				$this->_columns[] = $column;
			}
		}
	}
	
	public function save()
	{
		
	}
	
	/**
	 * Aliases the database's update() method for the current row.
	 * @author Jack Polgar
	 * @since 0.2
	 */
	public function update()
	{
		$primary_key_value = $this->data[$this->_primary_key];
		return Database::link()->update($this->_name)->where($this->_primary_key " = '?'", $primary_key_value);
	}
	
	/**
	 * Find the first matching row and returns it.
	 * @param string $find Either the value of the primary key, or the field name.
	 * @param value $value The value of the field to find if the $find param is the field name.
	 * @return Object
	 * @author Jack Polgar
	 * @since 0.1
	 */
	public function find($find, $value = null)
	{
		$find = Database::link()->select()->from(static::$_name);
		
		if ($value == null) {
			$find = $find->where(static::$primary_key . " = '?'", $find)->limit(1)->exec();
			return $find[0];
		} else {
			$find = $find->where($find . " = '?'", $value)->limit(1)->exec();
			return $find[0];
		}
	}
	
	/**
	 * Fetches all the rows for the table.
	 * @return array
	 */
	public function fetchAll()
	{
		$rows = array();
		$fetched = Database::link()->select()->from(static::$_name)->exec()->fetchAll();
		
		foreach ($fetched as $row) {
			$rows[] = new static($row);
		}
		
		return $rows;
	}
	
	public function __get($var)
	{
		
	}
}