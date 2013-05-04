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

use avalon\core\Kernel as Avalon;

use traq\models\Setting;
use traq\models\Project;

use traq\libraries\SCM;

/**
 * Returns the value of the requested setting.
 *
 * @param string $setting The setting to fetch
 *
 * @return string
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 */
function settings($setting) {
    static $CACHE = array();

    if (isset($CACHE[$setting])) {
        return $CACHE[$setting];
    }

    $data = Setting::find($setting);

    $CACHE[$setting] = $data ? $data->value : null;
    return $CACHE[$setting];
}

/**
 * Returns the value of the requested localization string.
 *
 * @return string
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 */
function l()
{
    global $locale;
    return call_user_func_array(array($locale, 'translate'), func_get_args());
}

/**
 * Returns the localized date.
 *
 * @param string $format
 * @param mixed $timestamo
 *
 * @return string
 */
function ldate()
{
    global $locale;
    return call_user_func_array(array($locale, 'date'), func_get_args());
}

/**
 * Returns a list of available localisations formatted
 * for the Form::select() helper.
 *
 * @return array
 */
function locale_select_options()
{
    $options = array();

    foreach (scandir(APPPATH . '/locale') as $file) {
        if (substr($file, -4) == '.php') {
            // Clean the name and set the class
            $name = substr($file, 0, -4);
            $class = "\\traq\locale\\{$name}";

            // Make sure the locale class
            // isn't already loaded
            if ($name != settings('locale') and $name != Avalon::app()->user->locale) {
                require APPPATH . '/locale/' . $file;
            }

            // Get the info
            $info = $class::info();

            // Add it to the options
            $options[] = array(
                'label' => "{$info['name']} ({$info['language_short']}{$info['locale']})",
                'value' => substr($file, 0, -4)
            );
        }
    }

    return $options;
}

/**
 * Returns a list of available themes formatted
 * for the Form::select() helper.
 *
 * @return array
 */
function theme_select_options()
{
    $options = array();

    foreach (scandir(APPPATH . '/views') as $file) {
        $path = APPPATH . '/views/' . $file;

        // Make sure this is a directory
        // and theres an _theme.php file
        if (is_dir($path) and file_exists($path . '/_theme.php')) {
            $info = require ($path . '/_theme.php');
            $options[] = array(
                'label' => l('admin.theme_select_option', $info['name'], $info['version'], $info['author']),
                'value' => $file
            );
        }
    }

    return $options;
}

/**
 * Formats the plugin directory name into
 * a class name.
 *
 * @param string $name
 *
 * @return string
 */
 function get_plugin_name($name)
 {
     $bits = explode('_', $name);
     foreach ($bits as $k => $v) {
         $bits[$k] = ucfirst($v);
     }
     return implode('', $bits);
 }

/**
 * Checks if the given regex matches the request
 *
 * @param string $uri
 *
 * @return bool
 */
function active_nav($uri)
{
    $uri = str_replace(
        array(':slug', ':any', ':num'),
        array('([a-zA-Z0-9\-\_]+)', '(.*)', '([0-9]+)'),
        $uri
    );
    return preg_match("#^{$uri}$#", Request::uri());
}

/**
 * Returns the logged in users model.
 *
 * @return object
 */
function current_user()
{
    return Avalon::app()->user;
}

/**
 * Checks the condition and returns the respective value.
 *
 * @param bool $condition
 * @param mixed $true
 * @param mixed $false
 *
 * @return mixed
 */
function iif($condition, $true, $false = null)
{
    return $condition ? $true : $false;
}

/**
 * Returns an array of the available permissions.
 *
 * @return array
 */
function permission_actions()
{
    global $locale;

    // Fetch the locale strings
    $locale_strings = $locale->locale();

    // Loop over them and get the permissions...
    $actions = array();
    foreach ($locale_strings['permissions'] as $action => $string) {
        // Is this a grouped set of permissions?
        if (is_array($string)) {
            // Add them to the actions array
            foreach ($string as $act => $str) {
                $actions[$action][] = $act;
            }
        }
        // Non group permission
        else {
            $actions[] = $action;
        }
    }

    return $actions;
}

/**
 * Returns an array of available SCMs.
 *
 * @return array
 */
function scm_types()
{
    static $scms = array();

    if (count($scms) == 0) {
        foreach (scandir(APPPATH . "/libraries/scm/adapters") as $file) {
            if (substr($file, -3) == 'php') {
                $name = str_replace('.php', '', $file);
                $scm = SCM::factory($name);
                $scms[$name] = $scm->name();
            }
        }
    }

    FishHook::run('function:scm_types', array(&$scms));
    return $scms;
}

/**
 * Returns the available SCMS as form select options.
 *
 * @return array
 */
function scm_select_options()
{
    $options = array();
    foreach (scm_types() as $scm => $name) {
        $options[] = array('label' => $name, 'value' => $scm);
    }
    return $options;
}

/**
 * Checks if the specified field is a project or not.
 *
 * @param mixed $find Value [or column] to search for.
 * @param mixed $field Column [or value].
 *
 * @return object
 *
 * @author Jack P.
 * @copyright Copyright (c) Jack P.
 * @package Traq
 */
function is_project($find, $field = 'slug') {
    if ($project = Project::find($field, $find)) {
        return $project;
    } else {
        return false;
    }
}

/**
 * Used to generate a random hash.
 *
 * @return string
 */
function random_hash()
{
    $chars = "qwertyuiopasdfghjklzxcvbnm[];',./{}|:<>?1234567890!@#$%^&*()";
    return sha1(time() . rand(0, 1000) . $chars[rand(0, count($chars))] . microtime());
}

/**
 * Calculates the percent of two numbers,
 * if both numbers are the same, 100(%) is returned.
 *
 * @param integer $min Lowest number
 * @param integer $max Highest number
 *
 * @return integer
 */
function get_percent($min, $max)
{
    // Make sure we don't divide by zero
    // and end the entire universe
    if ($min == 0 and $max == 0) return 0;

    // We're good, calcuate it like a boss,
    // toss out the crap we dont want.
    $calculate = ($min/$max*100);
    $split = explode('.',$calculate);

    // Return it like a pro.
    return $split[0];
}
