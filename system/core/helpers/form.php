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
 */

/**
 * Form Helper
 * @package Meridian
 * @subpackage Helpers
 */
class Form
{
	public static function text($name,$args=array())
	{
		return self::input('text',$name,$args);
	}
	
	public static function password($name,$args=array())
	{
		return self::input('password',$name,$args);
	}
	
	public static function hidden($name,$value)
	{
		return self::input('hidden',$name,array('value'=>$value));
	}
	
	public static function submit($text,$name='submit')
	{
		return self::input('submit',$name,array('value'=>$text));
	}
	
	public static function textarea($name,$args=array())
	{
		return self::input('textarea',$name,$args);
	}
	
	public static function checkbox($name,$value,$args=array())
	{
		$args['value'] = $value;
		return self::input('checkbox',$name,$args);
	}
	
	public static function select($name,$values,$args=array())
	{
		$select = '<select name="'.$name.'"'.(isset($args['id']) ? ' id="'.$args['id'].'"' :'').'>';
		
		foreach($values as $value)
		{
			$select .= '<option value="'.$value['value'].'"'.(@$args['value'] == $value['value'] ? ' selected="selected"' :'').'>'.$value['text'].'</option>';
		}
		
		$select .= '</select>';
		
		return $select;
	}
	
	public static function input($type,$name,$args)
	{
		if(isset($args['value']))
			$value = $args['value'];
		elseif(isset($_POST[$name]))
			$value = $_POST[$name];
		else
			$value = '';
		
		if($type == 'text'
		or $type == 'password'
		or $type == 'hidden')
		{
			return '<input type="'.$type.'" name="'.$name.'" value="'.$value.'"'.(isset($args['id']) ? ' id="'.$args['id'].'"' :'').' class="'.$type.'" />';
		}
		elseif($type == 'textarea')
		{
			return '<textarea name="'.$name.'"'.(isset($args['id']) ? ' id="'.$args['id'].'"' :'').(isset($args['class']) ? ' class="'.$args['class'].'"' :'').(isset($args['cols']) ? ' cols="'.$args['cols'].'"' :'').(isset($args['rows']) ? ' rows="'.$args['rows'].'"' :'').'>'.$value.'</textarea>';
		}
		elseif($type == 'submit')
		{
			return '<input type="'.$type.'" value="'.$value.'" class="'.$type.'" />';
		}
		elseif($type == 'checkbox')
		{
			return '<input type="'.$type.'" name="'.$name.'" value="'.$value.'" class="'.$type.'" '.(isset($args['checked']) && $args['checked'] ? 'checked ' :'').(isset($args['id']) ? ' id="'.$args['id'].'"' :'').'/>';
		}
	}
}