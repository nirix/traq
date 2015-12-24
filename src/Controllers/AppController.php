<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack P.
 * Copyright (C) 2012-2015 Traq.io
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

use Avalon\Http\Controller;
use Avalon\Http\Request;
use Avalon\Templating\View;
use Traq\Models\User;
use Traq\Models\Project;

/**
 * App controller
 *
 * @author Jack P.
 * @since 3.0.0
 */
class AppController extends Controller
{
    /**
     * @var User
     */
    protected $currentUser;

    /**
     * @var User
     */
    protected $anonymousUser;

    /**
     * @var array
     */
    protected $currentProject;

    /**
     * @var array
     */
    protected $title = [];

    protected $isOverlay = false;

    public function __construct()
    {
        // parent::__construct();

        session_start();

        $this->db = $GLOBALS['db'];
        $this->title(setting('title'));
        $this->set('traq', $this);

        if (Request::$headers->has('X-Overlay')) {
            $this->isOverlay = true;
            $this->layout = false;
        }

        $this->before('*', function () {
            // Are we on a project page?
            if ($projectSlug = Request::$properties->get('pslug')) {
                $this->currentProject = Project::where('slug = ?')
                    ->setParameter(0, $projectSlug)
                    ->fetch();
            }

            // Is the user logged in?
            if ((isset($_COOKIE['traq']) && $sessionHash = $_COOKIE['traq'])) {
                $user = User::select('u.*', 'g.is_admin')
                    ->leftJoin('u', PREFIX . 'usergroups', 'g', 'g.id = u.group_id');

                // Project role
                if ($this->currentProject) {
                    $user->addSelect('r.project_role_id')
                        ->leftJoin('u', PREFIX . 'user_roles', 'r', 'r.user_id = u.id');
                }

                // By session
                if ($sessionHash) {
                    $user->where('u.login_hash = :login_hash')
                        ->setParameter('login_hash', $sessionHash);
                }

                // By API key
                // if ($apiKey) {

                // }

                $this->currentUser = $user->fetch();
            }

            $GLOBALS['currentUser'] = $this->currentUser;
            $this->set('currentUser', $this->currentUser);

            // Check if project exists
            if (($projectSlug && !$this->currentProject)
            || ($projectSlug && !$this->hasPermission($this->currentProject['id'], 'view_project'))) {
                return $this->show404();
            } else {
                $this->title($this->currentProject['name']);
            }

            $GLOBALS['currentProject'] = $this->currentProject;
            $this->set('currentProject', $this->currentProject);
        });
    }

    protected function hasPermission($projectId, $action)
    {
        if (!$user = current_user()) {
            $user = anonymous_user();
        }

        return $user->hasPermission($projectId, $action);
    }

    /**
     * Set or get the page title.
     *
     * @param string|null $title
     *
     * @return null|string
     */
    public function title($title = null)
    {
        if ($title) {
            $this->title[] = $title;
        } else {
            return $this->title;
        }
    }

    /**
     * Show the login form.
     */
    protected function showLogin($goto = null)
    {
        return $this->render('sessions/new.phtml', [
            '_layout' => 'default.phtml',
            'goto'    => $goto
        ]);
    }
}
