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

namespace Traq\Controllers\Admin;

use Avalon\Http\RedirectResponse;
use Avalon\Http\Response;
use traq\models\Plugin;

/**
 * Admin Plugins controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Plugins extends \traq\controllers\admin\AppController
{
    public function index(): Response
    {
        $this->title(l('plugins'));

        $plugins = [
            'enabled' => [],
            'disabled' => []
        ];

        $installedPlugins = $this->db->select()->from('plugins')->order_by('enabled', 'ASC')->exec()->fetch_all();
        foreach ($installedPlugins as $plugin) {
            // Make sure the plugin file exists
            if (
                file_exists(DATADIR . "/plugins/{$plugin['file']}/{$plugin['file']}.php")
                || file_exists(APPPATH . "/plugins/{$plugin['file']}/{$plugin['file']}.php")
            ) {
                $plugins[$plugin['enabled'] ? 'enabled' : 'disabled'][$plugin['file']] = array_merge($plugin, ['installed' => true]);
            }
        }

        // Scan the plugin directory
        $pluginPaths = [
            DATADIR . '/plugins/',
            APPPATH . '/plugins/',
        ];

        // Scan both plugin paths
        foreach ($pluginPaths as $pluginPath) {
            if (is_dir($pluginPath)) {
                foreach (scandir($pluginPath) as $file) {
                    // Make sure its a plugin, not some weird or unwanted file or directory.
                    if ($file[0] == '.' or !is_dir("{$pluginPath}/{$file}") or !file_exists("{$pluginPath}/{$file}/{$file}.php")) {
                        continue;
                    }

                    // If the plugin isn't enabled, fetch the plugin file and then call the info() method.
                    if (!isset($plugins['enabled'][$file])) {
                        require_once $pluginPath . "{$file}/{$file}.php";
                        $className = "\\traq\plugins\\" . get_plugin_name($file);

                        if (class_exists($className)) {
                            $plugins['disabled'][$file] = array_merge(
                                $className::info(),
                                [
                                    'installed' => isset($plugins['disabled'][$file]),
                                    'enabled' => false,
                                    'file' => $file
                                ]
                            );
                        }
                    }
                    // It's enabled, only call the info() method.
                    else {
                        $className = "\\traq\plugins\\" . get_plugin_name($file);
                        if (class_exists($className)) {
                            $key = isset($plugins['enabled'][$file]) ? 'enabled' : 'disabled';
                            $plugins[$key][$file] = array_merge($className::info(), $plugins[$key][$file]);
                        }
                    }
                }
            }
        }

        return $this->renderView('admin/plugins/index.phtml', [
            'plugins' => $plugins
        ]);
    }

    /**
     * Enables the specified plugin.
     *
     * @param string $file The plugin filename (without .plugin.php)
     */
    public function enable(string $file): RedirectResponse
    {
        $file = htmlspecialchars($file);
        $this->loadPlugin($file);

        $className = "\\traq\plugins\\" . get_plugin_name($file);
        if (class_exists($className)) {
            $className::__enable();

            $plugin = Plugin::find('file', $file);
            $plugin->set('enabled', 1);
            $plugin->save();
        }

        return $this->redirectTo('/admin/plugins');
    }

    /**
     * Disables the specified plugin.
     *
     * @param string $file The plugin filename (without .plugin.php)
     */
    public function disable(string $file): RedirectResponse
    {
        $file = htmlspecialchars($file);

        $className = "\\traq\\plugins\\" . get_plugin_name($file);
        if (class_exists($className)) {
            $className::__disable();
        }

        $plugin = Plugin::find('file', $file);
        $plugin->set('enabled', 0);
        $plugin->save();

        return $this->redirectTo('/admin/plugins');
    }

    /**
     * Installs the specified plugin.
     *
     * @param string $file The plugin filename
     */
    public function install(string $file): RedirectResponse
    {
        $file = htmlspecialchars($file);
        $this->loadPlugin($file);

        $className = "\\traq\\plugins\\" . get_plugin_name($file);
        if (class_exists($className)) {
            $className::__install();

            $plugin = new Plugin(array('file' => $file));
            $plugin->set('enabled', 1);
            $plugin->save();
        }

        return $this->redirectTo('/admin/plugins');
    }

    /**
     * Uninstalls the specified plugin.
     *
     * @param string $file The plugin filename
     */
    public function uninstall(string $file): RedirectResponse
    {
        $file = htmlspecialchars($file);
        $className = "\\traq\\plugins\\" . get_plugin_name($file);

        $this->loadPlugin($file);

        if (class_exists($className)) {
            $className::__uninstall();
        }

        $plugin = Plugin::find('file', $file);
        $plugin->delete();

        return $this->redirectTo('/admin/plugins');
    }

    private function loadPlugin(string $file): bool
    {
        if (file_exists(DATADIR . "/plugins/{$file}/{$file}.php")) {
            require_once DATADIR . "/plugins/{$file}/{$file}.php";
            return true;
        } else {
            require_once APPPATH . "/plugins/{$file}/{$file}.php";
            return true;
        }

        return false;
    }
}
