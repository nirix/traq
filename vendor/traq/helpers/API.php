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

namespace traq\helpers;

use avalon\http\Request;

/**
 * API helper.
 *
 * @author Jack P.
 * @since 3.1
 * @package Traq
 * @subpackage Helpers
 * @deprecated 3.9.0
 */
class API
{
    /**
     * Returns a JSON formatted response.
     *
     * @param integer $status   Request result, 1 = good, 0 = bad
     * @param array   $response Response data
     *
     * @example
     *     API::response(0, array('error' => "api_key_invalid"));
     */
    public static function response($status = 1, array $response = array())
    {
        // 0, Bad Request
        if (!$status) {
            header("HTTP/1.1 400 Bad Request");
        }

        // 403, Forbidden
        if ($status == 403) {
            header("HTTP/1.1 403 Forbidden");
        }

        // 404, Not Found
        if ($status == 404) {
            header("HTTP/1.1 404 Not Found");
        }

        return to_json(array_merge(array('status' => $status, 'version' => TRAQ_API_VER), $response));
    }
}
