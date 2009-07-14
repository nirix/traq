<?php
/**
 * Traq 2
 * Copyright (c) 2009 Jack Polgar
 * All Rights Reserved
 *
 * $Id$
 */

/**
 * Settings
 * Used to get the value of the specified setting.
 * @param string $setting The setting...
 */
function settings($setting)
{
	global $CACHE, $db;
	
	// Check if the setting has already been fetched
	// and return it if it has.
	if(isset($CACHE['settings']['$setting'])) return $CACHE['settings'][$setting];
	
	// Looks like the setting isn't in the cache,
	// lets fetch it now...
	$result = $db->fetcharray($db->query("SELECT setting, value FROM ".DBPF."settings WHERE setting='".$db->es($setting)."' LIMIT 1"));
	$CACHE['settings'][$setting] = $result['value'];
	
	($hook = FishHook::hook('settings_function')) ? eval($hook) : false;
	
	return $CACHE['settings'][$setting];
}

/**
 * Template
 * Used to easily fetch templates.
 * @param string $template Template Name
 * @return string
 */
function template($template)
{
	($hook = FishHook::hook('template_function')) ? eval($hook) : false;
	
	// Check if the template exists
	if(file_exists(TRAQPATH.'/templates/'.settings('theme').'/'.$template.".php")) {
		return TRAQPATH.'/templates/'.settings('theme').'/'.$template.".php";
	} else {
	// Display an error it we couldn't load it
		error("Template","Unable to load file: <code>".settings('theme')."/".$template."</code>");
	}
}

/**
 * Add Breadcrumb
 * Used to easily add breadcrumbs.
 * @param string $url The URL.
 * @param string $label The Label.
 */
function addcrumb($url,$label)
{
	global $breadcrumbs;
	
	$breadcrumbs[] = array('url'=>$url,'label'=>$label);
}

/**
 * Error
 * Used to display an error message.
 * @param string $title Error title.
 * @param string $message Error message.
 */
function error($title,$message)
{
	die("<blockquote style=\"border:2px solid darkred;padding:5px;background:#f9f9f9;font-family:arial; font-size: 14px;\"><h1 style=\"margin:0px;color:#000;border-bottom:1px solid #000;margin-bottom:10px;\">".$title." Error</h1><div style=\"padding: 0;\">".$message."</div><div style=\"color:#999;border-top:1px solid #000;margin-top:10px;font-size:small;padding-top:2px;\">Traq ".TRAQVER." &copy; 2009 Jack Polgar</div></blockquote>");
}

/**
 * Locale String
 * Gets the specified locale string for the set language.
 * @param string $string String name/key
 * @param mixed $vars
 * @return string
 */
function l($string,$vars=array())
{
	global $lang;
	
	// Check if the string exists
	if(!isset($lang[$string])) return '['.$string.']';
	
	// Get the locale string
	$string = $lang[$string];
	
	// Check if the $vars is an array or use the function args.
	if(!is_array($vars)) $vars = array_slice(func_get_args(),1);
	
	// Loop through the vars and replace the the {x} stuff
	foreach($vars as $var)
	{
		++$v;
		$string = str_replace('{'.$v.'}',$var,$string);
	}
	
	($hook = FishHook::hook('locale_function')) ? eval($hook) : false;
	
	// Now return it...
	return $string;
}

/**
 * Is Project
 * Check if the supplied string is a project.
 * @param string $string String to check if a project exists with that slug.
 * @return integer
 */
function is_project($string)
{
	global $db;
	return $db->numrows($db->query("SELECT slug FROM ".DBPF."projects WHERE slug='".$db->escapestring($string)."' LIMIT 1"));
}

/**
 * Simple if()
 * Used to easy execute a condition.
 */
function iif($condition, $true, $false='')
{
	return ($condition ? $true : $false);
}

// Stupid function to easily make a simple array.
function a()
{
	return func_get_args();
}

/**
 * Ticket Types
 * Fetches the Ticket Types specified in the AdminCP.
 */
