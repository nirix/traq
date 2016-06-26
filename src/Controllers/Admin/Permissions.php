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
    public function usergroupsAction()
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
        $defaults = json_decode($defaults['permissions'], true) + PermissionsAPI::getPermissions();
        $permissions = [];

        $groupsQuery = queryBuilder()->select('g.*', 'p.permissions', 'p.type_id', 'p.id AS permission_id')
            ->from(PREFIX . 'usergroups', 'g')
            ->leftJoin('g', PREFIX . 'permissions', 'p', 'p.type = :p_type AND p.type_id = g.id')
            ->orderBy('g.id', 'ASC')
            ->setParameter('p_type', 'usergroup')
            ->execute();

        foreach ($groupsQuery->fetchAll() as $group) {
            $group['permissions'] = json_decode($group['permissions'], true);
            $permissions[$group['id']] = $group;
        }

        return $this->render('admin/permissions/list.phtml', [
            'type'        => 'usergroups',
            'defaults'    => $defaults,
            'permissions' => $permissions
        ]);
    }

    public function saveUsergroupsAction()
    {
        $this->savePermissions('usergroup');
        return $this->redirectTo('admin_permissions');
    }

    public function rolesAction()
    {
        $defaultPermissionsQuery = queryBuilder()->select('p.*', 'p.id AS permission_id')->from(PREFIX . 'permissions', 'p')
            ->where('type = ?')
            ->andWhere('type_id = ?')
            ->andWhere('project_id = ?')
            ->setParameter(0, 'role')
            ->setParameter(1, 0)
            ->setParameter(2, 0)
            ->execute();

        $defaults = $defaultPermissionsQuery->fetch();
        $defaults = json_decode($defaults['permissions'], true) + PermissionsAPI::getPermissions();
        $permissions = [];

        $groupsQuery = queryBuilder()->select('r.*', 'p.permissions', 'p.type_id', 'p.id AS permission_id')
            ->from(PREFIX . 'project_roles', 'r')
            ->leftJoin('r', PREFIX . 'permissions', 'p', 'p.type = :p_type AND p.type_id = r.id')
            ->orderBy('r.id', 'ASC')
            ->setParameter('p_type', 'role')
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

    public function saveRolesAction()
    {
        $this->savePermissions('role');
        return $this->redirectTo('admin_permissions_roles');
    }

    protected function savePermissions($type)
    {
        foreach (Request::$post['permissions'] as $group => $perms) {
            $permissions = [];

            if ($group == 'defaults') {
                foreach (PermissionsAPI::getPermissions() as $name => $default) {
                    if (isset($perms[$name])) {
                        $permissions[$name] = true;
                    } else {
                        $permissions[$name] = false;
                    }
                }

                $this->db->update(
                    PREFIX . 'permissions',
                    ['permissions' => json_encode($permissions)],
                    [
                        'type'       => $type,
                        'type_id'    => 0,
                        'project_id' => 0
                    ]
                );
            } else {
                // Ignore 'null' values
                foreach ($perms as $name => $value) {
                    if ($value == '1' || $value == '0') {
                        $permissions[$name] = (boolean) $value;
                    }
                }

                // If there are no permissions, delete the row
                if (!count($permissions)) {
                    $this->db->delete(
                        PREFIX . 'permissions',
                        [
                            'type'       => $type,
                            'type_id'    => $group,
                            'project_id' => 0
                        ]
                    );
                } else {
                    // Check if the row exists already
                    $query = queryBuilder()->select('id')->from(PREFIX . 'permissions')
                        ->where('type = ?')
                        ->andWhere('type_id = ?')
                        ->andWhere('project_id = ?')
                        ->setParameter(0, $type)
                        ->setParameter(1, $group)
                        ->setParameter(2, 0)
                        ->execute();

                    // Update the row
                    if ($query->rowCount()) {
                        $this->db->update(
                            PREFIX . 'permissions',
                            ['permissions' => json_encode($permissions)],
                            [
                                'type'       => $type,
                                'type_id'    => $group,
                                'project_id' => 0
                            ]
                        );
                    } else {
                        // Insert a new row
                        $this->db->insert(
                            PREFIX . 'permissions',
                            [
                                'type'        => $type,
                                'type_id'     => $group,
                                'project_id'  => 0,
                                'permissions' => json_encode($permissions)
                            ]
                        );
                    }
                }
            }
        }
    }
}
