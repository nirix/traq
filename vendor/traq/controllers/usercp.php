<?php
/*!
 * Traq
 * Copyright (C) 2009-2012 Traq.io
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

namespace traq\controllers;

use avalon\http\Request;
use avalon\output\View;
use \FishHook;

use traq\models\Subscription;

/**
 * UserCP controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Usercp extends AppController
{
    public function __construct()
    {
        parent::__construct();
        View::set('user', $this->user);
    }

    /**
     * The index page.
     */
    public function action_index()
    {
        // Make sure the user is logged in
        if (!LOGGEDIN) {
            $this->show_no_permission();
        }

        // Clone the logged in user object
        $user = clone $this->user;

        // Has the form been submitted?
        if (Request::method() == 'post') {
            $data = array(
                'name'   => Request::post('name', $user->name),
                'email'  => Request::post('email', $user->email),
                'locale' => Request::post('locale', $user->locale)
            );

            FishHook::add('controller:users::usercp/save', array(&$data));

            // Set the info
            $user->set($data);
            $user->option('watch_created_tickets', Request::post('watch_created_tickets'));

            // Save the user
            if ($user->save()) {
                // Redirect if successful
                if ($this->is_api) {
                    return \API::response(1, array('user' => $user));
                } else {
                    Request::redirect(Request::requestUri());
                }
            }
        }

        View::set('user', $user);
    }

    /**
     * Password page.
     */
    public function action_password()
    {
        // Make sure the user is logged in
        if (!LOGGEDIN or $this->is_api) {
            return $this->show_no_permission();
        }

        // Clone the logged in user object
        $user = clone $this->user;

        if (Request::method() == 'post') {
            $data = array(
                'old_password'     => Request::$post['password'],
                'new_password'     => Request::$post['new_password'],
                'confirm_password' => Request::$post['confirm_password']
            );

            FishHook::add('controller:users::usercp/password/save', array(&$data));

            // Set the info
            $user->set($data);

            if($user->is_valid()) {
                $user->set_password($data['new_password']);

                // Save the user
                if ($user->save()) {
                    // Redirect if successful
                    Request::redirect(Request::requestUri());
                }
            }
        }

        View::set('user', $user);
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
