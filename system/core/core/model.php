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
	protected static $_primary = 'id';
	protected static $_has_many;
	protected static $_belongs_to;
	
	private $keys = array();
	
	public function __construct($data = null)
	{
		if ($data !== null) {
			foreach ($data as $key => $val) {
				$this->keys[] = $key;
				$this->$key = $val;
			}
		}
	}
	
	public function save()
	{
		
	}
	
	public static function find($find, $value = null)
	{
		$row = Database::link()->select()->from(static::$_name);
		
		if ($value === null) {
			$row->where("`" . static::$_primary . "` = '?'", $find);
		} else {
			$row->where("`{$find}` = '?'", $value);
		}
		
		$data = $row->limit(1)->exec()->fetchAssoc();
		
		return new static($data);
	}
	
	public static function fetchAll($args = array())
	{
		$rows = Database::link()->select()->from(static::$_name);
		
		if (isset($args['where'])) {
			foreach ($args['where'] as $where) {
				$rows->where($where[0], $where[1]);
			}
		}
		
		if (isset($args['order'])) {
			$rows->orderby($args['order'][0], $args['order'][1]);
		}
		
		$rows = $rows->exec()->fetchAll();
		
		$data = array();
		foreach ($rows as $row) {
			$data[] = new static($row);
		}
		
		return $data;
	}
	
	public function __get($var)
	{
		// Has Many
		if (is_array(static::$_has_many) and (in_array($var, static::$_has_many) or isset(static::$_has_many[$var]))) {
			$has_many = array();
			if (isset(static::$_has_many[$var])) {
				$has_many = static::$_has_many[$var];
			}
			// Model
			if (!isset($has_many['model'])) {
				$has_many['model'] = ucfirst(substr($var, 0, -1));
			}
			// Different foreign key?
			if (!isset($has_many['foreign_key'])) {
				$has_many['foreign_key'] = substr(static::$_name, 0, -1) . '_id';
			}
			// Different column?
			if (!isset($has_many['column'])) {
				$has_many['column'] = static::$_primary;
			}
			$model = $has_many['model'];
			$this->$var = $model::fetchAll(array('where' => array(array($has_many['foreign_key'] . " = '?'", $this->$has_many['column']))));
			return $this->$var;
		}
		// Belongs to
		elseif (is_array(static::$_belongs_to) and (in_array($var, static::$_belongs_to) or isset(static::$_belongs_to[$var]))) {
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
}