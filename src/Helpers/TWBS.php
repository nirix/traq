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

use Radium\Action\View;
use Radium\Helpers\HTML;
use Radium\Language;

/**
 * Twitter Bootstrap Helper.
 *
 * @author Jack P.
 * @package Traq\Helpers
 * @since 4.0
 */
class TWBS
{
    /**
     * Returns the HTML for the caret used in dropdowns.
     *
     * @return string
     */
    public static function caret()
    {
        return '<b class="caret"></b>';
    }

    /**
     * Returns the HTML for FontAwesome icons.
     *
     * @param string $icon Name of the icon to use.
     *
     * @return string
     */
    public static function fa($icon, $text = null)
    {
        $html = "<span class=\"fa fa-{$icon}\"></span>";

        if ($text) {
            $html = "{$html} {$text}";
        }

        return $html;
    }

    /**
     * Returns the HTML of an alert.
     *
     * @param string  $message
     * @param string  $class   Alert class
     * @param boolean $dismissable
     *
     * @return string
     */
    public static function alert($message, $class = 'info', $dismissable = true)
    {
        if ($dismissable) {
            $class = "{$class} alert-dismissable";
        }

        $alert = ['<div class="alert alert-' . $class . '">'];

        if ($dismissable) {
            $alert[] = '    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
        }

        $alert[] = "    {$message}";
        $alert[] = "</div>";

        return implode(PHP_EOL, $alert);
    }

    /**
     * Returns the HTML for a progress bar.
     *
     * @param integer $value
     * @param array   $options
     *
     * @return string
     */
    public static function progressBar($value, $options)
    {
        $attributes = array(
            'class' => "progress-bar {$options['style']}",
            'role' => "progressbar",
            'aria-valuenow' => $value,
            'aria-valuemin' => 0,
            'aria-valuemax' => 100,
            'style' => "width:{$value}%;"
        );

        unset($options['style']);

        $attributes = array_merge($attributes, $options);

        $attributes = HTML::buildAttributes($attributes);

        return "<div {$attributes}></div>";
    }

    /**
     * Returns the HTML for a modal header.
     *
     * @param string $title
     *
     * @return string
     */
    public static function modalHeader($title)
    {
        $header = array(
            '<div class="modal-header">',
            '    <button type="button" class="close" data-dismiss="modal">',
            '        <span aria-hidden="true">&times;</span>',
            '        <span class="sr-only">' . Language::translate('close') . '</span>',
            '    </button>',
            '    <h4 class="modal-title">' . $title . '</h4>',
            '</div>'
        );

        return implode(PHP_EOL, $header);
    }
}
