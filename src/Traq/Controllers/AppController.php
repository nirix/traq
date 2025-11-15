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

namespace Traq\Controllers;

use Avalon\Core\Controller;
use Avalon\Core\Load;
use Avalon\Database;
use Avalon\Database\PDO;
use Avalon\Http\JsonResponse;
use Avalon\Http\RedirectResponse;
use Avalon\Http\Request;
use Avalon\Http\Response;
use Avalon\Http\Router;
use Avalon\Output\Body;
use Avalon\Output\View;
use Traq\Middleware\ProjectMiddleware;
use Traq\Middleware\UserMiddleware;
use Traq\Models\User;
use Traq\Models\Project;

/**
 * App controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
#[UserMiddleware]
#[ProjectMiddleware]
class AppController extends Controller
{
    public array $title = [];
    public array $feeds = [];
    public ?User $user = null;
    public ?Project $project = null;
    public array $projects = [];
    protected bool $isAtom = false;
    protected PDO $db;

    // true for JSON and API requests
    protected bool $isJson = false;

    // true for API requests
    protected bool $isApi = false;

    /**
     * @deprecated 3.9.0
     * @see AppController::$isApi
     */
    public bool $is_api = false;

    public function __construct()
    {
        // Set DB connection
        $this->db = Database::connection();
        $dbVersion = settings('db_version');
        if ($dbVersion < TRAQ_DB_VER) {
            // Database version is out of date, redirect to upgrader
            header("Location: " . Request::base('install/upgrade.php'));
            exit;
        }

        // Call the controller class constructor
        parent::__construct();

        // Set is_api and JSON view extension
        $this->user = Request::get('current_user');
        if (Request::get('is_api')) {
            $this->isApi = true;
            $this->isJson = true;
            Router::$extension = '.json';
            $this->render['view'] = $this->render['view'] . ".json";
        }

        // Set the project
        $this->project = Request::getAttribute('project');
        if ($this->project) {
            // Add project name to page title
            $this->title($this->project->name);

            // Send the project object to the view
            View::set('project', $this->project);
        }

        // Fix plugin view location
        if (strpos(Router::$controller, "\\traq\\plugins") !== false) {
            $this->render['view'] = str_replace("controllers/", '', $this->render['view']);
        }

        // Set the title
        $this->title(settings('title'));

        $this->loadHelpers();

        // Set the theme, title and pass the app object to the view.
        View::set('traq', $this);

        // Fetch all projects and make sure the user has permission
        // to access the project then pass them to the view.
        foreach (Project::select()->order_by('displayorder', 'ASC')->exec()->fetch_all() as $project) {
            // Check if the user has access to view the project...
            if ($this->user->permission($project->id, 'view')) {
                $this->projects[] = $project;
            }
        }
        View::set('projects', $this->projects);

        View::set('app', $this);

        if (Router::$extension == '.json' || $_SERVER['HTTP_ACCEPT'] == 'application/json') {
            $this->isJson = true;
        }

        if (Router::$extension == '.atom' || Router::$extension == '.rss') {
            $this->isAtom = true;
        }
    }

    /**
     * Adds to or returns the page title array.
     *
     * @param mixed $add
     *
     * @return mixed
     */
    public function title(?string $add = null)
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
     * Used to display the no permission page.
     */
    public function show_no_permission(): void
    {
        header("HTTP/1.0 401 Unauthorized");
        $this->render['view'] = 'error/no_permission';
        $this->render['action'] = false;
        $this->render['layout'] = 'default';
    }

    public function renderNoPermission(): Response
    {
        return $this->render('error/no_permission', statusCode: 401);
    }

    /**
     * Used to display the login page.
     */
    public function show_login(): void
    {
        $this->render['action'] = false;
        $this->render['view'] = 'users/login' . ($this->is_api ? '.api' : '');
        $this->render['layout'] = 'default';
    }

    /**
     * Display a bad API request.
     *
     * @param string $error Error message
     *
     * @deprecated 3.9.0
     */
    protected function bad_api_request(string $message): void
    {
        $this->render = array_merge(
            $this->render,
            array(
                'action' => false,
                'view'   => "api/bad_request.json",
                'layout' => "plain"
            )
        );

        View::set(compact('message'));
    }

    /**
     * API Response.
     *
     * @param array $data
     * @deprecated 3.9.0
     */
    protected function apiResponse(array $data): void
    {
        Router::$extension = 'json';

        $this->render['layout'] = false;
        $this->render['view'] = false;

        header('Content-Type: application/json; charset=UTF-8');
        Body::append(to_json($data));
    }

    /**
     * @deprecated 3.9.0
     */
    public function __shutdown()
    {
        // Plain layout for JSON and API requests
        if (Router::$extension == '.json' || $this->isApi) {
            $this->render['layout'] = 'plain';
        }

        // Was the page requested via ajax?
        if ($this->render['view'] and Request::isAjax() and Router::$extension == null) {
            // Is this page being used as an overlay?
            if (isset(Request::$request['overlay'])) {
                $extension = '.overlay';
            }
            // a popover?
            elseif (isset(Request::$request['popover'])) {
                $extension = '.popover';
            }
            // Neither, just regular javascript
            else {
                $extension = '.js';
            }

            // Set the layout and view extension
            $this->render['layout'] = 'plain';
            $this->render['view'] = $this->render['view'] . $extension;
        }

        if (Router::$extension == '.json') {
            header('Content-type: application/json');
            if ($this->render['view'] and strpos($this->render['view'], '.json') === false) {
                $this->render['view'] = $this->render['view'] . '.json';
            }
        }

        // Call the controllers shutdown method.
        parent::__shutdown();
    }

    protected function show404()
    {
        header('HTTP/1.0 404 Not Found');
        View::set('request', Request::requestUri());
        $this->render['view'] = 'error/404';
        $this->render['action'] = false;

        return $this->render('error/404');
    }

    /**
     * Set view variable.
     */
    protected function set(string|array $name, mixed $value = null): void
    {
        View::set($name, $value);
    }

    protected function render(string $name, array $vars = [], int $statusCode = 200): Response
    {
        return new Response($this->renderView($name, $vars), $statusCode);
    }

    protected function renderView(string $name, array $vars = []): string
    {
        $content = View::render(str_replace(['.phtml', '.php'], '', $name), $vars);

        if ($this->render['layout']) {
            $content = View::render("layouts/{$this->render['layout']}", ['content' => $content]);
        }

        return $content;
    }

    protected function json(array $data, int $statusCode = 200): JsonResponse
    {
        $badKeys = ['password', 'login_hash', 'api_key', 'private_key'];

        if (!is_array($data)) {
            $data = to_array($data);
        }

        foreach ($data as $k => $v) {
            $data[$k] = to_array($v);
        }

        // Recursively remove bad keys from data array at all levels
        $removeBadKeys = function ($array) use (&$removeBadKeys, $badKeys) {
            foreach ($array as $key => $value) {
                if (in_array($key, $badKeys)) {
                    unset($array[$key]);
                } elseif (is_array($value)) {
                    $array[$key] = $removeBadKeys($value);
                }
            }

            return $array;
        };

        $data = $removeBadKeys($data);

        return new JsonResponse($data, $statusCode);
    }

    protected function redirectTo(string $url): Response
    {
        return new RedirectResponse(Request::base($url));
    }

    protected function db(): PDO
    {
        return $this->db;
    }

    private function loadHelpers(): void
    {
        // Load helpers
        Load::helper(
            'errors',
            'form',
            'js',
            'formats',
            'time_ago',
            'string',
            'subscriptions',
            'timeline',
            'formatting',
            'tickets',
        );
    }
}
