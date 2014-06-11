<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

namespace traq\controllers\ProjectSettings;

use avalon\http\Request;
use avalon\output\View;

use traq\models\Permission;
use traq\models\Group;
use traq\models\ProjectRole;

/**
 * Project permissions controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Permissions extends AppController
{
    public function __construct()
    {
        parent::__construct();

        // Set Form::select() options
        View::set('options', array(
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
    public function action_index($type)
    {
        // If the type of permissions is 'groups', set it to 'usergroups'.
        $type = $type == 'groups' ? 'usergroup' : 'role';

        // Has the form been submitted?
        if (Request::method() == 'post') {
            $global_defaults = Permission::defaults(0, 0, $type);

            // Loop over group/role and get id and permissions
            foreach (Request::$post['perm'] as $type_id => $permissions) {
                // Loop over permissions for id and value
                foreach ($permissions as $permission_id => $value) {
                    // Fetch permission
                    $perm = Permission::find($permission_id);

                    // Are we dealing with a default?
                    if ($type_id == 0) {
                        // Does it exist?
                        if ($perm->project_id > 0) {
                            // We we need to delete it?
                            if ($global_defaults[$perm->action]->value == $value) {
                                $perm->delete();
                            }
                            // or update it?
                            elseif ($perm->value != $value) {
                                $perm->set('value', $value);
                                $perm->save();
                            }
                        }
                        // It doesn't exist
                        else {
                            // Should we create it?
                            if ($perm->value != $value) {
                                // Create the permission
                                $perm = new Permission(array(
                                    'project_id' => $this->project->id,
                                    'type'       => $type,
                                    'type_id'    => $type_id,
                                    'action'     => $perm->action,
                                    'value'      => $value
                                ));
                                $perm->save();
                            }
                        }
                    }
                    // Use default
                    elseif ($perm and $perm->type_id == $type_id and $value == -1 and $type_id > 0) {
                        $perm->delete();
                    }
                    // Allow / Deny
                    elseif ($value == 0 or $value == 1) {
                        // Update
                        if ($perm and $perm->type_id == $type_id) {
                            $perm->value = $value;
                            $perm->save();
                        }
                        // Create
                        else {
                            $perm = new Permission(array(
                                'project_id' => $this->project->id,
                                'type'       => $type,
                                'type_id'    => $type_id,
                                'action'     => $perm->action,
                                'value'      => $value
                            ));
                            $perm->save();
                        }
                    }
                }
            }

            Request::redirect(Request::requestUri());
        }

        // Setup the page
        $this->permissions_for($type);
    }

    /**
     * Fetches all the data for the permission listing page.
     */
    private function permissions_for($type)
    {
        // Fetch groups, set permissions and actions arrays
        if ($type == 'usergroup') {
            $groups = Group::select()->where('is_admin', 1, '!=')->exec()->fetch_all();
            $groups = array_merge(array(new Group(array('id' => 0, 'name' => l('defaults')))), $groups);
        }
        // Role
        elseif ($type == 'role')
        {
            $groups = ProjectRole::select()->custom_sql("WHERE project_id = 0 OR project_id = {$this->project->id}")->exec()->fetch_all();
            $groups = array_merge(array(new ProjectRole(array('id' => 0, 'name' => l('defaults'), 'project_id' => 0))), $groups);
        }
        $permissions = array();

        // Loop over the groups
        foreach ($groups as $group) {
            // Set the group array in the permissions array
            if (!isset($permissions[$group->id])) {
                $permissions[$group->id] = array();
            }

            // Loop over the permissions for the group
            foreach (Permission::get_permissions($this->project->id, $group->id, $type) as $action => $perm) {
                // Add the permission object to the permissions array
                $permissions[$group->id][$action] = $perm;
            }
        }

        // Send it all the to view.
        View::set('groups', $groups);
        View::set('permissions', $permissions);
        View::set('actions', permission_actions());
    }
}
