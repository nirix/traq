<?php
/**
 * Meridian
 * Copyright (C) 2010-2011 Jack Polgar
 * 
 * This file is part of Meridian.
 * 
 * Meridian is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 only.
 * 
 * Meridian is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Meridian. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Meridian
 */

/**
 * Returns the base URL to the application.
 * @package Meridian
 * @return string
 */
function baseurl()
{
	return str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']).(func_num_args() > 0 ? implode('/', func_get_args()) : '');
}

/**
 * Select array index
 * @package Meridian
 * @param mixed $index Index to select.
 * @param array $array The array to select from.
 * @return mixed
 */
function aselect($index, array $array)
{
	return $array[$index];
}

/**
 * Facebook Like Button
 * @package Meridian/SocialNetworks
 */
function fbLikeBtn($url, $layout = 'button_count', $color = 'light')
{
	return '<iframe src="http://www.facebook.com/plugins/like.php?href='.urlencode($url).'&amp;layout='.$layout.'&amp;show_faces=false&amp;width=90&amp;action=like&amp;font=arial&amp;colorscheme='.$color.'&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:21px;" allowTransparency="true"></iframe>';
}