<?php
/*
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

function time_ago_in_words($original, $detailed = true)
	{
		// Check what kind of format we're dealing with, timestamp or datetime
		// and convert it to a timestamp if it is in datetime form.
		if (!is_numeric($original)) {
			$original = Time::to_unix($original);
		}
		
		$now = time(); // Get the time right now...

		// Time chunks...
		$chunks = array(
			array(60 * 60 * 24 * 365, 'year', 'years'),
			array(60 * 60 * 24 * 30, 'month', 'months'),
			array(60 * 60 * 24 * 7, 'week', 'weeks'),
			array(60 * 60 * 24, 'day', 'days'),
			array(60 * 60, 'hour', 'hours'),
			array(60, 'minute', 'minutes'),
			array(1, 'second', 'seconds'),
		);

		// Get the difference
		$difference = ($now - $original);

		// Loop around, get the time from
		for ($i = 0, $c = count($chunks); $i < $c; $i++) {
			$seconds = $chunks[$i][0];
			$name = $chunks[$i][1];
			$names = $chunks[$i][2];
			if(0 != $count = floor($difference / $seconds)) break;
		}

		// Format the time from
		$from = $count . " " . (1 == $count ? $name : $names);

		// Get the detailed time from if the detaile variable is true
		if ($detailed && $i + 1 < $c) {
			$seconds2 = $chunks[$i + 1][0];
			$name2 = $chunks[$i + 1][1];
			$names2 = $chunks[$i + 1][2];
			if (0 != $count2 = floor(($difference - $seconds * $count) / $seconds2)) {
				$from = $from . " and " . $count2 . " " . (1 == $count2 ? $name2 : $names2);
			}
		}

		// Return the time from
		return $from;
	}