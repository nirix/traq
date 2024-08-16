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

use Avalon\Http\Router;
use Avalon\Output\Body;

use traq\models\Type;
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
     * Ticket types.
     *
     * @return string
     */
    public function action_types()
    {
        Body::append(to_json(Type::fetch_all()));
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

    /**
     * Project components.
     */
    public function action_customFields()
    {
        Body::append(to_json($this->project->custom_fields->exec()->fetch_all()));
    }

    /**
     * Project members.
     */
    public function action_projectMembers()
    {
        $members = array_map(
            function ($userRole) {
                return [
                    'id' => $userRole->user->id,
                    'username' => $userRole->user->username,
                    'name' => $userRole->user->name,
                    'role' => $userRole->role->name,
                ];
            },
            $this->project->user_roles->exec()->fetch_all()
        );

        Body::append(to_json($members));
    }

    /**
     * Current authenticated user.
     */
    public function action_auth()
    {
        $data = false;

        if ($this->user) {
            $data = $this->user->__toArray([
                'id',
                'username',
                'name',
                'group_id',
                'locale',
            ]);

            $data['id'] = (int) $data['id'];
            $data['group_id'] = (int) $data['group_id'];
            $data['group'] = $this->user->group->__toArray();
            $data['group']['is_admin'] = $data['group']['is_admin'] === "1";

            $data['permissions'] = [];
            if ($this->project) {
                foreach ($this->user->getPermissions($this->project->id) as $permission) {
                    if ((bool) $permission->value) {
                        $data['permissions'][$permission->action] = (bool) $permission->value;
                    }
                }
            }
        }

        Body::append(to_json($data));
    }
}
