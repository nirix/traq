<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
 * https://github.com/nirix
 * http://traq.io
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

use Avalon\Http\Request;
use Avalon\Output\View;
use Traq\Models\Setting;
use Traq\Models\Project;
use Traq\Models\User;

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
function settings(string $setting): string
{
    static $cache = [];

    if (isset($cache[$setting])) {
        return $cache[$setting];
    }

    // Cache all settings
    $settings = Setting::fetchAll();
    foreach ($settings as $row) {
        $cache[$row->setting] = $row->value;
    }
    unset($settings, $row);

    return $cache[$setting];
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
 * Path to a locale file, or false.
 * The code must be a single filename, then it is matched against files that
 * actually exist. "../index" never becomes part of the path.
 *
 * @param mixed $locale
 *
 * @return string|false
 */
function locale_file($locale)
{
    if (!is_string($locale) || $locale === '' || basename($locale) !== $locale) {
        return false;
    }

    $directories = [DOCROOT . '/src/Traq/Locale'];
    if (defined('DATADIR')) {
        $directories[] = DATADIR . '/locale';
    }

    foreach ($directories as $directory) {
        if (!is_dir($directory)) {
            continue;
        }

        foreach (scandir($directory) as $file) {
            if (strcasecmp($file, $locale . '.php') === 0) {
                return $directory . '/' . $file;
            }
        }
    }

    return false;
}

/**
 * Whether $locale names a file in a locale directory.
 *
 * @param mixed $locale
 *
 * @return bool
 */
function locale_exists($locale)
{
    return locale_file($locale) !== false;
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

    $paths = [
        DOCROOT . "/src/Traq/Locale",
        DATADIR . "/locale",
    ];

    $locales = [];
    foreach ($paths as $path) {
        foreach (scandir($path) as $file) {
            if (substr($file, -4) == '.php') {
                $name = substr($file, 0, -4);
                $class = "\\Traq\Locale\\{$name}";

                $info = $class::info();
                $locales[] = [
                    'label' => "{$info['name']} ({$info['language_short']}{$info['locale']})",
                    'value' => $name
                ];
            }
        }
    }

    return $locales;
}

/**
 * Returns a list of available themes formatted
 * for the Form::select() helper.
 *
 * @return array
 */
function theme_select_options(): array
{
    $options = [];

    // Scan data/themes/*/_theme.php and vendor/traq/views/*/_theme.php
    $themePaths = [
        DOCROOT . '/src/views',
        DATADIR . '/themes',
    ];

    foreach ($themePaths as $path) {
        foreach (scandir($path) as $file) {
            if (is_dir($path . '/' . $file) && file_exists($path . '/' . $file . '/_theme.php')) {
                $info = require($path . '/' . $file . '/_theme.php');
                $options[] = [
                    'label' => l('admin.theme_select_option', $info['name'], $info['version'], $info['author']),
                    'value' => $file
                ];
            }
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
 * @deprecated Use currentUser() instead
 */
function current_user()
{
    return currentUser();
}

function currentUser(): User|null
{
    return Request::attribute('current_user');
}

function currentProject(): Project|null
{
    return Request::attribute('project');
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
 * Escape a value for HTML text or quoted-attribute context.
 *
 * Use this for any user-controlled string interpolated into a view.
 * Do not use it on output that is already HTML (e.g. format_text()).
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
}

/**
 * JSON-encode a value so it is safe inside an HTML attribute.
 *
 * Use for Alpine x-data payloads: x-data="fn(<?= js_attr($data) ?>)"
 * json_encode alone is not enough — JSON strings contain raw quotes.
 */
function js_attr(mixed $value): string
{
    return e(json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
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
function is_project($find, $field = 'slug')
{
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
    return bin2hex(random_bytes(32));
}

function sessionCookieParams(int $expires): array
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

    return [
        'expires' => $expires,
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ];
}

function setSessionCookie(string $loginHash): void
{
    setcookie('_traq', $loginHash, sessionCookieParams(time() + (60 * 60 * 24 * 30)));
}

function clearSessionCookie(): void
{
    setcookie('_traq', '', sessionCookieParams(time() - 3600));
}

/**
 * Calculates the percentage of two numbers.
 *
 * @param int $min Lowest number
 * @param int $max Highest number
 *
 * @return float|int Returns 0 if max is 0, otherwise returns the percentage as a float
 */
function get_percent(int $min, int $max): float|int
{
    // Prevent division by zero
    if ($max === 0) {
        return 0;
    }

    return ($min / $max) * 100;
}

function dd(...$params)
{
    echo '<pre>';
    foreach ($params as $param) {
        var_dump($param);
        echo '<hr>';
    }
    exit;
}

function view(string $file, array $vars = []): string
{
    return View::render($file, $vars);
}

function extendView(string $file): void
{
    View::extend($file);
}

function startSection(string $name, ?string $content = null): void
{
    View::startSection($name, $content);
}

function endSection(): void
{
    View::endSection();
}

function hasSection(string $name): bool
{
    return View::hasSection($name);
}

function getSection(string $name, string $fallback = ''): string
{
    return View::getSection($name, $fallback);
}
