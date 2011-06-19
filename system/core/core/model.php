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
	protected static $_has_one;
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
	
	public static function find($find, $value = null, $depth = 2, $curdepth = 0)
	{
		if ($depth == $curdepth) {
			return null;
		}
		
		$row = static::$db->select()->from(static::$_name);
		
		if ($value === null) {
			$row->where("`" . static::$_primary . "` = '?'", $find);
		} else {
			$row->where("`{$find}` = '?'", $value);
		}
		
		$data = $row->limit(1)->exec()->fetchAssoc();
		
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
					$data[$has_many[0]] = static::$db->select()->from($has_many['table'])->where($has_many['foreign_key'] . " = '?'", $data[$has_many['column']])->exec()->fetchAll();
				}
				// Looks like we're using the simple way.
				else {
					$data[$has_many] = static::$db->select()->from($has_many)->where(substr(static::$_name, 0, -1) . "_id = '?'", $data[static::$_primary])->exec()->fetchAll();
				}
			}
		}
		
		// Has one
		if (static::$_has_one !== null) {
			
		}
		
		// Belongs to
		if (static::$_belongs_to !== null) {
			
		}
		
		return new static($data);
	}
	
	public static function fetchAll($args = array())
	{
		if (!isset($args['depth'])) {
			$args['depth'] = 2;
			$args['curdepth'] = 0;
		}
		if ($args['depth'] == $args['curdepth']) {
			return null;
		}
		
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
		
		$data = array();
		foreach ($rows as $row) {
			// Belongs to
			if (static::$_belongs_to !== null) {
				
			}
			
			$data[] = $row;
		}
		
		return $data;
	}
}