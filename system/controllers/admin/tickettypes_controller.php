<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Jack Polgar
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

require __DIR__ . '/base.php';

/**
 * Admin Ticket Types controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class AdminTicketTypesController extends AdminBase
{
	public function action_index()
	{
		$types = TicketType::fetch_all();
		View::set('types', $types);
	}
	
	/**
	 * New ticket type page.
	 */
	public function action_new()
	{
		// Create a new ticket type object
		$type = new TicketType();
		
		// Check if the form has been submitted
		if (Request::$method == 'post')
		{
			// Set the information
			$type->set(array(
				'name' => Request::$post['name'],
				'bullet' => Request::$post['bullet'],
				'changelog' => isset(Request::$post['changelog']) ? Request::$post['changelog'] : 0,
				'template' => Request::$post['template'],
			));
			
			// Check if the data is valid
			if ($type->is_valid())
			{
				// Save and redirect
				$type->save();
				Request::redirect(Request::base('/admin/tickets/types'));
			}
		}
		
		// Send the data to the view
		View::set('type', $type);
	}
	
	/**
	 * Edit ticket type.
	 *
	 * @param integer $id
	 */
	public function action_edit($id)
	{
		// Find the ticket type
		$type = TicketType::find($id);
		
		// Check if the form has been submitted
		if (Request::$method == 'post')
		{
			// Update the information
			$type->set(array(
				'name' => Request::$post['name'],
				'bullet' => Request::$post['bullet'],
				'changelog' => isset(Request::$post['changelog']) ? Request::$post['changelog'] : 0,
				'template' => Request::$post['template'],
			));
			
			// Check if the data is valid
			if ($type->is_valid())
			{
				// Save and redirect.
				$type->save();
				Request::redirect(Request::base('/admin/tickets/types'));
			}
		}
		
		// Send the data to the view.
		View::set('type', $type);
	}
	
	/**
	 * Delete ticket type.
	 *
	 * @param integer $id
	 */
	public function action_delete($id)
	{
		// Find the ticket type, delete and redirect.
		$type = TicketType::find($id);
		$type->delete();
		Request::redirect(Request::base('/admin/tickets/types'));
	}
}