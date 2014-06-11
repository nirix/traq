<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

namespace traq\controllers;

use avalon\core\Controller;
use avalon\http\Request;
use avalon\output\View;
use avalon\core\Load;

use traq\models\Type;
use traq\models\User;

/**
 * Misc controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Misc extends Controller
{
    /**
     * Custom constructor, we need to do extra stuff.
     */
    public function __construct()
    {
        // Set the theme
        View::$theme = '_misc';

        parent::__construct();

        // Load helpers
        Load::helper("html");
        Load::helper('formatting');
    }

    /**
     * "Dynamic JavaScript"
     */
    public function action_javascript()
    {
        global $locale;

        // Set the content type to javascript
        header("Content-type: text/javascript");

        // Set the view without the controller namespace
        $this->render['view'] = 'javascript';

        // Get the locale strings and set the editor strings
        $strings = $locale->locale();
        View::set('editor_strings', $strings['editor']);
    }

    /**
     * Used to get the ticket template.
     *
     * @param integer $type_id
     */
    public function action_ticket_template($type_id)
    {
        // No view, just print the ticket template
        $this->render['view'] = false;
        return Type::find($type_id)->template;
    }

    /**
     * Used to autocomplete usernames
     */
    public function action_autocomplete_username()
    {
        // No view, just json content
        $this->render['view'] = false;
        header("Content-type: application/json");

        // Get the users, and loop over them
        $users = User::select('username')->where('username', str_replace('*', '%', Request::$request['term']) . "%", 'LIKE')->exec()->fetch_all();
        $options = array();
        foreach ($users as $user) {
            // Add the user to the optionls array
            $options[] = $user->username;
        }

        // Make sure there are some options
        if (count($options)) {
            // Output in javascript array format
            return '["' . implode('","', $options) . '"]';
        }
    }

    public function action_preview_text()
    {
        $this->render['view'] = 'preview_text';
        View::set('data', format_text(Request::$request['data']));
    }

    public function action_format_text()
    {
        return format_text(Request::$request['data']);
    }
}
