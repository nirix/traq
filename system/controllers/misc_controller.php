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
 * Misc controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class MiscController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		View::$theme = '_misc';
	}

	/**
	 * Outputs the javascript to localize the editor.
	 */
	public function action_editor_locale()
	{
		global $locale;
		header("Content-type: text/javascript");
		$this->_render['view'] = 'editor_locale';
		$strings = $locale->locale();
		View::set('strings', $strings['editor']);
	}
}