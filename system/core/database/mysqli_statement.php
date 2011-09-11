<?php
/**
 * Avalon
 * Copyright (C) 2011 Jack Polgar
 * 
 * @license http://opensource.org/licenses/BSD-3-Clause BSD License
 */

class MySQLi_Statement
{
	private $_model;
	
	public function __construct($result)
	{
		$this->result = $result;
	}
	
	public function _model($model)
	{
		$this->_model = $model;
		return $this;
	}

	public function fetchArray()
	{
		return mysqli_fetch_array($this->result);
	}

	public function fetchAssoc()
	{
		return mysqli_fetch_assoc($this->result);
	}

	public function fetchAll()
	{
		$rows = array();
		while($row = $this->fetchAssoc()) {
			if ($this->_model !== null) {
				$model = $this->_model;
				$rows[] = new $model($row);
			} else {
				$rows[] = $row;
			}
		}
		return $rows;
	}

	public function numRows()
	{
		return mysqli_num_rows($this->result);
	}
}