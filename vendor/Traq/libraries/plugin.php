<?php
/*!
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

namespace traq\libraries;

/**
 * Plugin base class.
 *
 * @package Traq
 * @subpackage Libraries
 * @author Jack P.
 * @copyright (c) Jack P.
 */
abstract class Plugin
{
    protected static $info = array();

    /**
     * Returns an array of the plugins info
     */
    public static function info()
    {
        return static::$info;
    }

    /**
     * Called when the plugin is loaded
     */
    protected static function init()
    {
    }

    /**
     * Called when the plugin is installed
     *
     * @return bool
     */
    public static function __install()
    {
    }

    /**
     * Called when the enable plugin link is clicked.
     *
     * @return bool
     */
    public static function __enable()
    {
    }

    /**
     * Called when the disable plugin link is clicked.
     *
     * @return bool
     */
    public static function __disable()
    {
    }

    /**
     * Called when the plugin is uninstalled
     *
     * @return bool
     */
    public static function __uninstall()
    {
    }
}
