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

/**
 * Session controller.
 *
 * @package Traq\Controllers
 * @author Jack P.
 * @since 3.0.0
 */
class Sessions extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->addCrumb($this->translate('login'), $this->generateUrl('login'));
    }

    /**
     * Login form.
     */
    public function newAction()
    {
        return $this->render('sessions/new.phtml');
    }

    /**
     * Create session.
     */
    public function createAction()
    {
        $user = User::find('username', Request::$post->get('username'));

        if ($user && $user->authenticate(Request::$post->get('password'))) {
            return $this->redirectTo('root')
                ->addCookie('traq', $user['session_hash']);
        } else {
            return $this->render('sessions/new.phtml', ['error' => true]);
        }
    }
}
