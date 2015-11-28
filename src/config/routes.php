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

use Avalon\Routing\Router;
use Traq\config\routes\Admin as AdminRoutes;
use Traq\config\routes\Projects as ProjectRoutes;
use Traq\config\routes\ProjectSettings as ProjectSettingRoutes;

Router::map(function ($r) {
    Router::$extensions[] = '.js';
    Router::$extensions[] = '.txt';

    $traq = "Traq\\Controllers";

    // URL tokens
    $r->addToken('project_slug', '(?P<project_slug>[^/]+)');
    $r->addToken('slug', '(?P<slug>[^/]+?)');
    $r->addToken('event_id', '(?P<event_id>[\d]+)');
    $r->addToken('ticket_id', '(?P<ticket_id>[\d]+)');
    $r->addToken('revision', '(?P<revision>[\d]+)');

    $r->root("{$traq}\\Projects::index");

    $r->route('404')->to("{$traq}\\Errors::notFound");
    $r->get('/_js', 'internal_js')->to("{$traq}\\Misc::javascript");

    // --------------------------------------------------
    // Users
    $r->get('/login', 'login')->to("{$traq}\\Sessions::new");
    $r->post('/login')->to("{$traq}\\Sessions::create");
    $r->get('/logout', 'logout')->to("{$traq}\\Sessions::destroy");

    $r->get('/register', 'register')->to("{$traq}\\Users::new");
    $r->post('/register')->to("{$traq}\\Users::create");

    $r->get('/users/{id}')->to("{$traq}\\Profile::show");

    // UserCP
    $r->get('/usercp', 'usercp')->to("{$traq}\\UserCP::index");
    $r->post('/usercp')->to("{$traq}\\UserCP::save");
    $r->get('/usercp/create_api_key', 'usercp_create_api_key')->to("{$traq}\\UserCP::createApiKey");

    $r->get('/usercp/password', 'usercp_password')->to("{$traq}\\UserCP::password");
    $r->post('/usercp/password')->to("{$traq}\\UserCP::savePassword");

    $r->get('/usercp/subscriptions', 'usercp_subscriptions')->to("{$traq}\\UserCP::subscriptions");

    // Account activation
    $r->get('/users/activate/{activation_code}', 'account_activation')->to("{$traq}\\Users::activate");

    // --------------------------------------------------
    // AdminCP
    AdminRoutes::register($r);

    // --------------------------------------------------
    // Projects
    ProjectRoutes::register($r);

    // --------------------------------------------------
    // Project Settings
    ProjectSettingRoutes::register($r);
});
