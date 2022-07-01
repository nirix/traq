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

namespace traq\controllers;

use avalon\output\View;
use avalon\http\Router;
use avalon\output\Body;

use avalon\core\Load;
use traq\models\Status;
use traq\models\Priority;

/**
 * API controller.
 *
 * @author Jack P.
 * @since 3.1
 * @package Traq
 * @subpackage Controllers
 */
class API extends AppController
{
    public function __construct()
    {
        Router::$extension = 'json';
        parent::__construct();

        $this->render['layout'] = false;
        $this->render['view'] = false;

        header('Content-Type: application/json; charset=UTF-8');
    }

    /**
     * Ticket statuses.
     *
     * @return string
     */
    public function action_statuses()
    {
        Body::append(to_json(Status::fetch_all()));
    }

    /**
     * Ticket priorities.
     *
     * @return string
     */
    public function action_priorities()
    {
        Body::append(to_json(Priority::fetch_all()));
    }

    /**
     * Project components.
     */
    public function action_components()
    {
        Body::append(to_json($this->project->components->exec()->fetch_all()));
    }
}
