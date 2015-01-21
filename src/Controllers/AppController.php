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

namespace Traq\Controllers;

use Radium\Http\Controller;
use Radium\Http\Request;
use Radium\Http\Response;
use Avalon\Database\ConnectionManager;
use Traq\Models\Project;
use Traq\Models\User;
use Traq\Models\Setting;

/**
 * App controller
 *
 * @author Jack P.
 * @since 3.0
 */
class AppController extends Controller
{
    /**
     * Current user.
     *
     * @var User
     */
    protected $currentUser;

    /**
     * Current project.
     *
     * @var Project
     */
    protected $project;

    /**
     * Projects the user has access to.
     *
     * @var Project[]
     */
    protected $projects = [];

    /**
     * Page title.
     *
     * @var string[]
     */
    public $title = [];

    /**
     * Atom feeds.
     *
     * @var string[]
     */
    public $feeds = [];

    /**
     * Overlay view request.
     *
     * @var boolean
     */
    protected $isOverlay = false;

    public function __construct()
    {
        parent::__construct();

        // Get database connection
        $this->db = ConnectionManager::getConnection();

        // Append installation title to page title
        $this->title($this->setting('title'));

        // Get current user
        $this->getUser();

        // Get current project
        $this->getProject();
        $this->before('*', function(){
            // Make sure the user has permission to view the project
            if (LOGGEDIN && $this->project && !$this->currentUser->permission($this->project->id, 'view')) {
                return $this->showNoPermission();
            }
        });

        // No layouts for overlays
        if (Request::header('X-Overlay')) {
            $this->layout    = false;
            $this->isOverlay = true;
        }

        // Set environment
        $this->set('environment', $_ENV['environment']);

        // Fetch all projects and make sure the user has permission to view them
        foreach (Project::select()->orderBy('display_order', 'ASC')->fetchAll() as $project) {
            if ($this->currentUser->permission($project->id, 'view')) {
                $this->projects[] = $project;
            }
        }

        $this->set('projects', $this->projects);
        $this->set('traq', $this);
    }

    /**
     * Append the string to the page title.
     *
     * @param string $title
     */
    protected function title($title)
    {
        $this->title[] = $title;
    }

    /**
     * Returns the setting value.
     *
     * @param string $setting
     *
     * @return mixed
     */
    protected function setting($setting)
    {
        return Setting::get($setting)->value;
    }

    /**
     * Get the current user from cookie or request header.
     */
    protected function getUser()
    {
        // Check cookie
        if (isset($_COOKIE['traq']) && $user = User::find('login_hash', $_COOKIE['traq'])) {
            $this->currentUser = $user;
        }

        // Check headers
        if ($apiKey = Request::header('X-Access-Token') && $user = User::find('api_key', $apiKey)) {
            $this->currentUser = $user;
        }

        if ($this->currentUser) {
            define("LOGGEDIN", true);
            Language::setCurrent($this->currentUser->locale);
        } else {
            define("LOGGEDIN", false);
            $this->currentUser = User::anonymousUser();
        }

        $this->set('currentUser', $this->currentUser);
    }

    /**
     * Get the current project.
     *
     * @return mixed
     */
    public function getProject()
    {
        if (
            isset($this->route->params['project_slug'])
            && $this->project = Project::find('slug', $this->route->params['project_slug'])
        ) {
            $GLOBALS['project'] = $this->project;

            // Add project name to page title
            $this->title($this->project->name);

            // Set project view variable
            $this->set('project', $this->project);

            // Active milestones
            $this->set(
                'activeMilestones',
                $this->project->milestones()->where('status = ?', 1)
                    ->orderBy('display_order', 'ASC')
            );
        }
    }

    /**
     * Returns 404 response.
     *
     * @return Response
     */
    public function show404()
    {
        $default = parent::show404();

        return $this->respondTo(function($format) use ($default) {
            if ($format === 'json') {
                $response = $this->jsonResponse([
                    'message' => $this->translate('errors.404.message', [Request::pathInfo()])
                ]);

                // Set 404 status
                $response->status = 404;

                return $response;
            } else {
                return $default;
            }
        });
    }

    /**
     * Returns 403 response.
     *
     * @return Response
     */
    public function show403()
    {
        $this->executeAction = false;
        return new Response(function($resp){
            $resp->status = 401;
            $resp->body   = $this->renderView('errors/no_permission.phtml', [
                '_layout' => $this->layout
            ]);
        });
    }

    /**
     * Returns a new JSON response.
     *
     * @param array $data
     *
     * @return Response
     */
    protected function jsonResponse(array $data)
    {
        return new Response(function($resp) use ($data) {
            $resp->contentType = 'application/json';
            $resp->body = json_encode($data);
        });
    }
}
