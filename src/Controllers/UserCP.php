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

namespace Traq\Controllers;

use Traq\Models\User;
use Traq\Models\Subscription;

/**
 * UserCP controller
 *
 * @author Jack P.
 * @since 3.0.0
 * @package Traq\Controllers
 */
class UserCP extends AppController
{
    protected $layout = "usercp.phtml";

    public function __construct()
    {
        parent::__construct();
        $this->set('user', clone $this->currentUser);

        $this->before('*', function () {
            // Make sure the user is logged in
            if (!$this->currentUser) {
                $this->layout = "default.phtml";
                return $this->show403();
            }
        });
    }

    /**
     * @return \Avalon\Http\Response
     */
    public function indexAction()
    {
        return $this->render("usercp/index.phtml");
    }

    /**
     * @return \Avalon\Http\Response
     */
    public function saveAction()
    {
        $user = User::find($this->currentUser->id);

        $data = array(
            'name'     => $this->request->post('name', $user->name),
            'email'    => $this->request->post('email', $user->email),
            'language' => $this->request->post('language', $user->language)
        );

        $correctPassword = false;
        if (!$user->authenticate($this->request->post('current_password'))) {
            $user->addError('password', ['error' => "errors.incorrect_password"]);
        } else {
            $correctPassword = true;
        }

        // Set the info
        $user->set($data);
        $user->validates();

        // Save the user
        if ($correctPassword && $user->save()) {
            return $this->respondTo(function ($format) {
                if ($format == "html") {
                    return $this->redirectTo('usercp');
                } else {
                    return $this->jsonResponse($user);
                }
            });
        } else {
            return $this->render("usercp/index.phtml", ['user' => $user]);
        }
    }

    /**
     * Password page.
     *
     * @return \Avalon\Http\Response
     */
    public function passwordAction()
    {
        // Clone the logged in user object
        $user = User::find($this->currentUser->id);
        $this->set(compact('user'));

        return $this->render('usercp/password.phtml');
    }

    /**
     * Update password.
     *
     * @return \Avalon\Http\Response
     */
    public function savePasswordAction()
    {
        $user = User::find($this->currentUser->id);
        $this->set(compact('user'));

        // Authenticate current password
        if (!$user->authenticate($this->request->post('current_password'))) {
            $user->addError('password', ['error' => "errors.incorrect_password"]);
        } else {
            // Confirm passwords
            if ($this->request->post('password') !== $this->request->post('password_confirmation')) {
                $user->addError('password', ['error' => "errors.validations.fields_dont_match"]);
            } else {
                // Update password
                $user->setPassword($this->request->post('password'));

                // Save and redirect
                if ($user->save()) {
                    return $this->redirectTo('usercp_password');
                }
            }
        }

        // Incorrect password or new passwords don't match.
        return $this->render('usercp/password.phtml');
    }

    /**
     * Generate the users API key.
     */
    public function action_create_api_key()
    {
        $this->user->generate_api_key();
        $this->user->save();
        Request::redirectTo('usercp');
    }

    /**
     * Subscriptions page
     */
    public function action_subscriptions()
    {
        $subscriptions = Subscription::select()->where('user_id', $this->user->id)->exec()->fetch_all();
        View::set('subscriptions', $subscriptions);
    }
}
