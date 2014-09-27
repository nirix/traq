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

namespace Traq;

use Radium\Http\Request;
use Radium\Http\Response;
use Traq\Helpers\Format;

/**
 * API helper.
 *
 * @author Jack P.
 * @since 3.1
 * @package Traq
 * @subpackage Helpers
 */
class API
{
    /**
     * Check for an access token (API key) and return it,
     * otherwise return null.
     *
     * @return mixed
     */
    public static function getKey()
    {
        // Check request
        if (isset(Request::$request['access_token']) and isset(Request::$request['access_token'][5])) {
            return Request::$request['access_token'];
        }
        // Check header
        elseif (isset($_SERVER['HTTP_ACCESS_TOKEN']) and isset($_SERVER['HTTP_ACCESS_TOKEN'][5])) {
            return $_SERVER['HTTP_ACCESS_TOKEN'];
        }
        // Set, but not >= 5 characters
        elseif (isset(Request::$request['access_token']) or isset($_SERVER['HTTP_ACCESS_TOKEN'])) {
            return false;
        }
    }

    /**
     * Returns a JSON formatted response.
     *
     * @param array   $response Response data
     * @param integer $status   HTTP response code
     *
     * @example
     *     API::response(401, array('error' => "api_key_invalid"));
     */
    public static function response($status = 200, $data = array())
    {
        if (is_array($data) or is_object($data)) {
            $data = Format::toJson($data);
        }

        return new Response(function($resp) use ($status, $data) {
            $resp->contentType = 'application/json';
            $resp->status      = $status;
            $resp->body        = $data;

            header("X-Traq-Version: " . Traq::version());
            header("X-API-Version: " . TRAQ_API_VER);
        });
    }
}
