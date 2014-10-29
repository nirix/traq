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
    $r->registerToken('project_id', '(?P<project_id>[\d]+)');
    $r->registerToken('slug', '(?P<slug>[a-zA-Z0-9\-\_\.]+)');
    $r->registerToken('wiki_slug', '(?P<slug>[\w\d\-_]+)');

    $r->root("{$traq}\Projects::index");
    $r->get('/admin')->to("{$traq}\Admin\Dashboard::index");

    $r->route('404')->to("{$traq}\Errors::notFound");
    $r->get('/_js')->to("{$traq}\Misc::javascript");

    // --------------------------------------------------
    // Users
    $r->get('/login')->to("{$traq}\Sessions::new");
    $r->post('/login')->to("{$traq}\Sessions::create");
    $r->get('/logout')->to("{$traq}\Sessions::destroy");

    $r->get('/register')->to("{$traq}\Users::new");
    $r->post('/register')->to("{$traq}\Users::create");

    $r->get('/usercp')->to("{$traq}\UserCP::index");
    $r->post('/usercp')->to("{$traq}\UserCP::save");

    // --------------------------------------------------
    // Projects
    $r->get("/projects")->to("{$traq}\Projects::index");
    $r->get('/:project_slug')->to("{$traq}\Projects::show");

    // Timeline
    $r->route('/:project_slug/timeline')->to("{$traq}\Projects::timeline")
        ->method(['get','post']);

    // Roadmap
    $r->get('/:project_slug/roadmap')->to("{$traq}\Roadmap::index");
    $r->get('/:project_slug/roadmap/all')->to("{$traq}\Roadmap::index", ['filter' => 'all']);
    $r->get('/:project_slug/roadmap/completed')->to("{$traq}\Roadmap::index", ['filter' => 'completed']);
    $r->get('/:project_slug/roadmap/cancelled')->to("{$traq}\Roadmap::index", ['filter' => 'cancelled']);
    $r->get('/:project_slug/milestone/:slug')->to("{$traq}\Roadmap::show");

    // Tickets
    $r->get('/:project_slug/tickets')->to("{$traq}\Tickets::index");

    // Wiki
    $r->get('/:project_slug/wiki')->to("{$traq}\Wiki::show", ['slug' => 'main']);
    $r->get('/:project_slug/wiki/_pages')->to("{$traq}\Wiki::pages");
    $r->get('/:project_slug/wiki/:wiki_slug')->to("{$traq}\Wiki::show");
    $r->get('/:project_slug/wiki/:wiki_slug/_revisions')->to("{$traq}\Wiki::revisions");

    // --------------------------------------------------
    // AdminCP

    // Projects
    $r->get('/admin/projects')->to("{$traq}\Admin\Projects::index");
    $r->get('/admin/projects/new')->to("{$traq}\Admin\Projects::new");
    $r->get('/admin/projects/:project_id/edit')->to("{$traq}\Admin\Projects::edit");
    $r->get('/admin/projects/:project_id/delete')->to("{$traq}\Admin\Projects::delete");

    $r->post('/admin/projects/new')->to("{$traq}\Admin\Projects::create");
    $r->post('/admin/projects/:project_id/edit')->to("{$traq}\Admin\Projects::save");

    // Plugins
    $r->get('/admin/plugins')->to("{$traq}\Admin\Plugins::index");
    $r->get('/admin/plugins/install')->to("{$traq}\Admin\Plugins::install");
    $r->get('/admin/plugins/uninstall')->to("{$traq}\Admin\Plugins::uninstall");
    $r->get('/admin/plugins/enable')->to("{$traq}\Admin\Plugins::enable");
    $r->get('/admin/plugins/disable')->to("{$traq}\Admin\Plugins::disable");
});
