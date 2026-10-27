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

use Avalon\Http\Router;

/**
 * Used to create URI slugs.
 *
 * @param string $uri
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 * @subpackage Helpers
 */
function mkSlug(string $string)
{
    // Convert to lowercase, remove spaces, and replace with hyphens and strip special characters
    // Allow: -, _, .
    return trim(preg_replace('/[^a-z0-9-_.]/', '', str_replace(' ', '-', strtolower($string))));
}

/**
 * @deprecated 3.9.0 Use mkSlug instead.
 */
function create_slug($uri)
{
    return mkSlug((string) $uri);
}

function route(string $name, array $params = []): string
{
    $url = Router::getPath($name);

    foreach ($params as $key => $value) {
        // Build a regex to find the (?P<key>...) block
        // We use preg_quote on the key to ensure it's treated literally
        $pattern = '/\(\?P<' . preg_quote($key, '/') . '>[^)]+\)/';

        $url = preg_replace($pattern, (string) $value, $url);
    }

    return $url;
}
