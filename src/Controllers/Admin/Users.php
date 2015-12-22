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

namespace Traq\Controllers\Admin;

use Avalon\Http\Request;
use Traq\Models\User;
use Traq\Traits\Controllers\CRUD;

/**
 * Admin Users controller.
 *
 * @package Traq\Controllers\Admin
 * @author Jack P.
 * @since 3.0.0
 */
class Users extends AppController
{
    use CRUD;

    // Model class and views directory
    protected $model    = '\Traq\Models\User';
    protected $viewsDir = 'admin/users';

    // Singular and plural form
    protected $singular = 'user';
    protected $plural   = 'users';

    // Redirect route names
    protected $afterCreateRedirect  = 'admin_users';
    protected $afterDestroyRedirect = 'admin_users';

    public function __construct()
    {
        parent::__construct();
        $this->title($this->translate('users'));
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
        $params = $this->modelParams();

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
     * @return array
     */
    protected function modelParams()
    {
        return [
            'username' => Request::$post->get('username'),
            'password' => Request::$post->get('password'),
            'name'     => Request::$post->get('name'),
            'email'    => Request::$post->get('email'),
            'group_id' => Request::$post->get('group_id')
        ];
    }
}
