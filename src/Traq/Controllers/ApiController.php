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

use Avalon\Http\Router;
use Avalon\Output\Body;
use Traq\Models\Type;
use Traq\Models\Status;
use Traq\Models\Priority;

/**
 * API controller.
 *
 * @author Jack P.
 * @since 3.1
 * @package Traq
 * @subpackage Controllers
 */
class ApiController extends AppController
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
    public function types()
    {
        return $this->json(Type::fetchAll());
    }

    public function type(int $type_id)
    {
        return $this->json(Type::find($type_id)->toArray());
    }

    /**
     * Ticket statuses.
     *
     * @return string
     */
    public function statuses()
    {
        return $this->json(Status::fetchAll());
    }

    /**
     * Ticket priorities.
     *
     * @return string
     */
    public function priorities()
    {
        return $this->json(Priority::fetchAll());
    }

    /**
     * Project components.
     */
    public function components()
    {
        return $this->json($this->project->components->exec()->fetchAll());
    }

    /**
     * Project components.
     */
    public function customFields()
    {
        return $this->json($this->project->custom_fields->exec()->fetchAll());
    }

    /**
     * Project members.
     */
    public function projectMembers()
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
            $this->project->user_roles->exec()->fetchAll()
        );

        return $this->json($members);
    }

    /**
     * Current authenticated user.
     */
    public function auth()
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

        return $this->json($data);
    }
}
