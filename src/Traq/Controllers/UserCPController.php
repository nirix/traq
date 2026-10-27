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

use Avalon\Http\Request;
use Avalon\Http\Response;
use Avalon\Output\View;
use \FishHook;
use Traq\Controllers\AppController;
use Traq\Middleware\AuthMiddleware;
use Traq\Models\Subscription;

/**
 * UserCP controller
 *
 * @author Jack P.
 * @since 3.0
 * @package Traq
 * @subpackage Controllers
 */
#[AuthMiddleware(loggedIn: true)]
class UserCPController extends AppController
{
    /**
     * The index page.
     */
    public function index(): Response
    {
        // Clone the logged in user object
        $user = clone $this->user;

        // Has the form been submitted?
        if (Request::method() == 'POST') {
        }

        View::set('user', $user);

        return $this->render('usercp/index.phtml');
    }

    /**
     * Save the user info.
     */
    public function saveInfo(): Response
    {
        $user = clone $this->user;

        $data = array(
            'name'   => Request::get('name', $user->name),
            'email'  => Request::get('email', $user->email),
            'locale' => Request::get('locale', $user->locale)
        );

        FishHook::add('controller:users::usercp/save', array(&$data));

        // Set the info
        $user->set($data);
        $user->option('watch_created_tickets', Request::get('watch_created_tickets'));

        // Save the user
        if ($user->save()) {
            // Redirect if successful
            if ($this->isJson) {
                return $this->json(['user' => $user]);
            } else {
                return $this->redirectTo(Request::requestUri());
            }
        }

        return $this->render('usercp/index.phtml');
    }

    /**
     * Password page.
     */
    public function password()
    {
        // Clone the logged in user object
        $user = clone $this->user;

        View::set('user', $user);

        return $this->render('usercp/password.phtml');
    }

    public function savePassword(): Response
    {
        $user = clone $this->user;

        $data = [
            'old_password'     => Request::post('password', ''),
            'new_password'     => Request::post('new_password', ''),
            'confirm_password' => Request::post('confirm_password', '')
        ];

        FishHook::add('controller:users::usercp/password/save', array(&$data));

        // Set the info
        $user->set($data);

        if ($user->is_valid()) {
            $user->set_password($data['new_password']);

            // Save the user
            if ($user->save()) {
                setSessionCookie($user->login_hash);
                return $this->redirectTo(Request::requestUri());
            }
        }

        return $this->render('usercp/password.phtml');
    }

    /**
     * Generate the users API key.
     */
    public function createApiKey()
    {
        $this->user->generate_api_key();
        $this->user->save();

        return $this->redirectTo('usercp');
    }

    /**
     * Subscriptions page
     */
    public function subscriptions()
    {
        $subscriptions = Subscription::select()->where('user_id', $this->user->id)->exec()->fetch_all();
        View::set('subscriptions', $subscriptions);
        View::set('user', $this->user);

        return $this->render('usercp/subscriptions.phtml');
    }

    /**
     * Remove one of the current user's subscriptions.
     */
    public function unsubscribe(string $id): Response
    {
        $subscription = Subscription::select()->where([
            ['id', $id],
            ['user_id', $this->user->id],
        ])->exec()->fetch();

        if ($subscription) {
            $subscription->delete();
        }

        return $this->redirectTo('/usercp/subscriptions');
    }
}
