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
    $r->registerToken('slug', '(?P<slug>[a-zA-Z0-9\-\_]+)');

    $r->root("{$traq}\Projects::index");
    $r->get('/admin')->to("{$traq}\Admin\Dashboard::index");
    $r->route('404')->to("{$traq}\Errors::notFound");

    // --------------------------------------------------
    // Users
    $r->get('/login')->to("{$traq}\Sessions::new");
    $r->post('/login')->to("{$traq}\Sessions::create");
    $r->get('/logout')->to("{$traq}\Sessions::destroy");

    // --------------------------------------------------
    // Projects
    $r->get("/projects")->to("{$traq}\Projects::index");
    $r->get('/:project_slug')->to("{$traq}\Projects::show");

    // Timeline
    $r->route('/:project_slug/timeline')->to("{$traq}\Projects::timeline")
        ->method(array('get','post'));

    // Roadmap
    $r->get('/:project_slug/roadmap')->to("{$traq}\Roadmap::index");
    $r->get('/:project_slug/roadmap/all')->to("{$traq}\Roadmap::index", array('all'));
    $r->get('/:project_slug/roadmap/completed')->to("{$traq}\Roadmap::index", array('completed'));
    $r->get('/:project_slug/roadmap/cancelled')->to("{$traq}\Roadmap::index", array('cancelled'));
    $r->get('/:project_slug/roadmap/:slug')->to("{$traq}\Roadmap::show");

    // Tickets
    $r->get('/:project_slug/tickets')->to("{$traq}\Tickets::index");

    // --------------------------------------------------
    // AdminCP

    // Plugins
    $r->get('/admin/plugins')->to("{$traq}\Admin\Plugins::index");
    $r->get('/admin/plugins/:slug/install')->to("{$traq}\Admin\Plugins::install", array('slug'));
    $r->get('/admin/plugins/:slug/uninstall')->to("{$traq}\Admin\Plugins::uninstall", array('slug'));
    $r->get('/admin/plugins/:slug/enable')->to("{$traq}\Admin\Plugins::enable", array('slug'));
    $r->get('/admin/plugins/:slug/disable')->to("{$traq}\Admin\Plugins::disable", array('slug'));
});
