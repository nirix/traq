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
    public function indexAction()
    {
        $this->title($this->translate('plugins'));

        $plugins = array();

        foreach (Plugin::select()->orderBy('is_enabled', 'ASC')->fetchAll() as $plugin) {
            $pluginDir  = VENDORDIR . "/plugins/{$plugin->directory}";
            $pluginFile = "{$pluginDir}/plugin.json";

            if (file_exists($pluginFile)) {
                $pluginInfo = $plugin->__toArray();
                $pluginInfo['installed'] = true;
                $pluginInfo['is_enabled']   = $plugin->isEnabled();
                $pluginInfo['directory'] = $plugin->directory;
                $pluginInfo['status']  = $this->status($pluginInfo);

                $plugins[$plugin->directory] = $pluginInfo;
            }
        }

        // Scan the plugin directory
        foreach (scandir(VENDORDIR . '/plugins') as $dir) {
            if ($dir[0] == '.'
            or !is_dir(VENDORDIR . "/plugins/{$dir}")
            or !file_exists(VENDORDIR . "/plugins/{$dir}/plugin.json")
            or isset($plugins[$dir])) {
                continue;
            }

            $pluginInfo = $this->getInfo($dir);

            $pluginInfo['directory'] = $dir;
            $pluginInfo['installed'] = false;
            $pluginInfo['is_enabled']   = false;
            $pluginInfo['status']  = $this->status($pluginInfo);

            $plugins[$dir] = $pluginInfo;
        }

        $this->set('plugins', $plugins);
    }

    /**
     * Loads and parses the plugins JSON file.
     *
     * @param string $directory Plugin directory
     *
     * @return array
     */
    protected function getInfo($directory)
    {
        $path = VENDORDIR . "/plugins/{$directory}/plugin.json";
        $data = file_get_contents($path);
        return json_decode($data, true);
    }

    /**
     * Returns the status of the plugin.
     *
     * @param array $info Plugin info.
     *
     * @return string
     */
    protected function status($info)
    {
        if ($info['is_enabled']) {
            return 'enabled';
        } else if ($info['installed']) {
            return 'installed';
        } else {
            return 'uninstalled';
        }
    }

    /**
     * Enables the specified plugin.
     *
     * @param string $file The plugin filename (without .plugin.php)
     */
    public function enableAction($directory)
    {
        $plugin    = Plugin::find('directory', $directory);
        $pluginDir = VENDORDIR . "/plugins/{$directory}";
        $class     = "{$plugin->namespace}\\{$plugin->class}";
        $file      = "{$pluginDir}/{$plugin->file}";

        if (file_exists($file)) {
            require $file;
        }

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
    public function disableAction($directory)
    {
        $plugin    = Plugin::find('directory', $directory);
        $pluginDir = VENDORDIR . "/plugins/{$directory}";
        $class     = "{$plugin->namespace}\\{$plugin->class}";

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
    public function installAction($directory)
    {
        $pluginDir  = VENDORDIR . "/plugins/{$directory}";
        $pluginInfo = $this->getInfo($directory);

        $pluginInfo['directory'] = $directory;
        $pluginInfo['is_enabled']   = true;
        $pluginInfo['class']     = preg_replace("/^([\w]+).php$/", "$1", $pluginInfo['file']);

        $file = "{$pluginDir}/{$pluginInfo['file']}";
        $class = "{$pluginInfo['namespace']}\\{$pluginInfo['class']}";

        if (file_exists($file)) {
            require $file;
        }

        if (class_exists($class)) {
            $class::__install();
            (new Plugin($pluginInfo))->save();
        }

        return $this->redirectTo('/admin/plugins');
    }

    /**
     * Uninstalls the specified plugin.
     *
     * @param string $directory The plugin directory name
     */
    public function uninstallAction($directory)
    {
        $plugin = Plugin::find('directory', $directory);

        $class = "{$plugin->namespace}\\{$plugin->class}";

        // Check if the class exists
        if (class_exists($class)) {
            $class::__uninstall();
            $plugin->delete();
        }

        $this->redirectTo('/admin/plugins');
    }
}
