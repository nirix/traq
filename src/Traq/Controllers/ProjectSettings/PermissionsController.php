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

namespace Traq\Controllers\ProjectSettings;

use Avalon\Http\Request;
use Avalon\Http\Response;
use Traq\Models\Permission;
use Traq\Models\Group;
use Traq\Models\ProjectRole;

/**
 * Project permissions controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class PermissionsController extends AppController
{
    public function __construct()
    {
        parent::__construct();

        // Set Form::select() options
        $this->set('options', array(
            'defaults' => array(
                array('label' => l('allow'), 'value' => 1),
                array('label' => l('deny'), 'value' => 0),
            ),
            'all' => array(
                array('label' => l('defaults'), 'value' => -1),
                array('label' => l('allow'), 'value' => 1),
                array('label' => l('deny'), 'value' => 0),
            )
        ));
    }

    /**
     * Handles the permissions listing and saving...
     *
     * Nice sexy DRY code right here, eh?
     */
    public function index(string $type): Response
    {
        // If the type of permissions is 'groups', set it to 'usergroups'.
        $type = $type == 'groups' ? 'usergroup' : 'role';

        // Has the form been submitted?
        if (Request::method() == 'POST') {
            $this->savePermissions($type, Request::$post['perm'] ?? []);

            return $this->redirectTo($this->project->href('settings/permissions/' . ($type === 'usergroup' ? 'groups' : 'roles')));
        }

        // Setup the page
        $this->permissionsFor($type);

        return $this->render("project_settings/permissions/index.phtml");
    }

    /**
     * Saves the submitted permissions.
     *
     * The posted permission IDs are only used to look up the action name, every row
     * that is created, updated or deleted is scoped to this project, so a project
     * manager can't touch other projects or the global defaults.
     *
     * @param string $type  `usergroup` or `role`
     * @param mixed  $posted `perm[type_id][permission_id] => value`
     */
    private function savePermissions(string $type, mixed $posted): void
    {
        if (!is_array($posted)) {
            return;
        }

        $validActions = [];
        foreach (permission_actions() as $action) {
            foreach ((array) $action as $act) {
                $validActions[] = $act;
            }
        }

        $globalDefaults = Permission::defaults(0, 0, $type);

        foreach ($posted as $typeId => $permissions) {
            if (!is_array($permissions) || !$this->isValidTypeId($type, (int) $typeId)) {
                continue;
            }

            $typeId = (int) $typeId;

            foreach ($permissions as $permissionId => $value) {
                // Only used to find out which action this is for
                $source = Permission::find((int) $permissionId);

                if (!$source || !in_array($source->action, $validActions, true)) {
                    continue;
                }

                $action = $source->action;

                // 0 = defaults row (allow/deny), others can also reset to defaults (-1)
                $value = (int) $value;
                if (!in_array($value, $typeId === 0 ? [0, 1] : [-1, 0, 1], true)) {
                    continue;
                }

                $existing = Permission::select()
                    ->where('project_id', $this->project->id)
                    ->where('type', $type)
                    ->where('type_id', $typeId)
                    ->where('action', $action)
                    ->exec()
                    ->fetch();

                // Back to the defaults, or same as the global default
                if ($value === -1 || ($typeId === 0 && isset($globalDefaults[$action]) && (int) $globalDefaults[$action]->value === $value)) {
                    if ($existing) {
                        $existing->delete();
                    }
                    continue;
                }

                if ($existing) {
                    if ((int) $existing->value !== $value) {
                        $existing->set('value', $value);
                        $existing->save();
                    }
                } else {
                    (new Permission([
                        'project_id' => $this->project->id,
                        'type'       => $type,
                        'type_id'    => $typeId,
                        'action'     => $action,
                        'value'      => $value,
                    ]))->save();
                }
            }
        }
    }

    /**
     * Checks the group/role ID is valid for this project, 0 being the defaults.
     */
    private function isValidTypeId(string $type, int $typeId): bool
    {
        if ($typeId === 0) {
            return true;
        }

        if ($type === 'usergroup') {
            $group = Group::find($typeId);

            return $group && !$group->is_admin;
        }

        $role = ProjectRole::find($typeId);

        return $role && in_array((int) $role->project_id, [0, (int) $this->project->id], true);
    }

    /**
     * Fetches all the data for the permission listing page.
     */
    private function permissionsFor(string $type): void
    {
        // Fetch groups, set permissions and actions arrays
        if ($type == 'usergroup') {
            $groups = Group::select()->where('is_admin', 1, '!=')->exec()->fetch_all();
            $groups = array_merge(array(new Group(array('id' => 0, 'name' => l('defaults')))), $groups);
        }
        // Role
        elseif ($type == 'role') {
            $groups = ProjectRole::select()->custom_sql("WHERE project_id = 0 OR project_id = {$this->project->id}")->exec()->fetch_all();
            $groups = array_merge(array(new ProjectRole(array('id' => 0, 'name' => l('defaults'), 'project_id' => 0))), $groups);
        }
        $permissions = [];

        // Loop over the groups
        foreach ($groups as $group) {
            // Set the group array in the permissions array
            if (!isset($permissions[$group->id])) {
                $permissions[$group->id] = [];
            }

            // Loop over the permissions for the group
            foreach (Permission::get_permissions($this->project->id, $group->id, $type) as $action => $perm) {
                // Add the permission object to the permissions array
                $permissions[$group->id][$action] = $perm;
            }
        }

        // Send it all the to view.
        $this->set('groups', $groups);
        $this->set('permissions', $permissions);
        $this->set('actions', permission_actions());
    }
}
