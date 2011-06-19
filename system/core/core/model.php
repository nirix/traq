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
	
	public function __construct($data = array())
	{
		if (count($data) > 0) {
			foreach ($data as $key => $val) {
				$this->keys[] = $key;
				$this->$key = $val;
			}
		}
	}
	
	public static function find($find, $value = null)
	{
		$row = static::$db->select()->from(static::$_name);
		
		if ($value === null) {
			$row->where("`" . static::$_primary . "` = '?'", $find);
		} else {
			$row->where("`{$find}` = '?'", $value);
		}
		
		$data = $row->limit(1)->exec()->fetchAssoc();
		
		if (static::$_has_many !== null) {
			foreach (static::$_has_many as $has_many) {
				if (is_array($has_many)) {
					if (!isset($has_many['foreign_key'])) {
						$has_many['foreign_key'] = substr(static::$_name, 0, -1) . '_id';
					}
					if (!isset($has_many['column'])) {
						$has_many['column'] = 'id';
					}
					
					$model = $has_many['model'];
					$data[$has_many[0]] = $model::fetchAll(array('where' => array(array($has_many['foreign_key'] . " = '?'", $data[$has_many['column']]))));
				} else {
					$model = ucfirst(substr($has_many, 0, -1));
					$data[$has_many] = $model::fetchAll(array('where' => array(array(substr(static::$_name, 0, -1) . "_id = '?'", $data[static::$_primary]))));
				}
			}
		}
		
		return new static($data);
	}
	
	public static function fetchAll($args = array())
	{
		$rows = static::$db->select()->from(static::$_name);
		
		if (isset($args['where'])) {
			foreach ($args['where'] as $where) {
				$rows->where($where[0], $where[1]);
			}
		}
		
		if (isset($args['order'])) {
			$rows->orderby($args['order'][0], $args['order'][1]);
		}
		
		$rows = $rows->exec()->fetchAll();
		
		return $rows;
	}
}