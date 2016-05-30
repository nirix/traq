<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
 * https://github.com/nirix
 * https://traq.io
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

namespace Traq\Controllers;

use Avalon\Http\Request;
use Avalon\Http\Controller;
use Avalon\Database\ConnectionManager;
use Traq\Models\User;
use Traq\Models\Permission;
use Traq\Models\Project;

/**
 * Base Traq controller.
 *
 * @package Traq\Controllers
 * @author Jack P.
 * @since 3.0.0
 */
class AppController extends Controller
{
    // Disable layouts since we're using the `PhpExtended` engine.
    protected $layout = false;

    /**
     * @var User
     */
    protected $currentUser;

    /**
     * @var Project
     */
    protected $currentProject;

    /**
     * @var array
     */
    protected $pageTitle = [];

    /**
     * @var array
     */
    protected $breadcrumbs = [];

    /**
     * Always call this when defining `__construct()` in sub-classes.
     */
    public function __construct()
    {
        $this->db = ConnectionManager::getConnection();

        // Get current user.
        if ($sessionHash = Request::$cookies->get('traq')) {
            $this->currentUser = User::find('session_hash', $sessionHash) ?: null;
            $GLOBALS['current_user'] = $this->currentUser;
        }

        // Get current project.
        if (Request::$properties->has('pslug')) {
            $this->currentProject = Project::find('slug', Request::$properties->get('pslug')) ?: null;
            $GLOBALS['current_project'] = $this->currentProject;
        }

        $GLOBALS['permissions'] = Permission::getPermissions($this->currentUser, $this->currentProject);

        // Add Traq as first breadcrumb.
        $this->addCrumb(setting('title'), $this->generateUrl('root'));
    }

    /**
     * Add breadcrumb.
     *
     * @param string $text
     * @param string $url
     */
    protected function addCrumb($text, $url)
    {
        $this->breadcrumbs[] = [
            'text' => $text,
            'url' => $url
        ];

        $this->pageTitle[] = $text;

        $this->set([
            'breadcrumbs' => $this->breadcrumbs,
            'pageTitle' => $this->pageTitle
        ]);
    }
}
