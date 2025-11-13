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

    public function new()
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
        // Create role
        else {
            $userRole = new UserRole([
                'project_id' => $this->project->id,
                'user_id' => $user->id,
                'project_role_id' => Request::$post['role']
            ]);
            $userRole->save();

            return $this->redirectTo($this->project->href('settings/members'));
        }
    }

    public function save()
    {
        if (Request::method() == 'POST') {
            foreach (Request::$post['role'] as $role_id => $value) {
                $role = UserRole::find($role_id);
                $role->project_role_id = $value;
                $role->save();
            }

            return $this->redirectTo($this->project->href('settings/members'));
        }
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
