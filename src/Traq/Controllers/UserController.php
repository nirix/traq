<?php
/*!
 * Traq
 * Copyright (C) 2009-2026 Jack Polgar
 * Copyright (C) 2012-2026 Traq.io
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
    /**
     * Handles the login page.
     */
    public function login(): Response
    {
        if (Request::attribute('loggedin')) {
            return $this->redirectTo('/');
        }

        // Set the title
        $this->title(l('login'));

        return $this->render('users/login.phtml');
    }

    public function createSession(): Response
    {
        if (Request::attribute('loggedin')) {
            return $this->redirectTo('/');
        }

        $user = User::find('username', Request::$post['username']);

        // Try to find the user in the database and verify their password
        if ($user && $user->verify_password(Request::$post['password'])) {
            // User found and verified, set the cookie and redirect them
            // to the index page if no "redirect" page was set.
            if ($user->is_activated()) {
                $user->rotateLoginHash();
                $user->save();
                setSessionCookie($user->login_hash);

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
        if (LOGGEDIN) {
            $this->user->rotateLoginHash();
            $this->user->save();
        }

        clearSessionCookie();

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

        if (Request::attribute('loggedin')) {
            return $this->redirectTo('/');
        }

        $validation_required = false;
        $this->title(l('register'));

        $user = new User;

        $this->set(compact('user', 'validation_required'));

        return $this->render('users/register.phtml');
    }

    /**
     * Handles the account creation.
     */
    public function createAccount(): Response
    {
        if (Request::attribute('loggedin')) {
            return $this->redirectTo('/');
        }

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
                        'link' => absolute_url("users/validate/" . $user->option('validation_key'))
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
     *
     * Without a key it asks for the username and emails a reset link. The link only shows
     * a form, the password is only changed when that form is submitted (POST), so mail
     * scanners and link previews can't consume the reset.
     */
    public function resetPassword(?string $key = null)
    {
        if ($key !== null) {
            return $this->resetPasswordWithKey($key);
        }

        if (Request::method() == 'POST') {
        }

        return $this->render('users/reset_password.phtml');
    }

    public function sendResetPasswordEmail(): Response
    {
        $user = User::find('username', (string) Request::post('username', ''));

        if ($user) {
            // Only a hash of the key is stored, the key itself is only in the email
            $key = random_hash();
            $user->option('reset_password_key', hash('sha256', $key));
            $user->option('reset_password_expires', time() + 3600);
            $user->save();

            Notification::send(
                $user,
                l('notifications.password_reset.subject'),
                l(
                    'notifications.password_reset.message',
                    settings('title'),
                    $user->name,
                    $user->username,
                    absolute_url("login/resetpassword/{$key}"),
                    $_SERVER['REMOTE_ADDR']
                )
            );
        }

        // Same response whether or not the account exists
        $this->set('reset_email_sent', true);

        return $this->render('users/reset_password.phtml');
    }

    private function resetPasswordWithKey(string $key)
    {
        $user = User::select()
            ->where('options', '%"reset_password_key":"' . hash('sha256', $key) . '"%', 'LIKE')
            ->exec()
            ->fetch();

        if (!$user || (int) $user->option('reset_password_expires') < time()) {
            $this->set('invalid_key', true);

            return $this->render('users/reset_password.phtml');
        }

        $this->set('reset_key', $key);

        return $this->render('users/reset_password.phtml');
    }

    public function resetPasswordSave(string $key): Response
    {
        $user = User::select()
            ->where('options', '%"reset_password_key":"' . hash('sha256', $key) . '"%', 'LIKE')
            ->exec()
            ->fetch();

        $password = Request::post('new_password', '');
        $confirm = Request::post('confirm_password', '');

        if (!is_string($password) || $password === '') {
            $this->set('password_error', l('errors.users.new_password_blank'));
        } elseif ($password !== $confirm) {
            $this->set('password_error', l('errors.users.invalid_confirm_password'));
        } else {
            // Single use, also signs out any existing sessions
            $user->set_password($password);
            $user->option('reset_password_key', null);
            $user->option('reset_password_expires', null);
            $user->save();

            $this->set('password_reset', true);
        }

        $this->set('reset_key', $key);
        return $this->render('users/reset_password.phtml');
    }
}
