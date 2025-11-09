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
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;
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
    protected static MarkdownConverter $parser;

    protected static $info = [
        'name'    => 'Markdown',
        'version' => '4.0.0',
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
    public static function format_text(&$text, $stripHtml): void
    {
        // If HTML is being converted to text, undo it.
        if ($stripHtml) {
            $text = htmlspecialchars_decode($text);
        }

        // Initialise parser
        if (!isset(static::$parser)) {
            static::setupParser();
        }

        // Parse the text
        $text = static::$parser->convert($text);
    }

    private static function setupParser(): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new AutolinkExtension());
        $environment->addExtension(new DisallowedRawHtmlExtension());
        $environment->addExtension(new StrikethroughExtension());
        $environment->addExtension(new TableExtension());

        static::$parser = new MarkdownConverter($environment);
    }
}
