<?php
/**
 * Meridian
 * Copyright (C) 2010-2011 Jack Polgar
 * 
 * This file is part of Meridian.
 * 
 * Meridian is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 * 
 * Meridian is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Meridian. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * MySQL Improved Query handler
 * @package Meridian
 * @subpackage Database
 */
class MySQLi_Query
{
	private $type;
	private $cols;
	private $table;
	private $groupby;
	private $where;
	private $limit;
	private $orderby;
	
	public function __construct($type, $cols)
	{
		$this->type = $type;
		$this->cols = $cols;
		$this->prefix = DB_MySQLi::getInstance()->prefix;
		return $this;
	}
	
	public function distinct()
	{
		$this->type = $this->type.' DISTINCT';
		return $this;
	}
	
	public function from($table)
	{
		$this->table = $table;
		return $this;
	}
	
	public function into($table)
	{
		$this->table = $table;
		return $this;
	}
	
	public function groupby($cols)
	{
		$this->groupby = $cols;
		return $this;
	}
	
	public function where($where)
	{
		if(!is_array($where)) $where = func_get_args();
		$this->where = $where;
		return $this;
	}
	
	public function orderby($col, $direction)
	{
		if(!empty($col)) $this->orderby = ' ORDER BY '.$col.' '.$direction;
		return $this;
	}
	
	public function limit($a, $b = null)
	{
		$this->limit = ' LIMIT '.($b == null ? $a : "{$a}, {$b}");
		return $this;
	}
	
	private function _assemble()
	{
		// Type
		$sql = $this->type.' ';
		
		// SELECT
		// DELETE
		if($this->type == 'SELECT'
		or $this->type == 'DELETE')
		{
			// Columns
			$cols = array();
			foreach($this->cols as $col => $as)
			{
				if(!is_numeric($col))
					$cols[] = " {$col} AS {$as}";
				else
					$cols[] = " {$as}";
			}
			$sql .= implode(', ', $cols);
		
			// From
			$sql .= ' FROM '.$this->prefix.$this->table;
		
			// Group by
			if($this->groupby != null)
			{
				$sql .= " GROUP BY ".implode(', ', $this->groupby);
			}
		
			// Where
			if($this->where != null)
			{
				$_where = array();
				foreach($this->where as $col => $val)
				{
					if(is_numeric($col))
					{
						$_where[] = "`".$this->prefix.$this->table."`.".$val;
					}
					else
					{
						$_where[] = "`".$this->prefix.$this->table."`.`".$col."`='".DB_MySQLi::getInstance()->real_escape_string($val)."'";
					}
				}
			
				$sql .= ' WHERE '.implode(' AND ', $_where);
			}
		
			// Order by
			if($this->orderby != null)
				$sql .= $this->orderby;
		
			// Limit
			if($this->limit != null)
				$sql .= $this->limit;
		}
		// INSERT
		elseif($this->type == 'INSERT INTO')
		{
			$sql .= "`".$this->prefix.$this->table."`";
			
			$keys = array();
			$values = array();
			
			foreach($this->cols as $key => $value)
			{
				$keys[] = "{$key}";
				$values[] = $value;
			}
			
			$sql .= ' ('.implode(', ', $keys).')';
			$sql .= ' VALUES('.implode(', ', $values).')';
		}
		return $sql;
	}
	
	public function exec()
	{
		return DB_MySQLi::getInstance()->query($this->_assemble());
	}
	
	public function __toString()
	{
		return $this->_assemble();
	}
}