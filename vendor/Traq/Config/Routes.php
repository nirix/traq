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

use Radium\Http\Router;

Router::map(function($r){
    $traq = "Traq\Controllers";

    // URL tokens
    $r->registerToken('project_slug', '(?P<project_slug>[a-zA-Z0-9\-\_]+)');

    $r->root("{$traq}\Projects::index");
    $r->route('404')->to("{$traq}\Errors::notFound");

    // Users
    $r->get('/login')->to("{$traq}\Sessions::new");
    $r->post('/login')->to("{$traq}\Sessions::create");
    $r->get('/logout')->to("{$traq}\Sessions::destroy");

    // Projects
    $r->get("/projects")->to("{$traq}\Projects::index");
    $r->get('/:project_slug')->to("{$traq}\Projects::show");

    // Tickets
    $r->get('/:project_slug/tickets')->to("{$traq}\Tickets::index");
});
