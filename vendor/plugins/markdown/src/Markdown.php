<?php
/**
 * This plugin uses the Parsedown library to format text.
 *
 * Parsedown was created by erusev and is released under the MIT license.
 *
 * https://github.com/erusev
 * https://github.com/erusev/parsedown
 * https://github.com/erusev/parsedown-extra
 */

namespace Markdown;

use Radium\Hook;
use Traq\Plugin;
use ParsedownExtra;

/**
 * Markdown Plugin.
 *
 * @package Plugins
 */
class Markdown extends Plugin
{
    /**
     * Parsedown instance.
     *
     * @var ParsedownExtra
     */
    protected static $parsedown;

    /**
     * Instantiate ParsedownExtra.
     */
    public static function init()
    {
        static::$parsedown = new ParsedownExtra;
    }

    /**
     * Hook into `Format::text()`.
     */
    public static function enable()
    {
        Hook::add('function:Format::text', array('Markdown\Markdown', 'format'));
    }

    /**
     * Format the text with Parsedown.
     *
     * @param string $text
     */
    public static function format(&$text)
    {
        $text = static::$parsedown->text($text);
    }
}
