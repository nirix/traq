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

class TicketsController extends AppController
{
	public function index()
	{
		Load::helper('tickets');
		$this->tickets = Load::model('tickets');
		
		// Build filter array
		$filters = array(
			'project_id' => $this->project['id'],
		);
		foreach(ticket_filters() as $filter)
		{
			$filters[$filter] = isset(Param::$request[$filter]) ? Param::$request[$filter] : null;
		}
		
		View::set('columns', ticket_columns());
		
		View::set('tickets', $this->tickets->filter($filters));
	}
}