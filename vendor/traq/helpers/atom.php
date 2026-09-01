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

namespace traq\helpers;

/**
 * Atom feed generator.
 *
 * @author Jack P.
 * @copyright 2012 Jack P.
 */
class Atom
{
    private $title;
    private $entries;
    private $link;
    private $feed_link;
    private $updated;

    /**
     * Feed constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        foreach ($options as $option => $value) {
            $this->{$option} = $value;
        }
    }

    /**
     * Builds the feed.
     *
     * @return string
     */
    public function build()
    {
        $feed = array();

        $feed[] = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
        $feed[] = "<feed xmlns=\"http://www.w3.org/2005/Atom\">";
        $feed[] = "  <title>" . static::xml($this->title) . "</title>";
        $feed[] = "  <link href=\"" . static::xml($this->link) . "\" />";
        $feed[] = "  <link href=\"" . static::xml($this->feed_link) . "\" rel=\"self\" />";
        $feed[] = "  <updated>" . static::xml($this->updated) . "</updated>";


        foreach ($this->entries as $entry) {
            $feed[] = "  <entry>";
            $feed[] = "    <title>" . static::xml($entry['title']) . "</title>";
            $feed[] = "    <id>" . static::xml($entry['id']) . "</id>";
            $feed[] = "    <updated>" . static::xml($entry['updated']) . "</updated>";

            // Link
            if (isset($entry['link'])) {
                $feed[] = "    <link href=\"" . static::xml($entry['link']) . "\" />";
            }

            // Summary
            if (isset($entry['summary'])) {
                $feed[] = "    <summary>" . static::xml($entry['summary']) . "</summary>";
            }

            // Author
            if (isset($entry['author'])) {
                $feed[] = "    <author>";
                $feed[] = "      <name>" . static::xml($entry['author']['name']) . "</name>";
                $feed[] = "    </author>";
            }

            // Content
            if (isset($entry['content'])) {
                $contentType = array_key_exists('type', $entry['content']) ? $entry['content']['type'] : '';
                $content = $entry['content']['data'];
                if (strcasecmp((string) $contentType, 'HTML') !== 0) {
                    $content = static::xml($content);
                }
                $feed[] = "    <content" . ($contentType !== '' ? " type=\"" . static::xml($contentType) . "\"" : '') . ">";
                $feed[] = "      {$content}";
                $feed[] = "    </content>";
            }

            $feed[] = "  </entry>";
        }

        $feed[] = "</feed>";

        return implode(PHP_EOL, $feed);
    }

    private static function xml($value): string
    {
        return htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
