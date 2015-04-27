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

Router::map(function($r) {
    Router::$extensions[] = '.js';
    Router::$extensions[] = '.txt';

    $traq = "Traq\Controllers";

    // URL tokens
    $r->addToken('project_slug', '(?P<project_slug>[^/]+)');
    $r->addToken('slug',         '(?P<slug>[^/]+?)');
    $r->addToken('event_id',     '(?P<event_id>[\d]+)');
    $r->addToken('ticket_id',    '(?P<ticket_id>[\d]+)');
    $r->addToken('revision',     '(?P<revision>[\d]+)');

    $r->root("{$traq}\Projects::index");
    $r->get('/admin', 'admin')->to("{$traq}\Admin\Dashboard::index");

    $r->route('404')->to("{$traq}\Errors::notFound");
    $r->get('/_js', 'internal_js')->to("{$traq}\Misc::javascript");

    // --------------------------------------------------
    // Users
    $r->get('/login', 'login')->to("{$traq}\Sessions::new");
    $r->post('/login')->to("{$traq}\Sessions::create");
    $r->get('/logout', 'logout')->to("{$traq}\Sessions::destroy");

    $r->get('/register', 'register')->to("{$traq}\Users::new");
    $r->post('/register')->to("{$traq}\Users::create");

    $r->get('/usercp', 'usercp')->to("{$traq}\UserCP::index");
    $r->post('/usercp')->to("{$traq}\UserCP::save");

    $r->get('/users/{id}')->to("{$traq}\Profile::show");

    // --------------------------------------------------
    // Projects
    $r->get("/projects", 'projects')->to("{$traq}\Projects::index");
    $r->get('/{project_slug}', 'show_project')->to("{$traq}\Projects::show");

    // --------------------------------------------------
    // Timeline
    $r->route('/{project_slug}/timeline', 'timeline')->to("{$traq}\Timeline::index")
        ->method(['get','post']);
    $r->get('/{project_slug}/timeline/{event_id}/delete', 'delete_timeline_event')
        ->to("{$traq}\Timeline::deleteEvent");

    // --------------------------------------------------
    // Roadmap
    $r->get('/{project_slug}/roadmap', 'roadmap')
        ->to("{$traq}\Roadmap::index");

    $r->get('/{project_slug}/roadmap/all', 'roadmap_all')
        ->to("{$traq}\Roadmap::index", ['filter' => 'all']);

    $r->get('/{project_slug}/roadmap/completed', 'roadmap_completed')
        ->to("{$traq}\Roadmap::index", ['filter' => 'completed']);

    $r->get('/{project_slug}/roadmap/cancelled', 'roadmap_cancelled')
        ->to("{$traq}\Roadmap::index", ['filter' => 'cancelled']);

    $r->get('/{project_slug}/milestone/{slug}', 'show_milestone')
        ->to("{$traq}\Roadmap::show");

    // --------------------------------------------------
    // Issues
    $r->get('/{project_slug}/tickets')->to("{$traq}\TicketListing::index");
    $r->get('/{project_slug}/tickets/{ticket_id}')->to("{$traq}\Tickets::show");

    $r->get('/{project_slug}/issues', 'issues')
        ->to("{$traq}\TicketListing::index");

    $r->get('/{project_slug}/issues/{ticket_id}', 'show_issue')
        ->to("{$traq}\Tickets::show");

    $r->get('/{project_slug}/issues/new', 'new_issue')->to("{$traq}\Tickets::new");
    $r->post('/{project_slug}/issues/new')->to("{$traq}\Tickets::create");

    // --------------------------------------------------
    // Issue filters
    $r->post('/{project_slug}/issues/set-columns', 'set_issue_filter_columns')
        ->to("{$traq}\TicketListing::setColumns");

    $r->post('/{project_slug}/issues/update-filters', 'update_issue_filters')
        ->to("{$traq}\TicketListing::updateFilters");

    // --------------------------------------------------
    // Wiki
    $r->get('/{project_slug}/wiki', 'wiki')
        ->to("{$traq}\Wiki::show", ['slug' => 'main']);

    // New page
    $r->get('/{project_slug}/wiki/_new', 'new_wiki_page')
        ->to("{$traq}\Wiki::new");

    $r->post('/{project_slug}/wiki/_new')->to("{$traq}\Wiki::create");

    // Pages listing
    $r->get('/{project_slug}/wiki/_pages', 'wiki_pages')
        ->to("{$traq}\Wiki::pages");

    // Show page
    $r->get('/{project_slug}/wiki/{slug}', 'show_wiki_page')
        ->to("{$traq}\Wiki::show");

    // Edit page
    $r->get('/{project_slug}/wiki/{slug}/_edit', 'edit_wiki_page')
        ->to("{$traq}\Wiki::edit");

    $r->post('/{project_slug}/wiki/{slug}/_edit')->to("{$traq}\Wiki::save");

    // List revisions
    $r->get('/{project_slug}/wiki/{slug}/_revisions', 'wiki_page_revisions')
        ->to("{$traq}\Wiki::revisions");

    // Show revision
    $r->get('/{project_slug}/wiki/{slug}/_revisions/{revision}', 'show_wiki_page_revision')
        ->to("{$traq}\Wiki::revision");

    // --------------------------------------------------
    // Changelog
    $r->get('/{project_slug}/changelog', 'changelog')->to("{$traq}\Projects::changelog");

    // --------------------------------------------------
    // Project Settings
    $r->get('/{project_slug}/settings', 'project_settings')->to("{$traq}\ProjectSettings\Options::index");
    $r->post('/{project_slug}/settings')->to("{$traq}\ProjectSettings\Options::save");

    // Milestones
    $r->get('/{project_slug}/settings/milestones', 'project_settings_milestones')->to("{$traq}\ProjectSettings\Milestones::index");
    $r->get('/{project_slug}/settings/milestones/new', 'new_project_settings_milestone')->to("{$traq}\ProjectSettings\Milestones::new");
    $r->post('/{project_slug}/settings/milestones/new')->to("{$traq}\ProjectSettings\Milestones::create");
    $r->get('/{project_slug}/settings/milestones/{id}/edit', 'edit_project_settings_milestone')->to("{$traq}\ProjectSettings\Milestones::edit");
    $r->post('/{project_slug}/settings/milestones/{id}/edit')->to("{$traq}\ProjectSettings\Milestones::save");
    $r->get('/{project_slug}/settings/milestones/{id}/delete', 'delete_project_settings_milestone')->to("{$traq}\ProjectSettings\Milestones::destroy");

    // Components
    $r->get('/{project_slug}/settings/components', 'project_settings_components')->to("{$traq}\ProjectSettings\Components::index");
    $r->get('/{project_slug}/settings/components/new', 'new_project_settings_component')->to("{$traq}\ProjectSettings\Components::new");
    $r->post('/{project_slug}/settings/components/new')->to("{$traq}\ProjectSettings\Components::create");
    $r->get('/{project_slug}/settings/components/{id}/edit', 'edit_project_settings_component')->to("{$traq}\ProjectSettings\Components::edit");
    $r->post('/{project_slug}/settings/components/{id}/edit')->to("{$traq}\ProjectSettings\Components::save");
    $r->get('/{project_slug}/settings/components/{id}/delete', 'delete_project_settings_component')->to("{$traq}\ProjectSettings\Components::destroy");

    // Members
    $r->get('/{project_slug}/settings/members', 'project_settings_members')
        ->to("{$traq}\ProjectSettings\Members::index");

    $r->post('/{project_slug}/settings/members/new')->to("{$traq}\ProjectSettings\Members::create");


    // Custom Fields
    $r->get('/{project_slug}/settings/custom-fields', 'project_settings_custom_fields')->to("{$traq}\ProjectSettings\CustomFields::index");

    // Permissions
    $r->get('/{project_slug}/settings/permissions', 'project_settings_permissions')->to("{$traq}\ProjectSettings\Permissions::index");

    // --------------------------------------------------
    // AdminCP

    // Settings
    $r->get('/admin/settings', 'admin_settings')->to("{$traq}\Admin\Settings::index");

    // Projects
    $r->get('/admin/projects', 'admin_projects')->to("{$traq}\Admin\Projects::index");
    $r->get('/admin/projects/new', 'new_admin_project')->to("{$traq}\Admin\Projects::new");
    $r->post('/admin/projects/new')->to("{$traq}\Admin\Projects::create");
    $r->get('/admin/projects/{id}/edit', 'edit_admin_project')->to("{$traq}\Admin\Projects::edit");
    $r->post('/admin/projects/{id}/edit')->to("{$traq}\Admin\Projects::save");
    $r->get('/admin/projects/{id}/delete', 'delete_admin_project')->to("{$traq}\Admin\Projects::destroy");

    // Users
    $r->get('/admin/users', 'admin_users')->to("{$traq}\Admin\Users::index");
    $r->get('/admin/users/new', 'new_admin_user')->to("{$traq}\Admin\Users::new");
    $r->post('/admin/users/new')->to("{$traq}\Admin\Users::create");
    $r->get('/admin/users/{id}/edit', 'edit_admin_user')->to("{$traq}\Admin\Users::edit");
    $r->post('/admin/users/{id}/edit')->to("{$traq}\Admin\Users::save");
    $r->get('/admin/users/{id}/delete', 'delete_admin_user')->to("{$traq}\Admin\Users::destroy");

    // Plugins
    $r->get('/admin/plugins', 'admin_plugins')->to("{$traq}\Admin\Plugins::index");
    $r->get('/admin/plugins/install')->to("{$traq}\Admin\Plugins::install");
    $r->get('/admin/plugins/uninstall')->to("{$traq}\Admin\Plugins::uninstall");
    $r->get('/admin/plugins/enable')->to("{$traq}\Admin\Plugins::enable");
    $r->get('/admin/plugins/disable')->to("{$traq}\Admin\Plugins::disable");

    // Statuses
    $r->get('/admin/statuses', 'admin_statuses')->to("{$traq}\Admin\Statuses::index");
    $r->get('/admin/statuses/new', 'new_admin_status')->to("{$traq}\Admin\Statuses::new");
    $r->post('/admin/statuses/new')->to("{$traq}\Admin\Statuses::create");
    $r->get('/admin/statuses/{id}/edit', 'edit_admin_status')->to("{$traq}\Admin\Statuses::edit");
    $r->post('/admin/statuses/{id}/edit')->to("{$traq}\Admin\Statuses::save");
    $r->get('/admin/statuses/{id}/delete', 'delete_admin_status')->to("{$traq}\Admin\Statuses::destroy");

    // Priorities
    $r->get('/admin/priorities', 'admin_priorities')->to("{$traq}\Admin\Priorities::index");
    $r->get('/admin/priorities/new', 'new_admin_priority')->to("{$traq}\Admin\Priorities::new");
    $r->post('/admin/priorities/new')->to("{$traq}\Admin\Priorities::create");
    $r->get('/admin/priorities/{id}/edit', 'edit_admin_priority')->to("{$traq}\Admin\Priorities::edit");
    $r->post('/admin/priorities/{id}/edit')->to("{$traq}\Admin\Priorities::save");
    $r->get('/admin/priorities/{id}/delete', 'delete_admin_priority')->to("{$traq}\Admin\Priorities::destroy");

    // Severities
    $r->get('/admin/severities', 'admin_severities')->to("{$traq}\Admin\Severities::index");
    $r->get('/admin/severities/new', 'new_admin_severity')->to("{$traq}\Admin\Severities::new");
    $r->post('/admin/severities/new')->to("{$traq}\Admin\Severities::create");
    $r->get('/admin/severities/{id}/edit', 'edit_admin_severity')->to("{$traq}\Admin\Severities::edit");
    $r->post('/admin/severities/{id}/edit')->to("{$traq}\Admin\Severities::save");
    $r->get('/admin/severities/{id}/delete', 'delete_admin_severity')->to("{$traq}\Admin\Severities::destroy");

    // Types
    $r->get('/admin/types', 'admin_types')->to("{$traq}\Admin\Types::index");
    $r->get('/admin/types/new', 'new_admin_type')->to("{$traq}\Admin\Types::new");
    $r->post('/admin/types/new')->to("{$traq}\Admin\Types::create");
    $r->get('/admin/types/{id}/edit', 'edit_admin_type')->to("{$traq}\Admin\Types::edit");
    $r->post('/admin/types/{id}/edit')->to("{$traq}\Admin\Types::save");
    $r->get('/admin/types/{id}/delete', 'delete_admin_type')->to("{$traq}\Admin\Types::destroy");

    // Project Roles
    $r->get('/admin/roles', 'admin_project_roles')->to("{$traq}\Admin\ProjectRoles::index");
    $r->get('/admin/roles/new', 'new_admin_project_role')->to("{$traq}\Admin\ProjectRoles::new");
    $r->post('/admin/roles/new')->to("{$traq}\Admin\ProjectRoles::create");
    $r->get('/admin/roles/{id}/edit', 'edit_admin_project_role')->to("{$traq}\Admin\ProjectRoles::edit");
    $r->post('/admin/roles/{id}/edit')->to("{$traq}\Admin\ProjectRoles::save");
    $r->get('/admin/roles/{id}/delete', 'delete_admin_project_role')->to("{$traq}\Admin\ProjectRoles::destroy");

    // Groups
    $r->get('/admin/groups', 'admin_groups')->to("{$traq}\Admin\Groups::index");
    $r->get('/admin/groups/new', 'new_admin_group')->to("{$traq}\Admin\Groups::new");
    $r->post('/admin/groups/new')->to("{$traq}\Admin\Groups::create");
    $r->get('/admin/groups/{id}/edit', 'edit_admin_group')->to("{$traq}\Admin\Groups::edit");
    $r->post('/admin/groups/{id}/edit')->to("{$traq}\Admin\Groups::save");
    $r->get('/admin/groups/{id}/delete', 'delete_admin_group')->to("{$traq}\Admin\Groups::destroy");
});
