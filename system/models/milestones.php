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

class MilestonesModel extends Model
{
	public $_table = 'milestones';
	
	public function find(array $args)
	{
		$row = parent::find($args);
		
		$row = array_merge($row, $this->get_data($row));
		
		return $row;
	}
	
	public function fetchAll(array $args = array())
	{
		$rows = parent::fetchAll($args);
		
		$milestones = array();
		foreach($rows as $row)
		{
			$row = array_merge($row, $this->get_data($row));
			$row['tickets'] = $this->db->select('ticket_id','summary','closed')->from('tickets')->where("milestone_id='{$row['id']}'")->orderby('ticket_id','ASC')->exec()->fetchAll();
			$milestones[] = $row;
		}
		
		return $milestones;
	}
	
	private function get_data($row)
	{
		$row['ticket_count'] = array(
			'open' => $this->db->select('id')->from('tickets')->where("milestone_id='{$row['id']}'","closed='0'")->exec()->numRows(),
			'closed' => $this->db->select('id')->from('tickets')->where("milestone_id='{$row['id']}'","closed='1'")->exec()->numRows()
		);
		$row['ticket_count']['total'] = $row['ticket_count']['open'] + $row['ticket_count']['closed'];
		$row['progress'] = array(
			'open' => getpercent($row['ticket_count']['open'], $row['ticket_count']['total']),
			'closed' => getpercent($row['ticket_count']['closed'], $row['ticket_count']['total']),
		);
		
		return $row;
	}
}