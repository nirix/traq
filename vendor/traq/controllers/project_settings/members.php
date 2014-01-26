<?php
/*!
 * Traq
 * Copyright (C) 2009-2014 Jack Polgar
 * Copyright (C) 2012-2014 Traq.io
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

namespace traq\controllers\ProjectSettings;

use avalon\http\Request;
use avalon\output\View;

use traq\models\User;
use traq\models\UserRole;

/**
 * Project members controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Members extends AppController
{
    public function action_index()
    {
        View::set('user_roles', UserRole::select()->where('project_id', $this->project->id)->exec()->fetch_all());
    }

    public function action_new()
    {
        // Get the user
        $user = User::find('username', Request::$post['username']);

        // Check the username...
        $errors = array();

        // User exists?
        if ($user === false) {
            $errors['username'] = l('errors.users.doesnt_exist');
        }
        // Username entered?
        elseif (!isset(Request::$post['username']) or Request::$post['username'] == '') {
            $errors['username'] = l('errors.users.username_blank');
        }
        // Already a project member?
        elseif (UserRole::select('id')->where(array(array('project_id', $this->project->id), array('user_id', $user->id)))->exec()->row_count()) {
            $errors['username'] = l('errors.users.already_a_project_member');
        }


        // Any errors?
        if (count($errors)) {
            $this->action_index();
            $this->render['view'] = 'project_settings/members/index';
            View::set('errors', $errors);
        }
        // Create role
        else {
            $user_role = new UserRole(array(
                'project_id' => $this->project->id,
                'user_id' => $user->id,
                'project_role_id' => Request::$post['role']
            ));
            $user_role->save();

            Request::redirectTo($this->project->href('settings/members'));
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

    public function action_delete($user_id)
    {
        if ($user_role = UserRole::select('id')->where(array(array('project_id', $this->project->id), array('user_id', $user_id)))->exec()->fetch()) {
            $user_role->delete();
        }

        if ($this->is_api) {
            if ($user_role) {
                return \API::response(1);
            } else {
                return \API::response(0);
            }
        } else {
            Request::redirectTo($this->project->href('settings/members'));
        }
    }
}
