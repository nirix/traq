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
	
	public function create()
	{
		
	}
	
	public static function select($cols = '*')
	{
		return Database::link()->select($cols)->from(static::$_name)->_model(static::_class_name());
	}
	
	/**
	 * Aliases the database's update() method for the current row.
	 * @author Jack Polgar
	 * @since 0.2
	 */
	public function update()
	{
		return Database::link()->update(static::$_name)->where(static::$_primary_key . " = '?'", $this->data[static::$_primary_key]);
	}
	
	/**
	 * Find the first matching row and returns it.
	 * @param string $find Either the value of the primary key, or the field name.
	 * @param value $value The value of the field to find if the $find param is the field name.
	 * @return Object
	 * @author Jack Polgar
	 * @since 0.1
	 */
	public static function find($find, $value = null)
	{
		$select = Database::link()->select()->from(static::$_name);
		
		if ($value == null) {
			$select = $select->where(static::$_primary_key . " = '?'", $find)->limit(1)->exec()->fetchAssoc();
			return new static($select);
		} else {
			$select = $select->where($find . " = '?'", $value)->limit(1)->exec()->fetchAssoc();
			return new static($select);
		}
	}
	
	/**
	 * Fetches all the rows for the table.
	 * @return array
	 */
	public static function fetchAll()
	{
		$rows = array();
		$fetched = Database::link()->select('*')->from(static::$_name)->exec()->fetchAll();
		
		foreach ($fetched as $row) {
			$rows[] = new static($row);
		}
		
		return $rows;
	}
	
	public function __get($var)
	{
		// Has many
		// Belongs to
		if (is_array(static::$_belongs_to) and (in_array($var, static::$_belongs_to) or isset(static::$_belongs_to[$var]))) {
			$belongs_to = array();
			if (isset(static::$_belongs_to[$var])) {
				$belongs_to = static::$_belongs_to[$var];
			}
			// Model
			if (!isset($belongs_to['model'])) {
				$belongs_to['model'] = ucfirst($var);
			}
			// Different foreign key?
			if (!isset($belongs_to['foreign_key'])) {
				$belongs_to['foreign_key'] = 'id';
			}
			// Different column?
			if (!isset($belongs_to['column'])) {
				$belongs_to['column'] = $var . '_id';
			}
			$model = $belongs_to['model'];
			$this->$var = $model::find($belongs_to['foreign_key'], $this->$belongs_to['column']);
			return $this->$var;
		}
	}
	
	private static function _class_name()
	{
		return isset(static::$_class_name) ? static::$_class_name : static::$_name;
	}
}