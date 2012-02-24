<?php
/*!
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

require __DIR__ . '/base.php';

/**
 * Admin Ticket Statuses controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class AdminTicketStatusesController extends AdminBase
{
	public function __construct()
	{
		parent::__construct();
		$this->title(l('ticket_statuses'));
	}

	public function action_index()
	{
		$statuses = TicketStatus::fetch_all();
		View::set('statuses', $statuses);
	}

	/**
	 * New status page.
	 */
	public function action_new()
	{
		$this->title(l('new'));

		// Create a new status object.
		$status = new TicketStatus;

		// Check if the form has been submitted.
		if (Request::$method == 'post')
		{
			// Set the information.
			$status->set(array(
				'name' => Request::$post['name'],
				'status' => Request::$post['status'],
				'changelog' => isset(Request::$post['changelog']) ? Request::$post['changelog'] : 0
			));

			// Check if the data is valid.
			if ($status->is_valid())
			{
				// Save and redirect.
				$status->save();
				Request::redirect(Request::base('/admin/tickets/statuses'));
			}
		}

		// Send the data to the view.
		View::set('status', $status);
	}

	/**
	 * Edit status page.
	 *
	 * @param integer $id
	 */
	public function action_edit($id)
	{
		$this->title(l('edit'));

		// Fetch the status
		$status = TicketStatus::find($id);

		// Check if the form has been submitted.
		if (Request::$method == 'post')
		{
			// Set the information.
			$status->set(array(
				'name' => Request::$post['name'],
				'status' => Request::$post['status'],
				'changelog' => isset(Request::$post['changelog']) ? Request::$post['changelog'] : 0
			));

			// Check if the data is valid.
			if ($status->is_valid())
			{
				// Save and redirect.
				$status->save();
				Request::redirect(Request::base('/admin/tickets/statuses'));
			}
		}

		// Send the data to the view.
		View::set('status', $status);
	}

	/**
	 * Delete status page.
	 *
	 * @param integer $id
	 */
	public function action_delete($id)
	{
		// Fetch the status, delete it and redirect.
		$status = TicketStatus::find($id);
		$status->delete();
		Request::redirect(Request::base('/admin/tickets/statuses'));
	}
}