<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

namespace Traq\Controllers\Admin;

use Radium\Http\Request;

use Traq\Models\Plugin;

/**
 * Admin Plugins controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq\Controllers\Admin
 */
class Plugins extends AppController
{
    protected $loader;

    public function indexAction()
    {
        $this->title($this->translate('plugins'));

        $plugins = $this->getPlugins();

        foreach ($plugins as $id => $info) {
            $info = $info + array(
                'installed'  => false,
                'is_enabled' => false
            );

            if ($plugin = Plugin::find('directory', $info['directory'])) {
                $info['installed'] = true;

                if ($plugin->is_enabled) {
                    $info['is_enabled'] = true;
                }
            }

            $plugins[$id] = $info;
        }

        $this->set('plugins', $plugins);
    }

    /**
     * Enables the specified plugin.
     *
     * @param string $file The plugin filename (without .plugin.php)
     */
    public function enableAction()
    {
        $plugin = Plugin::find('directory', Request::$get['plugin']);
        $class  = "{$plugin->namespace}{$plugin->class}";

        $this->registerNamespace($plugin->namespace, VENDORDIR . "/{$plugin->directory}");

        // Check if the class exists
        if (class_exists($class)) {
            $class::__enable();
            $plugin->is_enabled = true;
            $plugin->save();
        }

        $this->redirectTo('/admin/plugins');
    }

    /**
     * Disables the specified plugin.
     *
     * @param string $file The plugin filename (without .plugin.php)
     */
    public function disableAction()
    {
        $plugin = Plugin::find('directory', Request::$get['plugin']);
        $class  = "{$plugin->namespace}{$plugin->class}";

        // Check if the class exists
        if (class_exists($class)) {
            $class::__disable();
            $plugin->is_enabled = false;
            $plugin->save();
        }

        $this->redirectTo('/admin/plugins');
    }

    /**
     * Installs the specified plugin.
     *
     * @param string $directory The plugin directory name
     */
    public function installAction()
    {
        $plugins = $this->getPlugins();
        $info    = $plugins[Request::$get['plugin']];
        $class   = "{$info['namespace']}{$info['class']}";

        // Register for autoload
        $this->registerNamespace($info['namespace'], VENDORDIR . "/{$info['directory']}");

        if (class_exists($class)) {
            $class::__install();
            $class::__enable();

            $info['is_enabled'] = true;
            (new Plugin($info))->save();
        }

        return $this->redirectTo('/admin/plugins');
    }

    /**
     * Uninstalls the specified plugin.
     *
     * @param string $directory The plugin directory name
     */
    public function uninstallAction()
    {
        $plugin  = Plugin::find('directory', Request::$get['plugin']);
        $plugins = $this->getPlugins();
        $info    = $plugins[Request::$get['plugin']];
        $class   = "{$plugin->namespace}{$plugin->class}";

        // Register for autoload
        if (!$plugin->is_enabled) {
            $this->registerNamespace($info['namespace'], VENDORDIR . "/{$info['directory']}");
        }

        // Check if the class exists
        if (class_exists($class)) {
            if ($plugin->is_enabled) {
                $class::__disable();
            }

            $class::__uninstall();
        }

        $plugin->delete();
        $this->redirectTo('/admin/plugins');
    }

    /**
     * Registers the namespace with the autoloader.
     *
     * @param string $namespace
     * @param string $directroy
     */
    protected function registerNamespace($namespace, $directroy)
    {
        if (!$this->loader) {
            $loader = require VENDORDIR . '/autoload.php';
        }

        $loader->addPsr4($namespace, "{$directroy}/src");
    }

    /**
     * Reads all plugin info files two levels deep in the `vendor` directory.
     *
     * @return array
     */
    protected function getPlugins()
    {
        $files = array();

        foreach (glob(VENDORDIR . '/*/traq_plugin.json') as $file) {
            $files[] = $file;
        }

        foreach (glob(VENDORDIR . '/*/*/traq_plugin.json') as $file) {
            $files[] = $file;
        }

        $plugins = array();

        foreach ($files as $file) {
            $info = json_decode(file_get_contents($file), true);

            if (!$info) {
                continue;
            }

            // Get directory without the vendor directory path
            $info['directory'] = trim(str_replace(VENDORDIR, '', dirname($file)), '/');

            $plugins[$info['directory']] = $info;
        }

        return $plugins;
    }
}
