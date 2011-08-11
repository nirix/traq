<?php
/**
 * Avalon
 * Copyright (C) 2011 Jack Polgar
 * 
 * @license http://opensource.org/licenses/BSD-3-Clause BSD License
 */

class Model
{
	public static $db;
	protected static $_name;
	protected static $_primary_key = 'id';
	protected static $_has_many;
	protected static $_belongs_to;
	private $_columns = array();
	
	public function __construct($data = null)
	{
		// Loop through the data and make it accessible
		// via $model->column_name
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
	
	public function update()
	{
		return $this->db->update($this->_name);
	}
	
	public function find($find, $value = null)
	{
		$find = $this->db->select()->from($this->_name);
		
		if ($value == null) {
			$find = $find->where($this->primary_key . " = '?'", $find)->limit(1)->exec();
			return $find[0];
		} else {
			$find = $find->where($find . " = '?'", $value)->limit(1)->exec();
			return $find[0];
		}
	}
	
	public function fetchAll()
	{
		return $this->db->select()->from($this->_name)
	}
	
	public function __get($var)
	{
		
	}
}