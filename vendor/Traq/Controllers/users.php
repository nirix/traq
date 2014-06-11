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

namespace traq\controllers;

use \FishHook;
use avalon\core\Load;
use avalon\http\Request;
use avalon\output\View;

use traq\helpers\Notification;
use traq\models\User;

/**
 * User controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class Users extends AppController
{
    public $before = array(
        'login'    => array('already_logged_in'),
        'register' => array('already_logged_in')
    );

    /**
     * User profile page.
     *
     * @param integer $user_id
     */
    public function action_view($user_id)
    {
        // If the user doesn't exist
        // display the 404 page.
        if (!$user = User::find($user_id)) {
            return $this->show_404();
        }

        // Set the title
        $this->title(l('users'));
        $this->title(l('xs_profile', $user->name));

        Load::helper('tickets');
        View::set('profile', $user);
    }

    /**
     * Handles the login page.
     */
    public function action_login()
    {
        // Set the title
        $this->title(l('login'));

        // Check if the form has been submitted
        if (Request::method() == 'post') {
            // Try to find the user in the database and verify their password
            if ($user = User::find('username', Request::$post['username'])
            and $user->verify_password(Request::$post['password'])) {
                // User found and verified, set the cookie and redirect them
                // to the index page if no "redirect" page was set.
                if ($user->is_activated()) {
                    setcookie('_traq', $user->login_hash, time() + (2 * 4 * 7 * 24 * 60 * 60 * 60), '/');
                    Request::redirect(isset(Request::$post['redirect']) ? Request::$post['redirect'] : Request::base());
                }
                // Tell the user to activate
                else {
                    View::set('validation_required', true);
                }
            }
            // No user found
            else {
                View::set('error', true);
            }
        }
    }

    /**
     * Handles the logout request.
     */
    public function action_logout()
    {
        setcookie('_traq', sha1(time()), time() + 5, '/');
        Request::redirectTo();
    }

    /**
     * Handles the register page and account creation.
     */
    public function action_register()
    {
        if (!settings('allow_registration')) {
            return $this->show_404();
        }

        $validation_required = false;
        $this->title(l('register'));

        $user = new User;

        // Check if the form has been submitted
        if (Request::method() == 'post') {
            // Build the data array
            $data = array(
                'username' => Request::$post['username'],
                'name'     => Request::$post['name'],
                'password' => Request::$post['password'],
                'email'    => Request::$post['email']
            );

            // Create a model with the data
            $user = new User($data);

            // Email validation
            if (settings('email_validation')) {
                $user->option('validation_key', sha1($user->username . $user->name . microtime() . rand(0, 1000)));
            }

            // Run plugin hooks
            FishHook::run('controller:users.register', array(&$user));

            // Check if the model is valid
            if ($user->save()) {
                // Send validation email
                if (settings('email_validation')) {
                    Notification::send_to(
                        $user,
                        'email_validation',
                        array(
                            'link' => "http://" . $_SERVER['HTTP_HOST'] . Request::base("users/validate/" . $user->option('validation_key'))
                        )
                    );

                    $validation_required = true;
                }
                // Redirect to login page
                else {
                    Request::redirectTo('login');
                }
            }
        }

        View::set(compact('user', 'validation_required'));
    }

    /**
     * Account validation.
     */
    public function action_validate($key)
    {
        $user = User::select()->where('options', '%"validation_key":"' . $key . '"%', 'LIKE')->exec()->fetch();
        $user->option('validation_key', null);
        $user->save();

        $this->render['view'] = 'users/login';
        View::set('validated', true);
    }

    /**
     * Forgot/Reset password page.
     */
    public function action_reset_password($key = null)
    {
        // Reset key provided?
        if ($key !== null) {
            // Find user
            if ($user = User::select()->where('options', '%"reset_password_key":"' . $key . '"%', 'LIKE')->exec()->fetch()) {
                // Generate new password
                $new_password = substr(random_hash(), 0, 10);

                // Set new password, clear reset key and save
                $user->set_password($new_password);
                $user->option('reset_password_key', null);
                $user->save();

                // Send data to the view
                View::set('password_reset', true);
                View::set('new_password', $new_password);
            }
        }
        // Find user and generate key
        else {
            // Check if the form has been submitted
            if (Request::method() == 'post') {
                // Generate key
                if ($user = User::find('username', Request::$post['username'])) {
                    // Generate reset key
                    $key = random_hash();

                    // Set reset key option
                    $user->option('reset_password_key', $key);
                    $user->save();

                    // Send email
                    Notification::send(
                        $user, // User object
                        l('notifications.password_reset.subject'),     // Subject
                        l('notifications.password_reset.message',    // Message
                            settings('title'), // Installation title
                            $user->name,       // Users name
                            $user->username,   // Users username
                            "http://" . $_SERVER['HTTP_HOST'] . Request::base("/login/resetpassword/{$key}"), // Reset password URL
                            $_SERVER['REMOTE_ADDR'] // IP of reset request
                        )
                    );
                    View::set('reset_email_sent', true);
                } else {
                    View::set('error', true);
                }
            }
        }
    }

    /**
     * Redirect to the front page if the user is logged in.
     */
    public function already_logged_in()
    {
        if (LOGGEDIN) {
            Request::redirectTo('/');
        }
    }
}
