<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
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

/**
 * Theme handler.
 *
 * @package Traq
 * @author Jack P.
 * @since 4.0.0
 */
class Themes
{
    /**
     * @var array[]
     */
    protected static $themes = [];

    /**
     * Search the `themes` and `vendor` directories for themes.
     *
     * @return array
     */
    public static function index()
    {
        $files = [];

        $rootDir = __DIR__ . '/../';
        $vendorDir = __DIR__ . '/../vendor';

        foreach (glob($rootDir . '/themes/*/theme.json') as $file) {
            $files[] = $file;
        }

        foreach (glob($vendorDir . '/*/*/theme.json') as $file) {
            $files[] = $file;
        }

        foreach ($files as $file) {
            $info = json_decode(file_get_contents($file), true);

            if (!$info) {
                continue;
            }

            // Get directory without the vendor directory path
            $info['directory'] = trim(str_replace([$rootDir, $vendorDir], '', dirname($file)), '/');

            static::registerTheme($info);
        }

        return static::$themes;
    }

    /**
     * Add a theme.
     */
    public static function registerTheme($info)
    {
        static::$themes[$info['directory']] = $info;
    }

    /**
     * Returns a `Form::select()` options array.
     *
     * @return array
     */
    public static function selectOptions()
    {
        $options = [
            ['label' => 'Default', 'value' => 'default']
        ];

        foreach (static::$themes as $theme) {
            $options[] = ['label' => $theme['name'], 'value' => $theme['directory']];
        }

        return $options;
    }
}
