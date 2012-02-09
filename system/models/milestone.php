<?php
/*
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

class Milestone extends Model
{
	protected static $_name = 'milestones';
	protected static $_properties = array(
		'id',
		'name',
		'slug',
		'codename',
		'info',
		'changelog',
		'due',
		'is_completed',
		'is_cancelled',
		'is_locked',
		'project_id',
		'displayorder'
	);
	
	protected static $_has_many = array('tickets');
	protected static $_belongs_to = array('project');
	
	/**
	 * Easily get the URI to the milestone.
	 *
	 * @return string
	 */
	public function href()
	{
		return '/' . $this->project->slug . "/milestone/" . $this->slug;
	}

	/**
	 * Returns the number of tickets for the specified status.
	 *
	 * @param string $status The status of the ticket:
	 *     open, closed, total, open_percent, closed_percent
	 *
	 * @return integer
	 */
	public function ticket_count($status = 'total')
	{
		// Holder for the counts array.
		static $counts = array();

		// Check if we need to fetch
		// the ticket counts.
		if (!isset($counts[$this->id]))
		{
			$counts[$this->id] = array(
				'open' => $this->tickets->where('is_closed', 0)->exec()->row_count(),
				'closed' => $this->tickets->where('is_closed', 1)->exec()->row_count()
			);
			$counts[$this->id]['total'] = $counts[$this->id]['open'] + $counts[$this->id]['closed'];
			$counts[$this->id]['open_percent'] = $counts[$this->id]['open'] ? get_percent($counts[$this->id]['open'], $counts[$this->id]['total']) : 0;
			$counts[$this->id]['closed_percent'] = get_percent($counts[$this->id]['closed'], $counts[$this->id]['total']);
		}

		// Return the requested count index.
		return $counts[$this->id][$status];
	}
	
	/**
	 * Checks if the models data is valid.
	 *
	 * @return bool
	 */
	public function is_valid()
	{
		$errors = array();
		
		// Check if the name is empty
		if (empty($this->_data['name']))
		{
			$errors['name'] = l('error:name_blank');
		}
		
		// Check if the slug is empty
		if (empty($this->_data['slug']))
		{
			$errors['slug'] = l('error:slug_blank');
		}
		
		// Check if the slug is in use
		if (Milestone::select('slug')->where('id', $this->_is_new() ? 0 : $this->_data['id'], '!=')->where('slug', $this->_data['slug'])->where('project_id', $this->project_id)->exec()->row_count())
		{
			$errors['slug'] = l('error:slug_in_use');
		}
		
		$this->errors = $errors;
		return !count($errors) > 0;
	}
}