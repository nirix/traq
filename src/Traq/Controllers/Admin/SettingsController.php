<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
 * https://github.com/nirix
 * http://traq.io
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

namespace Traq\Controllers\Admin;

use Avalon\Database;
use Avalon\Http\Request;
use Avalon\Output\View;

/**
 * Admin Settings controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class SettingsController extends AppController
{
    /**
     * Traq Settings page
     */
    public function index()
    {
        $this->title(l('settings'));

        // Check if the form has been submitted.
        if (Request::method() === 'POST') {
            $_settings = Request::$post['settings'];

            $errors = [];

            // Check title
            if (empty($_settings['title'])) {
                $errors['title'] = l('errors.settings.title_blank');
            }

            // Check select fields
            foreach (['locale', 'theme', 'allow_registration'] as $select) {
                if (!isset($_settings[$select])) {
                    $errors[$select] = l("errors.settings.{$select}_blank");
                }
            }

            // Check for errors
            if (!count($errors)) {
                foreach ($_settings as $_setting => $_value) {
                    Database::connection()->update('settings')->set(['value' => $_value])->where('setting', $_setting)->exec();
                }

                return $this->redirectTo(Request::requestUri());
            }

            return $this->render('admin/settings/index.phtml', [
                'errors' => $errors,
            ]);
        }

        return $this->render('admin/settings/index.phtml');
    }
}
