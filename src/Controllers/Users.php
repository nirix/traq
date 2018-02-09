<?php
/*!
 * Traq
 * Copyright (C) 2009-2018 Jack P.
 * Copyright (C) 2012-2018 Traq.io
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

namespace Traq\Controllers;

use Avalon\Http\Request;
use Traq\Models\User;
use Traq\Models\UserActivationCode;
use Traq\Helpers\Notification;

/**
 * User controller.
 *
 * @package Traq\Controllers
 * @author Jack P.
 * @since 3.0.0
 */
class Users extends AppController
{
    /**
     * Registration form.
     */
    public function newAction()
    {
        $this->addCrumb($this->translate('register'), routeUrl('register'));
        return $this->render('users/new.phtml', ['user' => new User]);
    }

    /**
     * Validate and create account.
     */
    public function createAction()
    {
        $this->addCrumb($this->translate('register'), routeUrl('register'));

        // Validate user
        $user = new User($this->userParams());

        // Check for errors
        if ($user->validate()) {
            $user->save();

            // Is email validation turned on?
            if (setting('email_validation')) {
                // Insert validation row
                $activationCode = random_hash();
                $this->db->insert(PREFIX . 'user_activation_codes', [
                    'user_id'         => $user->id,
                    'activation_code' => $activationCode,
                    'type'            => 'email_validation'
                ]);

                // Send notification and render login form
                Notification::accountActivation($user, $activationCode)->send();
                return $this->render("sessions/new.phtml", ['activationRequired' => true]);
            }

            return $this->redirectTo('session_new');
        } else {
            return $this->render('users/new.phtml', ['user' => $user]);
        }
    }

    /**
     * Activate account.
     */
    public function activateAction($activationCode)
    {
        if ($activation = UserActivationCode::get('email_validation', $activationCode)) {
            $activation->delete();
            return $this->redirectTo('session_new');
        } else {
            return $this->show404();
        }
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function showAction($id)
    {
        $user = User::find($id);

        return $this->respondTo(function ($format) use ($user) {
            if ($format === 'html') {
                return $this->render('users/profile.phtml', [
                    'profile' => $user
                ]);
            } elseif ($format === 'json') {
                return $this->jsonResponse($user->publicArray());
            }
        });
    }
    /**
     * Get submitted form data.
     *
     * @return array
     */
    protected function userParams()
    {
        return [
            'name'     => Request::$post['name'],
            'username' => Request::$post['username'],
            'email'    => Request::$post['email'],
            'password' => Request::$post['password'],
            'password_confirmation' => Request::$post['password_confirmation']
        ];
    }

}
