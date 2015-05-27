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

use Avalon\Routing\Router;
use Traq\config\routes\Admin as AdminRoutes;
use Traq\config\routes\Projects as ProjectRoutes;

Router::map(function($r) {
    Router::$extensions[] = '.js';
    Router::$extensions[] = '.txt';

    $traq = "Traq\\Controllers";

    // URL tokens
    $r->addToken('project_slug', '(?P<project_slug>[^/]+)');
    $r->addToken('slug',         '(?P<slug>[^/]+?)');
    $r->addToken('event_id',     '(?P<event_id>[\d]+)');
    $r->addToken('ticket_id',    '(?P<ticket_id>[\d]+)');
    $r->addToken('revision',     '(?P<revision>[\d]+)');

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

    $r->get('/usercp', 'usercp')->to("{$traq}\\UserCP::index");
    $r->post('/usercp')->to("{$traq}\\UserCP::save");

    $r->get('/users/{id}')->to("{$traq}\\Profile::show");

    // --------------------------------------------------
    // AdminCP
    AdminRoutes::register($r);

    // --------------------------------------------------
    // Projects
    ProjectRoutes::register($r);

    // --------------------------------------------------
    // Project Settings
    $r->get('/{project_slug}/settings', 'project_settings')->to("{$traq}\\ProjectSettings\\Options::index");
    $r->post('/{project_slug}/settings')->to("{$traq}\\ProjectSettings\\Options::save");

    // Milestones
    $r->get('/{project_slug}/settings/milestones', 'project_settings_milestones')->to("{$traq}\\ProjectSettings\\Milestones::index");
    $r->get('/{project_slug}/settings/milestones/new', 'new_project_settings_milestone')->to("{$traq}\\ProjectSettings\\Milestones::new");
    $r->post('/{project_slug}/settings/milestones/new')->to("{$traq}\\ProjectSettings\\Milestones::create");
    $r->get('/{project_slug}/settings/milestones/{id}/edit', 'edit_project_settings_milestone')->to("{$traq}\\ProjectSettings\\Milestones::edit");
    $r->post('/{project_slug}/settings/milestones/{id}/edit')->to("{$traq}\\ProjectSettings\\Milestones::save");
    $r->get('/{project_slug}/settings/milestones/{id}/delete', 'delete_project_settings_milestone')->to("{$traq}\\ProjectSettings\\Milestones::destroy");

    // Components
    $r->get('/{project_slug}/settings/components', 'project_settings_components')->to("{$traq}\\ProjectSettings\\Components::index");
    $r->get('/{project_slug}/settings/components/new', 'new_project_settings_component')->to("{$traq}\\ProjectSettings\\Components::new");
    $r->post('/{project_slug}/settings/components/new')->to("{$traq}\\ProjectSettings\\Components::create");
    $r->get('/{project_slug}/settings/components/{id}/edit', 'edit_project_settings_component')->to("{$traq}\ProjectSettings\\Components::edit");
    $r->post('/{project_slug}/settings/components/{id}/edit')->to("{$traq}\\ProjectSettings\\Components::save");
    $r->get('/{project_slug}/settings/components/{id}/delete', 'delete_project_settings_component')->to("{$traq}\\ProjectSettings\\Components::destroy");

    // Members
    $r->get('/{project_slug}/settings/members', 'project_settings_members')
        ->to("{$traq}\\ProjectSettings\\Members::index");
    $r->post('/{project_slug}/settings/members')
        ->to("{$traq}\\ProjectSettings\\Members::save");

    $r->get('/{project_slug}/settings/members/new', 'new_project_settings_member')
        ->to("{$traq}\\ProjectSettings\\Members::new");
    $r->post('/{project_slug}/settings/members/new')->to("{$traq}\\ProjectSettings\\Members::create");

    $r->get('/{project_slug}/settings/members/{id}/delete', 'delete_project_settings_member')
        ->to("{$traq}\\ProjectSettings\\Members::destroy");

    // Custom Fields
    $r->get('/{project_slug}/settings/custom-fields', 'project_settings_custom_fields')->to("{$traq}\\ProjectSettings\\CustomFields::index");

    // Permissions
    $r->get('/{project_slug}/settings/permissions', 'project_settings_permissions')->to("{$traq}\\ProjectSettings\\Permissions::index");
});
