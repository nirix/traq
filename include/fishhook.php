<?php
/**
 * FishHook 2.0
 * Easy add plugin support to your scripts.
 *
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved.
 */
class FishHook
{
	// Hooks Array
	private static $hooks = array();
	
	/**
	 * Fetch Hook
	 * Used to fetch the hook and return the plugin code.
	 *
	 * @param string $hook Hook name.
	 * @return string Plugin Code.
	 */
	public function hook($hook)
	{
		$code = array();
		foreach(self::$hooks[$hook] as $id => $function)
		{
			$code[] = $function();
		}
		return implode(' /* */ ',$code);
	}
	
	/**
	 * Add Function
	 * Adds a function to the specified hook.
	 *
	 * @param string $hook Hook name.
	 * @param string $function Function name.
	 */
	public function add($hook,$function)
	{
		self::$hooks[$hook][] = $function;
	}
	
	public function version()
	{
		return '2.0';
	}
}
?>