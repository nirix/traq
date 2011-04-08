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
		$row['milestone'] = $this->db->select('id','milestone','slug')->from('milestones')->where(array('id'=>$row['milestone_id']))->exec()->fetchArray();
		$row['version'] = $this->db->select('id','milestone','slug')->from('milestones')->where(array('id'=>$row['version_id']))->exec()->fetchArray();
		$row['owner'] = $this->db->select('id','username')->from('users')->where(array('id'=>$row['user_id']))->exec()->fetchArray();
		$row['assigned_to'] = $this->db->select('id','username')->from('users')->where(array('id'=>$row['assigned_to']))->exec()->fetchArray();
		$row['status'] = $this->db->select('id','name')->from('ticket_status')->where(array('id'=>$row['status']))->exec()->fetchArray();
		$row['type'] = $this->db->select('id','name')->from('ticket_types')->where(array('id'=>$row['type']))->exec()->fetchArray();
		$row['severity'] = $this->db->select('id','name')->from('severities')->where(array('id'=>$row['severity']))->exec()->fetchArray();
		return $row;
	}
	
	public function filter($filters)
	{
		$rows = $this->db->select()->from($this->_table);
		
		$where = array();
		
		foreach($filters as $filter => $opt)
		{
			// Make sure the filter is set
			if($opt === null) continue;
			
			// Project ID
			if($filter == 'project_id')
			{
				$where[] = "project_id='{$opt}'";
			}
			// Status
			elseif($filter == 'status')
			{
				if($opt == 'open' or $opt == 'closed')
				{
					$opts = array();
					foreach(ticket_status_list($opt == 'open' ? 1 : 0) as $status)
						$opts[] = $status['id'];
					
					$opt = implode(',', $opts);
				}
				
				$where[] = "`status` IN ({$opt})";
			}
			// Milestone
			elseif($filter == 'milestone')
			{
				$opts = explode(',', $opt);
				$milestones = array();
				foreach($opts as $opt)
				{
					$milestone = $this->db->select('id')->from('milestones')->where(array('slug'=>$opt,'project_id'=>$filters['project_id']))->exec()->fetchArray();
					$milestones[] = $milestone['id'];
				}
				
				$where[] = "`milestone_id` IN (".implode(',', $milestones).")";
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