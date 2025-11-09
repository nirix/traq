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

namespace Traq\Plugins;

use \FishHook;
use \ParsedownExtra;
use Traq\Libraries\Plugin;

/**
 * Markdown Plugin.
 *
 * @package Traq
 * @subpackage Plugins
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Markdown extends Plugin
{
    protected static ParsedownExtra $parser;

    protected static $info = [
        'name'    => 'Markdown',
        'version' => '3.0.0',
        'author'  => 'Jack P.',
    ];

    /**
     * Handles the startup of the plugin.
     */
    public static function init(): void
    {
        FishHook::add('function:format_text', array(get_called_class(), 'format_text'));
    }

    /**
     * Handles the format_text function hook.
     */
    public static function format_text(&$text, $strip_html)
    {
        // If HTML is being converted to text, undo it.
        if ($strip_html) {
            $text = htmlspecialchars_decode($text);
        }

        // Initialise parser
        if (!isset(static::$parser)) {
            static::$parser = new ParsedownExtra();
            static::$parser->setSafeMode(true);
        }

        // Parse the text
        $text = static::$parser->text($text);
    }
}
