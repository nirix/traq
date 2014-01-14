<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

namespace traq\plugins;

require __DIR__ . '/Michelf/Markdown.php';
require __DIR__ . '/Michelf/MarkdownExtra.php';

use \FishHook;

/**
 * Markdown Plugin.
 *
 * @package Traq
 * @subpackage Plugins
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Markdown extends \traq\libraries\Plugin
{
    protected static $parser;

    protected static $info = array(
        'name'    => 'Markdown',
        'version' => '2.0.1',
        'author'  => 'Jack P.'
    );

    /**
     * Handles the startup of the plugin.
     */
    public static function init()
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
            static::$parser = new \Michelf\MarkdownExtra;
            static::$parser->no_markup = true;
        }

        // Parse the text
        $text = static::$parser->transform($text);
    }
}
