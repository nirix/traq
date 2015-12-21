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

use Avalon\Http\Request;
use Traq\Models\User;
use Avalon\Http\RedirectResponse;

/**
 * User sessions controller.
 *
 * @author Jack P.
 * @package Traq\Controllers
 * @since 4.0.0
 */
class Sessions extends AppController
{
    /**
     * Login form
     */
    public function newAction()
    {
        return $this->render('sessions/new.phtml');
    }

    /**
     * Create session
     */
    public function createAction()
    {
        $user = User::find('username', Request::$post->get('username'));
        if ($user && $user->authenticate(Request::$post->get('password'))) {
            // Check account activation
            if (setting('email_validation') && !$user->isActivated()) {
                return $this->render("sessions/new.phtml", ['activationRequired' => true]);
            }

            $response = new RedirectResponse(routeUrl('root'));
            $response->addCookie('traq', $user->login_hash, time() + (2 * 4 * 7 * 24 * 60 * 60 * 60), '/');

            return $response;
        } else {
            return $this->render('sessions/new.phtml', ['error' => true]);
        }
    }

    /**
     * Destroy session
     */
    public function destroyAction()
    {
        $response = new RedirectResponse(routeUrl('root'));
        $response->addCookie('traq', '', time(), '/');
        return $response;
    }
}
