<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack P.
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
 * @package Traq\Controllers\ProjectSettings
 * @author Jack P.
 * @since 3.0.0
 */
class Members extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->addCrumb($this->translate('members'), $this->generateUrl('project_settings_members'));
    }

    /**
     * @return \Avalon\Http\Response
     */
    public function indexAction()
    {
        $userRoles = queryBuilder()->select(
            'u.id AS user_id',
            'u.name AS user_name',
            'r.id AS role_id',
            'r.name AS role_name',
            'ur.project_role_id'
        )
        ->from(PREFIX . 'user_roles', 'ur')
        ->where('ur.project_id = ?')
        ->leftJoin('ur', PREFIX . 'users', 'u', 'u.id = ur.user_id')
        ->leftJoin('ur', PREFIX . 'project_roles', 'r', 'r.id = ur.project_role_id')
        ->setParameter(0, $this->currentProject['id'])
        ->execute()
        ->fetchAll();

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

    /**
     * Add project member.
     *
     * @return \Avalon\Http\RedirectResponse|\Avalon\Http\Response
     */
    public function createAction()
    {
        $errors = [];
        $user   = User::find('username', Request::$post->get('username'));
        $role   = ProjectRole::find(Request::$post->get('role_id'));

        // Check if they entered a username
        if (!Request::$post->has('username') || Request::$post->get('username') == '') {
            $errors['username'] = $this->translate('errors.validations.required', [
                'field' => $this->translate('username')
            ]);
        } elseif (!$user) {
            $errors['username'] = $this->translate('errors.users.doesnt_exist');
        }

        // Check if the user is already a member of the project
        if ($user) {
            $member = UserRole::select('id')
                ->where('project_id = ?')->setParameter(0, $this->currentProject['id'])
                ->andWhere('user_id = ?')->setParameter(1, $user->id)
                ->execute();
        }

        if ($user && isset($member) && $member->rowCount() > 0) {
            $errors['username'] = $this->translate('errors.users.already_a_project_member');
        }

        // Check if they chose a role
        if (Request::$post->get('role_id', '') == '') {
            $errors['role_id'] = $this->translate('errors.validations.required', [
                'field' => $this->translate('role')
            ]);
        }

        // Check if the role exists
        if (!$role) {
            $errors['role'] = $this->translate('errors.roles.doesnt_exist');
        }

        // Check if the role belongs to the project
        if ($role && ($role->project_id != 0 && $role->project_id != $this->currentProject['id'])) {
            $errors['role'] = $this->translate('errors.roles.invalid_role');
        }

        if (count($errors)) {
            return $this->render('project_settings/members/new.phtml', [
                'errors' => $errors
            ]);
        } else {
            $userRole = new UserRole([
                'project_id'      => $this->currentProject['id'],
                'project_role_id' => $role->id,
                'user_id'         => $user->id
            ]);
            $userRole->save();

            return $this->redirectTo('project_settings_members');
        }
    }

    /**
     * Save project member roles.
     *
     * @return \Avalon\Http\RedirectResponse
     */
    public function saveAllAction()
    {
        foreach (Request::$post->get('user', [], false) as $userId => $info) {
            $userRole = UserRole::select()->where('project_id = ?')
                ->andWhere('user_id = ?')
                ->setParameter(0, $this->currentProject['id'])
                ->setParameter(1, $userId)
                ->fetch();

            $userRole->project_role_id = $info['role_id'];
            $userRole->save();
        }

        return $this->redirectTo("project_settings_members");
    }

    /**
     * Remove project member.
     *
     * @param $id User ID
     *
     * @return \Avalon\Http\RedirectResponse|\Avalon\Http\Response
     */
    public function destroyAction($id)
    {
        $userRole = UserRole::select()->where('project_id = ?')
            ->andWhere('user_id = ?')
            ->setParameter(0, $this->currentProject['id'])
            ->setParameter(1, $id)
            ->fetch();

        if (!$userRole) {
            return $this->show404();
        }

        $userRole->delete();

        return $this->respondTo(function ($format) {
            if ($format == 'json') {
                return $this->jsonResponse([
                    'deleted' => true
                ]);
            } else {
                return $this->redirectTo('project_settings_members');
            }
        });
    }
}
