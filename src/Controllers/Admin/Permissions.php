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

namespace Traq\Controllers\Admin;

use Avalon\Http\Request;
use Traq\Permissions as PermissionsAPI;

/**
 * Permissions controller.
 *
 * @package Traq\Controllers\Admin
 * @author Jack P.
 * @since 3.0.0
 */
class Permissions extends AppController
{
    public function groupsAction()
    {
        $defaultPermissionsQuery = queryBuilder()->select('p.*', 'p.id AS permission_id')->from(PREFIX . 'permissions', 'p')
            ->where('type = ?')
            ->andWhere('type_id = ?')
            ->andWhere('project_id = ?')
            ->setParameter(0, 'usergroup')
            ->setParameter(1, 0)
            ->setParameter(2, 0)
            ->execute();

        $defaults = $defaultPermissionsQuery->fetch();
        $defaults = json_decode($defaults['permissions'], true);
        $permissions = [];

        $groupsQuery = queryBuilder()->select('g.*', 'p.permissions', 'p.type_id', 'p.id AS permission_id')
            ->from(PREFIX . 'usergroups', 'g')
            ->leftJoin('g', PREFIX . 'permissions', 'p', 'p.type = "usergroup" AND p.type_id = g.id')
            ->execute();

        foreach ($groupsQuery->fetchAll() as $group) {
            $group['permissions'] = json_decode($group['permissions'], true);
            $permissions[$group['id']] = $group;
        }

        return $this->render('admin/permissions/list.phtml', [
            'type'        => 'groups',
            'defaults'    => $defaults,
            'permissions' => $permissions
        ]);
    }

    public function saveGroupsAction()
    {
        $permissions = [];

        foreach (Request::$post['permissions'] as $group => $perms) {
            if ($group == 'defaults') {
                foreach (PermissionsAPI::getPermissions() as $name => $default) {
                    if (isset($perms[$name])) {
                        $permissions['default'][$name] = true;
                    } else {
                        $permissions['default'][$name] = false;
                    }
                }
            } else {
                foreach ($perms as $name => $value) {
                    if ($value == '1' || $value == '0') {
                        $permissions[$group][$name] = (boolean) $value;
                    }
                }
            }
        }

        dd($permissions);
    }

    public function rolesAction()
    {
        return $this->render('admin/permissions/list.phtml', [
            'type'        => 'roles',
            'defaults'    => [],
            'permissions' => []
        ]);
    }
}
