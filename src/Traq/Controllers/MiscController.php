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

namespace Traq\Controllers;

use Avalon\Core\Controller;
use Avalon\Core\Load;
use Avalon\Database;
use Avalon\Http\JsonResponse;
use Avalon\Http\Request;
use Avalon\Http\Response;
use Avalon\Output\View;
use PDO;
use Traq\Models\Type;

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
    /**
     * Custom constructor, we need to do extra stuff.
     */
    public function __construct()
    {
        parent::__construct();

        View::$searchPaths[] = DOCROOT . '/src/views/_misc';
    }

    /**
     * "Dynamic JavaScript"
     */
    public function javascript(): Response
    {
        return new Response(View::render('javascript'), headers: [
            'Content-Type' => 'text/javascript',
        ]);
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
}
