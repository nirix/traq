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
				if (is_array($has_many)) {
					if (!isset($has_many['foreign_key'])) {
						$has_many['foreign_key'] = substr(static::$_name, 0, -1) . '_id';
					}
					if (!isset($has_many['column'])) {
						$has_many['column'] = 'id';
					}
					
					$model = $has_many['model'];
					$data[$has_many[0]] = $model::fetchAll(array('where' => array(array($has_many['foreign_key'] . " = '?'", $data[$has_many['column']])), 'depth' => $depth, 'curdepth' => $curdepth + 1));
				} else {
					$model = ucfirst(substr($has_many, 0, -1));
					$data[$has_many] = $model::fetchAll(array('where' => array(array(substr(static::$_name, 0, -1) . "_id = '?'", $data[static::$_primary])), 'depth' => $depth, 'curdepth' => $curdepth + 1));
				}
			}
		}
		
		// Has one
		if (static::$_has_one !== null) {
			foreach (static::$_has_one as $has_one) {
				if (is_array($has_one)) {
					
				} else {
					$model = ucfirst($has_one);
					$data[$has_one] = $model::find($has_one . '_id', $data[static::$_primary], $depth, $curdepth + 1);
				}
			}
		}
		
		// Belongs to
		if (static::$_belongs_to !== null) {
			foreach (static::$_belongs_to as $belongs_to) {
				if (is_array($belongs_to)) {
					
				} else {
					$model = ucfirst($belongs_to);
					$data[$belongs_to] = $model::find($data[static::$_primary], null, $depth, $curdepth + 1);
				}
			}
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
				foreach (static::$_belongs_to as $belongs_to) {
					if (is_array($belongs_to)) {
						
					} else {
						$model = ucfirst($belongs_to);
						$row[$belongs_to] = $model::find($row[static::$_primary], null, $args['depth'], $args['curdepth']);
					}
				}
			}
			
			$data[] = $row;
		}
		
		return $data;
	}
}