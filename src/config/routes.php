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

use Radium\Routing\Router;

Router::map(function($r){
    Router::$extensions[] = '.js';

    $traq = "Traq\Controllers";

    // URL tokens
    $r->addToken('project_slug', '(?P<project_slug>[^/]+)');
    $r->addToken('project_id',   '(?P<project_id>[\d]+)');
    $r->addToken('slug',         '(?P<slug>[^/]+?)');
    $r->addToken('wiki_slug',    '(?P<slug>[^/]+?)');
    $r->addToken('event_id',     '(?P<event_id>[\d]+)');
    $r->addToken('ticket_id',    '(?P<ticket_id>[\d]+)');
    $r->addToken('revision',     '(?P<revision>[\d]+)');

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
    $r->route('/:project_slug/timeline')->to("{$traq}\Timeline::index")
        ->method(['get','post']);
    $r->get('/:project_slug/timeline/(?P<event_id>[\d]+)/delete')->to("{$traq}\Timeline::deleteEvent");

    // Roadmap
    $r->get('/:project_slug/roadmap')->to("{$traq}\Roadmap::index");
    $r->get('/:project_slug/roadmap/all')->to("{$traq}\Roadmap::index", ['filter' => 'all']);
    $r->get('/:project_slug/roadmap/completed')->to("{$traq}\Roadmap::index", ['filter' => 'completed']);
    $r->get('/:project_slug/roadmap/cancelled')->to("{$traq}\Roadmap::index", ['filter' => 'cancelled']);
    $r->get('/:project_slug/milestone/:slug')->to("{$traq}\Roadmap::show");

    // Issues
    $r->get('/:project_slug/tickets')->to("{$traq}\TicketListing::index");
    $r->get('/:project_slug/tickets/(?P<ticket_id>[\d]+)')->to("{$traq}\Tickets::show");

    $r->get('/:project_slug/issues')->to("{$traq}\TicketListing::index");
    $r->get('/:project_slug/issues(?P<ticket_id>[\d]+)')->to("{$traq}\Tickets::show");
    $r->post('/:project_slug/issues/set-columns')->to("{$traq}\TicketListing::setColumns");
    $r->post('/:project_slug/issues/update-filters')->to("{$traq}\TicketListing::updateFilters");

    $r->get('/:project_slug/issues/new')->to("{$traq}\Tickets::new");
    $r->post('/:project_slug/issues/new')->to("{$traq}\Tickets::create");

    // Wiki
    $r->get('/:project_slug/wiki')->to("{$traq}\Wiki::show", ['slug' => 'main']);
    $r->get('/:project_slug/wiki/_new')->to("{$traq}\Wiki::new");
    $r->post('/:project_slug/wiki/_new')->to("{$traq}\Wiki::create");
    $r->get('/:project_slug/wiki/_pages')->to("{$traq}\Wiki::pages");
    $r->get('/:project_slug/wiki/:wiki_slug')->to("{$traq}\Wiki::show");
    $r->get('/:project_slug/wiki/:wiki_slug/_edit')->to("{$traq}\Wiki::edit");
    $r->post('/:project_slug/wiki/:wiki_slug/_edit')->to("{$traq}\Wiki::save");
    $r->get('/:project_slug/wiki/:wiki_slug/_revisions')->to("{$traq}\Wiki::revisions");
    $r->get('/:project_slug/wiki/:wiki_slug/_revisions/(?<revision>[\d]+)')->to("{$traq}\Wiki::revision");

    // Changelog
    $r->get('/:project_slug/changelog')->to("{$traq}\Projects::changelog");

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

    // Statuses
    $r->get('/admin/statuses')->to("{$traq}\Admin\Statuses::index");
    $r->get('/admin/statuses/new')->to("{$traq}\Admin\Statuses::new");
    $r->post('/admin/statuses/new')->to("{$traq}\Admin\Statuses::create");
    $r->get('/admin/statuses/:id/edit')->to("{$traq}\Admin\Statuses::edit");
    $r->post('/admin/statuses/:id/edit')->to("{$traq}\Admin\Statuses::save");
    $r->get('/admin/statuses/:id/delete')->to("{$traq}\Admin\Statuses::destroy");

    // Priorities
    $r->get('/admin/priorities')->to("{$traq}\Admin\Priorities::index");
    $r->get('/admin/priorities/new')->to("{$traq}\Admin\Priorities::new");
    $r->post('/admin/priorities/new')->to("{$traq}\Admin\Priorities::create");
    $r->get('/admin/priorities/:id/edit')->to("{$traq}\Admin\Priorities::edit");
    $r->post('/admin/priorities/:id/edit')->to("{$traq}\Admin\Priorities::save");
    $r->get('/admin/priorities/:id/delete')->to("{$traq}\Admin\Priorities::destroy");

    // Severities
    $r->get('/admin/severities')->to("{$traq}\Admin\Severities::index");
    $r->get('/admin/severities/new')->to("{$traq}\Admin\Severities::new");
    $r->post('/admin/severities/new')->to("{$traq}\Admin\Severities::create");
    $r->get('/admin/severities/:id/edit')->to("{$traq}\Admin\Severities::edit");
    $r->post('/admin/severities/:id/edit')->to("{$traq}\Admin\Severities::save");
});
