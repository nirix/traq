<?php
/*!
 * Traq
 * Copyright (C) 2009-2016 Jack P.
 * Copyright (C) 2012-2016 Traq.io
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
        $this->title($this->translate('register'));
        return $this->render('users/new.phtml', ['user' => new User]);
    }

    /**
     * Validate and create account.
     */
    public function createAction()
    {
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
            $this->title($this->translate('register'));
            return $this->render('users/new.phtml', ['user' => $user]);
        }
    }

    /**
     * Activate account.
     */
    public function activateAction($activation_code)
    {
        if ($activation = UserActivationCode::get('email_validation', $activation_code)) {
            $activation->delete();
            return $this->redirectTo('session_new');
        } else {
            return $this->show404();
        }
    }

    /**
     * Get submitted form data.
     *
     * @return array
     */
    protected function userParams()
    {
        return [
            'name'     => Request::$post->get('name'),
            'username' => Request::$post->get('username'),
            'email'    => Request::$post->get('email'),
            'password' => Request::$post->get('password'),
            'password_confirmation' => Request::$post->get('password_confirmation')
        ];
    }
}
