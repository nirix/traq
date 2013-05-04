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

use \avalon\core\Kernel as Avalon;
use \traq\models\Project;

/**
 * Formats the supplied text.
 *
 * @param string $text
 * @param bool $strip_html Disables HTML, making it safe.
 *
 * @return string
 */
function format_text($text, $strip_html = true)
{
    $text = $strip_html ? htmlspecialchars($text) : $text;

    FishHook::run('function:format_text', array(&$text, $strip_html));

    // Ticket links
    $text = ticket_links($text);

    return $text;
}

/**
 * Links #123 and project#123 to the corresponding ticket.
 *
 * @param string $text
 *
 * @return string
 */
function ticket_links($text)
{
    $match = preg_match_all("/\s+(?P<ticket_id>(?:[a-zA-Z0-9\-\_]+)?#[0-9]+)\s+/", $text, $matches);
    foreach ($matches['ticket_id'] as $match) {
        $match = explode('#', $match);

        // Replace project#123
        if ($project = Project::find('slug', $match[0])) {
            $text = str_replace(
                "{$project->slug}#{$match[1]}",
                HTML::link("{$project->slug}#{$match[1]}", $project->href("tickets/{$match[1]}")),
                $text
            );
        }
        // Replace #123
        elseif (empty($match[0])) {
            $text = str_replace(
                " #{$match[1]} ",
                " " . HTML::link("#{$match[1]}", Avalon::app()->project->href("tickets/{$match[1]}")) . " ",
                $text
            );
        }
    }

    return $text;
}
