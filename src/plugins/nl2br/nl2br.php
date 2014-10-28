<?php
/*!
 * Traq
 * Copyright (C) 2009-2013 Traq.io
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

use \FishHook;

/**
 * Line break plugin.
 *
 * @package Traq
 * @subpackage Plugins
 * @author Jack P.
 * @copyright (c) Jack P.
 */
class Nl2br extends \traq\libraries\Plugin
{
    protected static $info = array(
        'name'    => 'Enable nl2br <abbr style="border:1px solid red;border-radius:8px; padding:0 5px;" class="error" title="Will break some text formatting in Markdown.">!</abbr>',
        'version' => '1.0',
        'author'  => 'Jack P.'
    );

    /**
     * Plugin Initializer
     */
    public static function init()
    {
        FishHook::add('function:format_text', array(get_called_class(), 'format_text'));
    }

    /**
     * Converts new lines to breaks.
     *
     * @param string $text
     */
    public static function format_text(&$text)
    {
        $text = nl2br($text);
    }
}
