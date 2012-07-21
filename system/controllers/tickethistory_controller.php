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

/**
 * Ticket History controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class TicketHistoryController extends AppController
{
	public function action_edit($id)
	{
		$history = TicketHistory::find($id);

		// Check permission
		if (!$this->user->permission($history->ticket->project_id, 'edit_ticket_history'))
		{
			return $this->show_no_permission();
		}

		if (Request::$method == 'post')
		{
			$history->comment = Request::$post['comment'];

			if ($history->save())
			{
				Request::redirect(Request::base($history->ticket->href()));
			}
		}

		View::set('history', $history);
	}

	public function action_delete($id)
	{
		$history = TicketHistory::find($id);

		// Check permission
		if (!$this->user->permission($history->ticket->project_id, 'delete_ticket_history'))
		{
			return $this->show_no_permission();
		}

		$history->delete();

		if (Request::is_ajax())
		{
			View::set('history', $history);
		}
		else
		{
			Request::redirect(Request::base($history->ticket->href()));
		}
	}
}