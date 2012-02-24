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

/**
 * AdminCP controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class AdminBase extends AppController
{
	public function __construct()
	{
		parent::__construct();
		
		// Set the admin layout.
		$this->_render['layout'] = 'admin';
		$this->title(l('admincp'));
		
		// Check if the user is an admin before
		// if not show the login page with this pages
		// URI so we can redirect them back to this page
		// after they login.
		if (!$this->user->group->is_admin)
		{
			$this->show_login(Request::full_uri());
		}
	}
}