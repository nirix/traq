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
 * Git class.
 * Copyright (C) Jack Polgar
 *
 * @author Jack P.
 * @copyright (C) Jack P.
 * @package Traq
 * @package SCM
 * @version 0.1
 */
class GitScm extends SCMBase
{
	protected $_binary = 'git';

	/**
	 * Returns an array of branches.
	 *
	 * @return array
	 */
	public function branches()
	{
		return explode("\n", $this->_shell('branch'));
	}

	/**
	 * Returns an array of tags.
	 *
	 * @return array
	 */
	public function tags()
	{
		return explode("\n", $this->_shell('tag'));
	}
}