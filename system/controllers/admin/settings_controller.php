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

use avalon\Database;
use avalon\http\Request;
use avalon\output\View;

require __DIR__ . '/base.php';

/**
 * Admin Settings controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class AdminSettingsController extends AdminBase
{
	/**
	 * Traq Settings page
	 */
	public function action_index()
	{
		$this->title(l('settings'));

		// Check if the form has been submitted.
		if (Request::$method == 'post') {
			$_settings = Request::$post['settings'];

			// Check for errors
			$errors = array();

			// Check title
			if (empty($_settings['title'])) {
				$errors['title'] = l('errors.settings.title_blank');
			}

			// Check select fields
			foreach (array('locale', 'theme', 'allow_registration') as $select) {
				if (empty($_settings[$select])) {
					$errors[$select] = l("errors.settings.{$select}_blank");
				}
			}

			if (!count($errors)) {
				foreach ($_settings as $_setting => $_value) {
					Database::connection()->update('settings')->set(array('value' => $_value))->where('setting', $_setting)->exec();
				}

				Request::redirect(Request::full_uri());
			}

			View::set('errors', $errors);
		}
	}
}