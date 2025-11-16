<?php
/*!
 * Traq
 * Copyright (C) 2009-2025 Jack Polgar
 * Copyright (C) 2012-2025 Traq.io
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

namespace Traq\Controllers;

use \FishHook;
use Avalon\Http\Request;
use Avalon\Http\Response;
use Traq\Controllers\AppController;
use Traq\Helpers\Notification;
use Traq\Models\User;

/**
 * User controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
class UserController extends AppController
{
    public $before = [
        'login'    => ['alreadyLoggedIn'],
        'register' => ['alreadyLoggedIn']
    ];

    /**
     * Handles the login page.
     */
    public function login(): Response
    {
        // Set the title
        $this->title(l('login'));

        // Check if the form has been submitted
        if (Request::method() == 'POST') {
            return $this->createSession();
        }

        return $this->render('users/login.phtml');
    }

    private function createSession()
    {
        $user = User::find('username', Request::$post['username']);

        // Try to find the user in the database and verify their password
        if ($user && $user->verify_password(Request::$post['password'])) {
            // User found and verified, set the cookie and redirect them
            // to the index page if no "redirect" page was set.
            if ($user->is_activated()) {
                setcookie('_traq', $user->login_hash, time() + (2 * 4 * 7 * 24 * 60 * 60 * 60), '/');

                return Request::redirect(
                    isset(Request::$post['redirect'])
                        ? Request::$post['redirect']
                        : Request::base()
                );
            } else {
                // Tell the user to activate
                $this->set('validation_required', true);
            }
        }

        // No user found
        $this->set('error', true);

        return $this->render('users/login.phtml');
    }

    /**
     * Handles the logout request.
     */
    public function logout()
    {
        setcookie('_traq', sha1(time()), time() + 5, '/');

        return $this->redirectTo('/');
    }

    /**
     * Handles the register page and account creation.
     */
    public function register(): Response
    {
        if (!settings('allow_registration')) {
            return $this->show404();
        }

        $validation_required = false;
        $this->title(l('register'));

        $user = new User;

        // Check if the form has been submitted
        if (Request::method() == 'POST') {
            return $this->createAccount();
        }

        $this->set(compact('user', 'validation_required'));

        return $this->render('users/register.phtml');
    }

    private function createAccount(): Response
    {
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
        $validation_required = false;
        if ($user->save()) {
            // Send validation email
            if (settings('email_validation')) {
                Notification::send_to(
                    $user,
                    'email_validation',
                    [
                        'link' => "http://" . $_SERVER['HTTP_HOST'] . Request::base("users/validate/" . $user->option('validation_key'))
                    ]
                );

                $validation_required = true;

                return $this->render('users/register.phtml');
            } else {
                // Redirect to login page
                return $this->redirectTo('login');
            }
        }

        $this->set(compact('user', 'validation_required'));

        return $this->render('users/register.phtml');
    }

    /**
     * Account validation.
     */
    public function validate($key)
    {
        $user = User::select()->where('options', '%"validation_key":"' . $key . '"%', 'LIKE')->exec()->fetch();

        $validated = false;
        if ($user) {
            $user->option('validation_key', null);
            $user->save();
            $validated = true;
        }

        return $this->render('users/login.phtml', ['validated' => $validated]);
    }

    /**
     * Forgot/Reset password page.
     */
    public function resetPassword(?string $key = null)
    {
        // Reset key provided?
        if ($key !== null) {
            // Find user
            if ($user = User::select()->where('options', '%"reset_password_key":"' . $key . '"%', 'LIKE')->exec()->fetch()) {
                // Generate new password
                $newPassword = substr(random_hash(), 0, 32);

                // Set new password, clear reset key and save
                $user->set_password($newPassword);
                $user->option('reset_password_key', null);
                $user->save();

                // Send data to the view
                $this->set('password_reset', true);

                // TODO: email this to the user instead
                $this->set('new_password', $newPassword);
            }
        }
        // Find user and generate key
        else {
            // Check if the form has been submitted
            if (Request::method() == 'POST') {
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
                        l(
                            'notifications.password_reset.message',    // Message
                            settings('title'), // Installation title
                            $user->name,       // Users name
                            $user->username,   // Users username
                            "http://" . $_SERVER['HTTP_HOST'] . Request::base("/login/resetpassword/{$key}"), // Reset password URL
                            $_SERVER['REMOTE_ADDR'] // IP of reset request
                        )
                    );
                    $this->set('reset_email_sent', true);
                } else {
                    $this->set('error', true);
                }
            }
        }

        return $this->render('users/reset_password.phtml');
    }

    /**
     * Redirect to the front page if the user is logged in.
     */
    public function alreadyLoggedIn(): Response|false
    {
        if (LOGGEDIN) {
            return $this->redirectTo('/');
        }

        return false;
    }
}
