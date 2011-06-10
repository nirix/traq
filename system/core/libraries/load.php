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
 * Loader
 * @package Meridian
 */
class Load
{
	private static $helpers = array();
	private static $classes = array();
	private static $models = array();
	
	/**
	 * Loads a library from the applications library directory.
	 * @param string $name The library file and class name.
	 * @param bool $init Initialize the class or not.
	 * @return object
	 */
	public static function library($name, $init = true)
	{
		if($init and in_array($name, self::$classes)) return self::$classes[$name];
		
		if(file_exists(APPPATH.'libraries/'.strtolower($name).'.php'))
			require_once APPPATH.'libraries/'.strtolower($name).'.php';
		else
			Meridian::error('Loader Error','Unable to load library: '.$name);
		
		if($init)
		{
			self::$classes[$name] = new $name();
			self::$classes[$name]->db = Meridian::$db;
			
			return self::$classes[$name];
		}
		else return true;
	}
	
	/**
	 * Loads a helper from either the application or meridian helper directory.
	 * @param string $file The name of the helper file.
	 * @return bool
	 */
	public static function helper($file)
	{
		if(in_array($file, self::$helpers)) return true;
		
		if(file_exists(APPPATH.'helpers/'.strtolower($file).'.php'))
			require_once APPPATH.'helpers/'.strtolower($file).'.php';
		else if(file_exists(SYSPATH.'helpers/'.strtolower($file).'.php'))
			require_once SYSPATH.'helpers/'.strtolower($file).'.php';
		else
			Meridian::error('Loader Error','Unable to load helper: '.$file);
		
		self::$helpers[] = $file;
		return true;
	}
	
	/**
	 * Loads a model form the applications model directory.
	 * @param string $name The model name to load.
	 * @return object
	 */
	public static function model($name)
	{
		if(!class_exists('Model')) require_once SYSPATH.'libraries/model.php';
		
		if(in_array($name, self::$models)) return self::$models[$name];
		
		if(file_exists(APPPATH.'models/'.strtolower($name).'.php'))
			require_once APPPATH.'models/'.strtolower($name).'.php';
		else
			Meridian::error('Loader Error','Unable to load model: '.$name);
		
		$modelName = $name.'Model';
		self::$models[$name] = new $modelName(strtolower($name));
		self::$models[$name]->db = Meridian::$db;
		
		return self::$models[$name];
	}
}