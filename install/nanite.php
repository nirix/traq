<?php
/*!
 * Nanite
 * Copyright (C) 2012 Jack P.
 * https://github.com/nirix
 *
 * Nanite is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; version 3 only.
 *
 * Nanite is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Nanite. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Shortcut to the Nanite::get() method.
 *
 * @param string $route
 * @param function $function
 */
function get($route, $function)
{
    Nanite::get($route, $function);
}

/**
 * Shortcut to the Nanite::post() method.
 *
 * @param string $route
 * @param function $function
 */
function post($route, $function)
{
    Nanite::post($route, $function);
}

/**
 * Nanite is a tiny PHP router.
 *
 * @copyright Copyright (c) 2012 Jack P.
 * @license GNU Lesser General Public License
 * @version 3.0
 */
class Nanite
{
    private static $request_uri;

    /**
     * Routes a get request and executes the routed function.
     *
     * @param string $route
     * @param function $function
     */
    public static function get($route, $function)
    {
        // Check if the request method type
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'get') {
            static::_process_route($route, $function);
        }
    }

    /**
     * Routes a post request and executes the routed function.
     *
     * @param string $route
     * @param function $function
     */
    public static function post($route, $function)
    {
        // Check the request method type
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            static::_process_route($route, $function);
        }
    }

    /**
     * Determines the base URL of the app.
     *
     * @return string
     */
    public static function base_uri($segments = null)
    {
        return str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) . ($segments ? trim($segnebts, '/') : '');
    }

    /**
     * Porcesses the route.
     *
     * @access private
     */
    private static function _process_route($route, $function)
    {
        // Check if the request is empty
        if (static::_request_uri() == '') {
            static::$request_uri = '/';
        }

        // Match the route
        if (preg_match("#^{$route}$#", static::_request_uri(), $matches)) {
            unset($matches[0]);
            call_user_func_array($function, $matches);
        }
    }

    /**
     * Determines the requested URL.
     *
     * @return string
     * @access private
     */
    private static function _request_uri()
    {
        // Check ff this is the first time getting the request uri
        if (static::$request_uri === null) {
            // Check if there is a PATH_INFO variable
            // Note: some servers seem to have trouble with getenv()
            $path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
            if (trim($path, '/') != '' && $path != "/index.php") {
                return static::$request_uri = $path;
            }

            // Check if ORIG_PATH_INFO exists
            $path = str_replace($_SERVER['SCRIPT_NAME'], '', (isset($_SERVER['ORIG_PATH_INFO'])) ? $_SERVER['ORIG_PATH_INFO'] : @getenv('ORIG_PATH_INFO'));
            if (trim($path, '/') != '' && $path != "/index.php") {
                return static::$request_uri = $path;
            }

            // Check for ?uri=x/y/z
            if (isset($_REQUEST['url'])) {
                return static::$request_uri = $_REQUEST['url'];
            }

            // Check the _GET variable
            if (is_array($_GET) && count($_GET) == 1 && trim(key($_GET), '/') != '') {
                return static::$request_uri = key($_GET);
            }

            // Check for QUERY_STRING
            $path = (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');
            if (trim($path, '/') != '') {
                return static::$request_uri = $path;
            }

            // Check for REQUEST_URI
            $path = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']);
            if (trim($path, '/') != '' && $path != "/index.php") {
                return static::$request_uri = str_replace(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']), '', $path);
            }

            // I dont know what else to try, screw it..
            return static::$request_uri = '';
        }

        return static::$request_uri;
    }
}