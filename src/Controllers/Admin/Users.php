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

namespace Traq\Controllers\Admin;

use Avalon\Http\Request;
use Traq\Models\User;

/**
 * Admin Users controller.
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers
 */
class Users extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('users'));
    }

    /**
     * Users listing page.
     */
    public function indexAction()
    {
        $users = User::all();

        return $this->respondTo(function ($format) use ($users) {
            if ($format == 'html') {
                return $this->render('admin/users/index.phtml', [
                    'users' => $users
                ]);
            } elseif ($format == 'json') {
                return $this->jsonResponse($users);
            }
        });
    }

    /**
     * New user page.
     */
    public function newAction()
    {
        $this->title($this->translate('new'));

        if ($this->isOverlay) {
            return $this->render('admin/users/new.overlay.phtml', [
                'user' => new User
            ]);
        } else {
            return $this->render('admin/users/new.phtml', [
                'user' => new User
            ]);
        }
    }

    /**
     * Create user.
     */
    public function createAction()
    {
        $this->title($this->translate('new'));

        $user = new User($this->userParams());

        if ($user->save()) {
            return $this->redirectTo('admin_users');
        } else {
            $this->set('user', $user);
            return $this->respondTo(function ($format) {
                if ($format == "html") {
                    return $this->render('admin/users/new.phtml');
                } elseif ($format == "json") {
                    return $this->jsonResponse($user);
                }
            });
        }
    }

    /**
     * Edit user.
     *
     * @param integer $id
     */
    public function editAction($id)
    {
        $this->title($this->translate('edit'));

        // Find the user
        $user = User::find($id);

        if ($this->isOverlay) {
            return $this->render('admin/users/edit.overlay.phtml', [
                'user' => $user
            ]);
        } else {
            return $this->render('admin/users/edit.phtml', [
                'user' => $user
            ]);
        }
    }

    /**
     * Save user.
     *
     * @param integer $id
     */
    public function saveAction($id)
    {
        $this->title($this->translate('edit'));

        // Fetch and update user
        $user   = User::find($id);
        $params = $this->userParams();

        // Update password.
        if (!empty($params['password'])) {
            $user->setPassword($params['password']);
        }

        // Remove password from params
        unset($params['password']);

        // Set the rest of the params
        $user->set($params);

        if ($user->save()) {
            return $this->redirectTo('admin_users');
        } else {
            $this->set('user', $user);
            return $this->respondTo(function ($format) use ($user) {
                if ($format == "html") {
                    return $this->render('admin/users/edit.phtml');
                } elseif ($format == "json") {
                    return $this->jsonResponse($user);
                }
            });
        }
    }

    /**
     * Delete user.
     *
     * @param integer $id
     */
    public function destroyAction($id)
    {
        // Find the user, delete and redirect.
        $user = User::find($id)->delete();

        return $this->respondTo(function ($format) use ($user) {
            if ($format == "html") {
                return $this->redirectTo('admin_users');
            } elseif ($format == "json") {
                return $this->jsonResponse([
                    'deleted' => true,
                    'user'    => $user->toArray()
                ]);
            }
        });
    }

    /**
     * @return array
     */
    protected function userParams()
    {
        return [
            'username' => Request::post('username'),
            'password' => Request::post('password'),
            'name'     => Request::post('name'),
            'email'    => Request::post('email'),
            'group_id' => Request::post('group_id')
        ];
    }
}
