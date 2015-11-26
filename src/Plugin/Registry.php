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

namespace Traq\Plugin;

/**
 * Plugin registry
 *
 * @since 4.0
 * @package Traq\Plugin
 * @author Jack Polgar
 */
class Registry
{
    protected static $plugins = array();

    /**
     * @param array $info
     */
    public static function registerPlugin($info)
    {
        static::$plugins[$info['directory']] = $info;
    }

    /**
     * Returns an array of registered plugins.
     *
     * @return array
     */
    public static function registered()
    {
        return static::$plugins;
    }

    public static function infoFor($directory)
    {
        return isset(static::$plugins[$directory]) ? static::$plugins[$directory] : false;
    }

    /**
     * Reads all plugin info files two levels deep in the `vendor` directory.
     *
     * @return array
     */
    public static function indexPlugins()
    {
        $files = array();

        $vendorDir = __DIR__ . '/../../vendor';

        foreach (glob($vendorDir . '/*/plugin.json') as $file) {
            $files[] = $file;
        }

        foreach (glob($vendorDir . '/*/*/plugin.json') as $file) {
            $files[] = $file;
        }

        foreach ($files as $file) {
            $info = json_decode(file_get_contents($file), true);

            if (!$info) {
                continue;
            }

            // Get directory without the vendor directory path
            $info['directory'] = trim(str_replace($vendorDir, '', dirname($file)), '/');

            static::registerPlugin($info);
        }

        return static::$plugins;
    }
}
