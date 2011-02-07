<?php
/**
 * Traq 2
 * Copyright (C) 2009, 2010 Jack Polgar
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
 *
 * $Id$
 */

class RSSFeed
{
	private $title = NULL;
	private $link = NULL;
	private $desc = NULL;
	private $items = array();
	
	public function __construct($title,$link,$desc,$items)
	{
		$this->title = $title;
		$this->link = $link;
		$this->desc = $desc;
		$this->items = $items;
	}
	
	public function output()
	{
		$pubDate = 0;
		
		foreach($this->items as $item)
			if($item['timestamp'] > $pubDate)
				$pubDate = $item['timestamp'];
		
		header('Content-type: application/rss+xml');
		echo '<'.'?'.'xml version="1.0" encoding="UTF-8"'.'?'.'>'.PHP_EOL;
		echo "<rss version=\"2.0\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">".PHP_EOL;
		echo "	<channel>".PHP_EOL;
		echo "		<title><![CDATA[".$this->title."]]></title>".PHP_EOL;
		echo "		<link>".$this->link."</link>".PHP_EOL;
		echo "		<description><![CDATA[".$this->desc."]]></description>".PHP_EOL;
		echo "		<pubDate>".date("D, d M Y H:i:s O",$pubDate)."</pubDate>".PHP_EOL;
		echo "		<generator>Traq</generator>".PHP_EOL;
		
		foreach($this->items as $item)
		{
			echo "		<item>".PHP_EOL;
			echo "			<title><![CDATA[".$item['title']."]]></title>".PHP_EOL;
			echo "			<link>".$item['link']."</link>".PHP_EOL;
			echo "			<pubDate>".date("D, d M Y H:i:s O",$item['timestamp'])."</pubDate>".PHP_EOL;
			echo "			<guid isPermaLink=\"false\">".$item['guid']."</guid>".PHP_EOL;
			echo "			<description><![CDATA[".$item['content']."]]></description>".PHP_EOL;
			echo "			<content:encoded><![CDATA[".$item['content_encoded']."]]></content:encoded>".PHP_EOL;
			echo "		</item>".PHP_EOL;
		}
		
		echo "	</channel>".PHP_EOL;
		echo "</rss>";
	}
}
