<?php
/*!
 * Traq
 * Copyright (C) 2009-2015 Jack Polgar
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

/**
 * User sessions controller.
 *
 * @author Jack P.
 * @package Traq\Controllers
 * @since 4.0.0
 */
class Sessions extends AppController
{
    public function __construct()
    {
        parent::__construct();

        $this->before(['new', 'create'], function () {
            if ($this->currentUser) {
                $this->redirectTo('root');
            }
        });
    }

    /**
     * Login form
     */
    public function newAction()
    {
        return $this->respondTo(function ($format) {
            if ($format == 'html') {
                if ($this->isOverlay) {
                    return $this->render('sessions/new.overlay.phtml');
                } else {
                    return $this->render('sessions/new.phtml');
                }
            }
        });
    }

    /**
     * Create session
     */
    public function createAction()
    {
        $activationRequired = false;

        if ($user = User::find('username', Request::$post['username'])
        and $user->authenticate(Request::$post['password'])) {
            if ($user->isActivated()) {
                setcookie('traq', $user->login_hash, time() + (2 * 4 * 7 * 24 * 60 * 60 * 60), '/');
                return $this->redirect(
                    Request::post('redirect', $this->generateUrl('root'))
                );
            } else {
                $activationRequired = true;
            }
        }

        return $this->render('sessions/new.phtml', [
            'activationRequired' => $activationRequired
        ]);
    }

    /**
     * Destroy session
     */
    public function destroyAction()
    {
        setcookie('traq', sha1(time()), time() + 5, '/');
        return $this->redirectTo('root');
    }
}
