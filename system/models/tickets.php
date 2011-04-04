<?php
/**
 * Traq
 * Copyright (C) 2009-2011 Jack Polgar
 * 
 * This file is part of Traq.
 * 
 * Traq is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 * 
 * Traq is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Traq. If not, see <http://www.gnu.org/licenses/>.
 */

class TicketsModel extends Model
{
	public function find(array $args)
	{
		$row = parent::find($args);
		
		$row = $this->get_data($row);
		
		return $row;
	}
	
	public function fetchAll(array $args = array())
	{
		$rows = parent::fetchAll($args);
		
		$tickets = array();
		foreach($rows as $row)
		{
			$row = $this->get_data($row);
			$tickets[] = $row;
		}
		
		return $tickets;
	}
	
	private function get_data($row)
	{
		$row['milestone'] = array();
		$row['version'] = array();
		$row['author'] = array();
		$row['assigned_to'] = array();
		
		return $row;
	}
	
	public function filter($filters)
	{
		$rows = $this->db->select()->from($this->_table);
		
		$where = array();
		
		foreach($filters as $filter => $opt)
		{
			if($opt === null) continue;
			
			if($filter == 'project_id')
			{
				$where[] = "project_id='".$opt."'";
			}
			elseif($filter == 'status')
			{
				if($opt == 'open' or $opt == 'closed')
				{
					$opts = array();
					foreach(ticket_status_list($opt == 'open' ? 1 : 0) as $status)
						$opts[] = $status['id'];
					
					$opt = implode(',', $opts);
				}
				
				$where[] = "`status` IN (".$opt.")";
			}
		}
		
		$tickets = array();
		foreach($rows->where($where)->exec()->fetchAll() as $row)
		{
			$row = $this->get_data($row);
			$tickets[] = $row;
		}
		
		return $tickets;
	}
}