function ticket_types()
{
	global $db;
	
	$types = array();
	$fetch = $db->query("SELECT * FROM ".DBPF."ticket_types ORDER BY id ASC");
	while($info = $db->fetcharray($fetch)) {
		$types[] = $info;
	}
	($hook = FishHook::hook('function_ticket_types')) ? eval($hook) : false;
	return $types;
}

/**
 * Ticket Priorities
 * Fetches the Ticket Priorities specified in the AdminCP.
 */
function ticket_priorities()
{
	global $db;
	
	$priorities = array();
	$fetch = $db->query("SELECT * FROM ".DBPF."priorities ORDER BY id DESC");
	while($info = $db->fetcharray($fetch)) {
		$priorities[] = $info;
	}
	($hook = FishHook::hook('function_ticket_priorities')) ? eval($hook) : false;
	return $priorities;
}

/**
 * Ticket Severities
 * Fetches the Ticket Severities specified in the AdminCP.
 */
function ticket_severities()
{
	global $db;
	
	$severities = array();
	$fetch = $db->query("SELECT * FROM ".DBPF."severities ORDER BY id ASC");
	while($info = $db->fetcharray($fetch)) {
		$severities[] = $info;
	}
	($hook = FishHook::hook('function_ticket_severities')) ? eval($hook) : false;
	return $severities;
}

/**
 * Project Milestones
 * Fetches the project milestones.
 */
function project_milestones($project_id=NULL)
{
	global $project, $db;
	$project_id = ($project_id == NULL ? $project['id'] : $project_id);
	
	$milestones = array();
	$fetch = $db->query("SELECT * FROM ".DBPF."milestones WHERE project_id='".$db->es($project_id)."' ORDER BY displayorder ASC");
	while($info = $db->fetcharray($fetch))
	{
		$milestones[] = $info;
	}
	($hook = FishHook::hook('function_project_milestones')) ? eval($hook) : false;
	return $milestones;
}

/**
 * Calcuate Percent
 * Used to calculate the percent of two numbers,
 * if both numbers are the same, 100(%) is returned.
 * @param integer $min Lowest number
 * @param integer $max Highest number
 * @return integer
 */
function getpercent($min,$max)
{
	if($min == $max) return 100;
	
	$calculate = ($min/$max*100);
	$split = explode('.',$calculate);
	return $split[0];
}

/**
 * Time Since
 * @param integer $original Original Timestamp
 * @param integer $detailed Detailed format or not
 * @return string
 */
function timesince($original, $detailed = false)
{
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
	
	// Loop around, get the time since
	for($i = 0, $c = count($chunks); $i < $c; $i++)
	{
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];
		$names = $chunks[$i][2];
		if(0 != $count = floor($difference / $seconds)) {
			break;
		}
	}
	
	// Format the time since
	//$since = $count." ".((1 == $count) ? $name : $names);
	$since = l('x_'.((1 == $count) ? $name : $names),$count);
	
	// Get the detailed time since if the detaile variable is true
	if($detailed && $i + 1 < $c)
	{
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		$names2 = $chunks[$i + 1][2];
		if(0 != $count2 = floor(($difference - $seconds * $count) / $seconds2))
			$since = l('x_and_x',$since,l('x_'.((1 == $count2) ? $name2 : $names2),$count2));
	}
	
	// Return the time since
	return $since;
}

/**
 * Time From
 * @param integer $original Original Timestamp
 * @param integer $detailed Detailed format or not
 * @return string
 */
function timefrom($original, $detailed = false)
{
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
	$difference = ($original - $now);
	
	// Loop around, get the time from
	for($i = 0, $c = count($chunks); $i < $c; $i++)
	{
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];
		$names = $chunks[$i][2];
		if(0 != $count = floor($difference / $seconds)) {
			break;
		}
	}
	
	// Format the time from
	$from = l('x_'.((1 == $count) ? $name : $names),$count);
	
	// Get the detailed time from if the detaile variable is true
	if($detailed && $i + 1 < $c)
	{
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		$names2 = $chunks[$i + 1][2];
		if(0 != $count2 = floor(($difference - $seconds * $count) / $seconds2))
			$from = l('x_and_x',$from,l('x_'.((1 == $count2) ? $name2 : $names2),$count2));
	}
	
	// Return the time from
	return $from;
}
?>