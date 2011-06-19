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
		
		$data = static::_get_data($data);
		
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
			$data[] = static::_get_data($row);
		}
		
		return $data;
	}
	
	private static function _get_data($data)
	{
		// Has many
		if (static::$_has_many !== null) {
			foreach (static::$_has_many as $has_many) {
				// Check if we're doing it the verbose way.
				if (is_array($has_many)) {
					// Different table?
					if (!isset($has_many['table'])) {
						$has_many['table'] = $has_many[0];
					}
					// Different foreign key?
					if (!isset($has_many['foreign_key'])) {
						$has_many['foreign_key'] = substr(static::$_name, 0, -1) . '_id';
					}
					// Different column?
					if (!isset($has_many['column'])) {
						$has_many['column'] = static::$_primary;
					}
					// Get the data
					$data[$has_many[0]] = Database::link()->select()->from($has_many['table'])->where($has_many['foreign_key'] . " = '?'", $data[$has_many['column']])->exec()->fetchAll();
				}
				// Looks like we're using the simple way.
				else {
					$data[$has_many] = Database::link()->select()->from($has_many)->where(substr(static::$_name, 0, -1) . "_id = '?'", $data[static::$_primary])->exec()->fetchAll();
				}
			}
		}
		
		// Belongs to
		if (static::$_belongs_to !== null) {
			foreach (static::$_belongs_to as $belongs_to) {
				// Check if we're doing it the verbose way.
				if (is_array($belongs_to)) {
					// Different table?
					if (!isset($belongs_to['table'])) {
						$belongs_to['table'] = $belongs_to[0] . 's';
					}
					// Different foreign key?
					if (!isset($belongs_to['foreign_key'])) {
						$belongs_to['foreign_key'] = 'id';
					}
					// Different column?
					if (!isset($belongs_to['column'])) {
						$belongs_to['column'] = $belongs_to[0] . '_id';
					}
					// Get the data
					$data[$belongs_to[0]] = Database::link()->select()->from($belongs_to['table'])->where($belongs_to['foreign_key'] . " = '?'", $data[$belongs_to['column']])->limit(1)->exec()->fetchAssoc();
				}
				// Looks like we're using the simple way.
				else {
					$data[$belongs_to] = Database::link()->select()->from($belongs_to . 's')->where("id = '?'", $data[$belongs_to . '_id'])->limit(1)->exec()->fetchAssoc();
				}
			}
		}
		
		return $data;
	}
}