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
	
	public function action_new()
	{
		$type = new TicketType();
		
		if (Request::$method == 'post')
		{
			$type->set(array(
				'name' => Request::$post['name'],
				'bullet' => Request::$post['bullet'],
				'changelog' => isset(Request::$post['changelog']) ? Request::$post['changelog'] : 0,
				'template' => Request::$post['template'],
			));
			
			if ($type->is_valid())
			{
				$type->save();
				Request::redirect(Request::base('/admin/tickets/types'));
			}
		}
		
		View::set('type', $type);
	}
	
	public function action_edit($id)
	{
		$type = TicketType::find($id);
		
		if (Request::$method == 'post')
		{
			$type->set(array(
				'name' => Request::$post['name'],
				'bullet' => Request::$post['bullet'],
				'changelog' => isset(Request::$post['changelog']) ? Request::$post['changelog'] : 0,
				'template' => Request::$post['template'],
			));
			
			if ($type->is_valid())
			{
				$type->save();
				Request::redirect(Request::base('/admin/tickets/types'));
			}
		}
		
		View::set('type', $type);
	}
	
	public function action_delete($id)
	{
		$type = TicketType::find($id);
		$type->delete();
		Request::redirect(Request::base('/admin/tickets/types'));
	}
}