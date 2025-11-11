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

namespace traq\controllers;

use Avalon\Core\Controller;
use Avalon\Core\Load;
use Avalon\Database;
use Avalon\Http\JsonResponse;
use Avalon\Http\Request;
use Avalon\Output\View;
use PDO;
use traq\models\Type;

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
        parent::__construct();

        View::$searchPaths[] = DOCROOT . '/src/views/_misc';

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
    public function autocompleteUsername(): JsonResponse
    {
        $term = Request::get('term', '');

        if (empty($term)) {
            return new JsonResponse([]);
        }

        $db = Database::connection();
        $prefix = $db->prefix;
        $stmt = $db->prepare("SELECT username FROM {$prefix}users WHERE username LIKE :username");
        $stmt->execute([
            'username' => str_replace('*', '%', $term . "%")
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $usernames = array_map(function ($row) {
            return $row['username'];
        }, $rows);

        return new JsonResponse($usernames);
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
