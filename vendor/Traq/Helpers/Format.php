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

namespace Traq\Helpers;

use Radium\Kernel as Radium;
use Radium\Hook;
use Traq\Models\Project;

class Format extends \Radium\Helpers\Format
{
    /**
     * Formats the supplied text.
     *
     * @param string $text
     * @param bool $strip_html Disables HTML, making it safe.
     *
     * @return string
     */
    public static function text($text, $strip_html = true)
    {
        $text = $strip_html ? htmlspecialchars($text) : $text;

        Hook::run('function:format_text', array(&$text, $strip_html));

        // Ticket links
        $text = static::ticketLinks($text);

        // Wiki links
        $text = static::wikiLinks($text);

        return $text;
    }

    /**
     * Links #123 and project#123 to the corresponding ticket.
     *
     * @param string $text
     *
     * @return string
     */
    public static function ticketLinks($text)
    {
        return preg_replace_callback(
            "|(?:[\w\d\-_]+)?#([\d]+)|",
            function($matches){
                $match = explode('#', $matches[0]);

                // Replace project#123
                if (isset($match[1]) and $project = Project::find('slug', $match[0])) {
                    return HTML::link("{$project->slug}#{$match[1]}", $project->href("tickets/{$match[1]}"));
                }
                // Replace #123
                elseif (isset(Avalon::app()->project->name)) {
                    return HTML::link("#{$match[1]}", Avalon::app()->project->href("tickets/{$match[1]}"));
                }
                // No project found, don't link it
                else {
                    return "#{$match[1]}";
                }
            },
            $text
        );
    }

    /**
     * Converts the wiki [[page]] and [[text|page]] to HTML links.
     *
     * @param string $text
     *
     * @return string
     */
    public static function wikiLinks($text)
    {
        return preg_replace_callback(
            "|\[\[(?P<page>[\w\d\-_]+)(\|(?P<text>[\s\w\d\-_]+))?\]\]|",
            function($matches){
                $project = Avalon::app()->project;

                if (!isset($matches['text'])) {
                    $matches['text'] = $matches['page'];
                }

                return HTML::link($matches['text'], $project->href("wiki/{$matches['page']}"));
            },
            $text
        );
    }
}
