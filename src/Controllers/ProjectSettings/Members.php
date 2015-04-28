<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
 * Copyright (C) 2012-2015 Traq.io
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

namespace Traq\Controllers\ProjectSettings;

use Avalon\Http\Request;
use Traq\Models\User;
use Traq\Models\UserRole;
use Traq\Models\ProjectRole;

/**
 * Project members controller
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers\ProjectSettings
 */
class Members extends AppController
{
    public function indexAction()
    {
        $userRoles = UserRole::select()->where('project_id = ?', $this->project->id)->fetchAll();

        return $this->respondTo(function ($format) use ($userRoles) {
            if ($format == "html") {
                return $this->render("project_settings/members/index.phtml", [
                    'userRoles' => $userRoles
                ]);
            } elseif ($format == "json") {
                return $this->jsonResponse($userRoles);
            }
        });
    }

    public function createAction()
    {
        $errors = [];
        $user   = User::find('username', Request::post('username'));
        $role   = ProjectRole::find(Request::post('role_id'));

        // Check if they entered a username
        if (empty(Request::post('username'))) {
            $errors['username'] = $this->translate('errors.validations.required', [
                'field' => $this->translate('username')
            ]);
        } elseif (!$user) {
            $errors['username'] = $this->translate('errors.users.doesnt_exist');
        }

        // Check if the user is already a member of the project
        if ($user) {
            $member = UserRole::select('id')
                ->where('project_id = ?', $this->project->id)
                ->andWhere('user_id = ?', $user->id);
        }

        if ($user && isset($member) && $member->rowCount() > 0) {
            $errors['username'] = $this->translate('errors.users.already_a_project_member');
        }

        // Check if they chose a role
        if (Request::post('role_id', '') == '') {
            $errors['role_id'] = $this->translate('errors.validations.required', [
                'field' => $this->translate('role')
            ]);
        }

        // Check if the role exists
        if (!$role) {
            $errors['role'] = $this->translate('errors.roles.doesnt_exist');
        }

        // Check if the role belongs to the project
        if ($role && ($role->project_id != 0 && $role->project_id != $this->project->id)) {
            $errors['role'] = $this->translate('errors.roles.invalid_role');
        }

        if (count($errors)) {
            return $this->render('project_settings/members/new.phtml', [
                'errors' => $errors
            ]);
        } else {
            $userRole = new UserRole([
                'project_id'      => $this->project->id,
                'project_role_id' => $role->id,
                'user_id'         => $user->id
            ]);
            $userRole->save();

            return $this->redirectTo('project_settings_members');
        }
    }

    public function action_save()
    {
        if (Request::method() == 'post') {
            foreach (Request::$post['role'] as $role_id => $value) {
                $role = UserRole::find($role_id);
                $role->project_role_id = $value;
                $role->save();
            }
            Request::redirectTo($this->project->href('settings/members'));
        }
    }

    public function destroyAction($id)
    {
        $userRole = UserRole::select()->where('project_id = ?', $this->project->id)
            ->andWhere('user_id = ?', $id)
            ->fetch();

        if (!$userRole) {
            return $this->show404();
        } else {
            $userRole->delete();
            return $this->redirectTo('project_settings_members');
        }
    }
}
