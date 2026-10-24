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
use Traq\Models\ProjectRole;
use Traq\Models\User;
use Traq\Models\UserRole;

/**
 * Project members controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class MembersController extends AppController
{
    public function index(): Response
    {
        $projectRoles = ProjectRole::select_options();
        $userRoles = UserRole::select()->where('project_id', $this->project->id)->exec()->fetch_all();

        if ($this->isJson) {
            return $this->json([
                'members' => $userRoles,
            ]);
        }

        return $this->render('project_settings/members/index.phtml', [
            'userRoles' => $userRoles,
            'projectRoles' => $projectRoles,
        ]);
    }

    public function add(): Response
    {
        // Get the user
        $user = User::find('username', Request::$post['username']);

        // Check the username...
        $errors = [];

        // User exists?
        if ($user === false) {
            $errors['username'] = l('errors.users.doesnt_exist');
        }
        // Username entered?
        elseif (!isset(Request::$post['username']) || Request::$post['username'] == '') {
            $errors['username'] = l('errors.users.username_blank');
        }
        // Already a project member?
        elseif (UserRole::select('id')->where(array(array('project_id', $this->project->id), array('user_id', $user->id)))->exec()->row_count()) {
            $errors['username'] = l('errors.users.already_a_project_member');
        }

        // Any errors?
        if (count($errors)) {
            $this->set('errors', $errors);
            return $this->index();
        }
        // Role must exist and belong to this project (or be global)
        elseif (!$this->isValidRole((int) (Request::$post['role'] ?? 0))) {
            $this->set('errors', ['role' => l('errors.users.doesnt_exist')]);
            return $this->index();
        }
        // Create role
        else {
            $userRole = new UserRole([
                'project_id' => $this->project->id,
                'user_id' => $user->id,
                'project_role_id' => (int) Request::$post['role']
            ]);
            $userRole->save();

            return $this->redirectTo($this->project->href('settings/members'));
        }
    }

    public function save()
    {
        $roles = Request::$post['role'] ?? [];

        foreach (is_array($roles) ? $roles : [] as $userRoleId => $roleId) {
            $userRole = UserRole::find((int) $userRoleId);

            // Only members of this project, and only roles for this project
            if (
                !$userRole
                || (int) $userRole->project_id !== (int) $this->project->id
                || !$this->isValidRole((int) $roleId)
            ) {
                continue;
            }

            $userRole->project_role_id = (int) $roleId;
            $userRole->save();
        }

        return $this->redirectTo($this->project->href('settings/members'));
    }

    /**
     * Checks the role exists and is either global or belongs to this project.
     */
    private function isValidRole(int $roleId): bool
    {
        $role = ProjectRole::find($roleId);

        return $role && in_array((int) $role->project_id, [0, (int) $this->project->id], true);
    }

    public function delete(int $id): Response
    {
        if ($userRole = UserRole::select('id')->where(array(array('project_id', $this->project->id), array('user_id', $id)))->exec()->fetch()) {
            $userRole->delete();
        }

        if ($this->isApi) {
            if ($userRole) {
                return $this->json([
                    'success' => true,
                ]);
            } else {
                return $this->json([
                    'success' => false,
                ]);
            }
        } else {
            return $this->redirectTo($this->project->href('settings/members'));
        }
    }
}
