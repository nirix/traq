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
 * Attachments controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class AttachmentsController extends AppController
{
	public function action_view($attachment_id)
	{
		$attachment = Attachment::find($attachment_id);

		// Check permission
		if (!$this->user->permission($attachment->ticket->project_id, 'view_attachments'))
		{
			return $this->show_no_permission();
		}

		// Don't try to load a view
		$this->_render['view'] = false;

		header("Content-type: {$attachment->type}");
		$content_type = explode('/', $attachment->type);

		// Check what type of file we're dealing with.
		if($content_type[0] == 'text' or $content_type[0] == 'image')
		{
			if ($content_type[0] == 'text')
			{
				header("Content-type: text/plain");
			}
			header("Content-Disposition: filename=\"{$attachment->name}\"");
		}
		else
		{
			header("Content-Disposition: attachment; filename=\"{$attachment->name}\"");
		}

		print(base64_decode($attachment->contents));
		exit;
	}

	public function action_delete($attachment_id)
	{
		$attachment = Attachment::find($attachment_id);

		// Check permission
		if (!$this->user->permission($attachment->ticket->project_id, 'delete_attachments'))
		{
			return $this->show_no_permission();
		}

		$attachment->delete();
		Request::redirect(Request::base($attachment->ticket->href()));
	}
}