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

use Radium\Action\Controller;
use Radium\Database;
use Radium\Http\Response;
use Radium\Http\Request;
use Radium\Http\Router;
use Radium\Action\View;
use Radium\Language;

use Traq\Models\Setting;
use Traq\Models\User;
use Traq\Models\Project;
use Traq\API;

/**
 * App controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class AppController extends Controller
{
    public $project;
    public $projects;
    public $user;
    public $isApi = false;
    public $title = array();
    public $feeds = array();

    public function __construct()
    {
        // Call the controller class constructor
        parent::__construct();

        // Set the title
        $this->title(Setting::find('title')->value);

        // Get the user info
        $this->getUser();

        // Set the theme, title and pass the app object to the view.
        $this->set('traq', $this);

        // Check if we're on a project page and get the project info
        $route = Router::currentRoute();
        if (isset($route['params']['project_slug'])
        and $this->project = is_project($route['params']['project_slug'])) {
            if ($this->user->permission($this->project->id, 'view')) {
                // Add project name to page title
                $this->title($this->project->name);

                // Send the project object to the view
                $this->set('project', $this->project);
            } else {
                $this->showNoPermission();
            }
        }

        // Fetch all projects and make sure the user has permission
        // to access the project then pass them to the view.
        $this->projects = array();
        foreach (Project::select()->orderBy('display_order', 'ASC')->fetchAll() as $project) {
            // Check if the user has access to view the project...
            if ($this->user->permission($project->id, 'view')) {
                $this->projects[] = $project;
            }
        }
        $this->set('projects', $this->projects);

        $this->set('app', $this);
    }

    /**
     * Adds to or returns the page title array.
     *
     * @param mixed $add
     *
     * @return mixed
     */
    public function title($add = null)
    {
        // Check if we're adding or returning
        if ($add === null) {
            // We're returning
            return $this->title;
        }

        // Add the title
        $this->title[] = $add;
    }

    /**
     * Sets the response to a 404 Not Found
     */
    public function show404()
    {
        $default = parent::show404();

        return $this->respondTo(function($format) use ($default) {
            if ($format === 'html') {
                return $default;
            } elseif ($format === 'json') {
                return API::response(404, [
                    'message' => $this->translate('errors.404.message', [Request::uri()])
                ]);
            }
        });
    }

    /**
     * Used to display the no permission page.
     */
    public function showNoPermission()
    {
        $this->executeAction = false;
        return new Response(function($resp){
            $resp->status = 401;
            $resp->body   = $this->renderView('errors/no_permission', [
                '_layout' => $this->layout
            ]);
        });
    }

    /**
     * Used to display the login page.
     */
    public function showLogin()
    {
        $this->render['action'] = false;
        $this->render['view'] = 'users/login' . ($this->is_api ? '.api' :'');
    }

    /**
     * Does the checking for the session cookie and fetches the users info.
     *
     * @author Jack P.
     * @since 3.0
     * @access private
     */
    protected function getUser()
    {
        $route = Router::currentRoute();

        // Regular request
        if (isset($_COOKIE['_traq']) and $user = User::find('login_hash', $_COOKIE['_traq'])) {
            $this->user = $user;
        }
        // Check for
        else if ($apiKey = API::getKey()) {
            $this->user = User::find('api_key', $apiKey);
            $this->isApi = true;
            // $this->setView($this->view . ".json");
        }

        if ($this->user) {
            define("LOGGEDIN", true);
            Language::setCurrent($this->user->locale);
        } else {
            define("LOGGEDIN", false);
            $this->user = User::anonymousUser();
        }

        $this->set('currentUser', $this->user);
    }
}
