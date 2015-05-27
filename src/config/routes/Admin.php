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

namespace Traq\config\routes;

use Avalon\Routing\Router;

/**
 * Admin routes.
 *
 * @author Jack Polgar <jack@polgar.id.au>
 * @since 4.0.0
 */
class Admin
{
    public static function register(Router $r)
    {
        $traq = "Traq\\Controllers";

        // Dashboard
        $r->get('/admin', 'admin')->to("{$traq}\\Admin\\Dashboard::index");

        // Settings
        $r->get('/admin/settings', 'admin_settings')->to("{$traq}\\Admin\\Settings::index");
        $r->post('/admin/settings')->to("{$traq}\\Admin\\Settings::save");

        // Projects
        $r->get('/admin/projects', 'admin_projects')->to("{$traq}\\Admin\\Projects::index");
        $r->get('/admin/projects/new', 'new_admin_project')->to("{$traq}\\Admin\\Projects::new");
        $r->post('/admin/projects/new')->to("{$traq}\\Admin\\Projects::create");
        $r->get('/admin/projects/{id}/edit', 'edit_admin_project')->to("{$traq}\\Admin\\Projects::edit");
        $r->post('/admin/projects/{id}/edit')->to("{$traq}\\Admin\\Projects::save");
        $r->get('/admin/projects/{id}/delete', 'delete_admin_project')->to("{$traq}\\Admin\\Projects::destroy");

        // Users
        $r->get('/admin/users', 'admin_users')->to("{$traq}\\Admin\\Users::index");
        $r->get('/admin/users/new', 'new_admin_user')->to("{$traq}\\Admin\\Users::new");
        $r->post('/admin/users/new')->to("{$traq}\\Admin\\Users::create");
        $r->get('/admin/users/{id}/edit', 'edit_admin_user')->to("{$traq}\\Admin\\Users::edit");
        $r->post('/admin/users/{id}/edit')->to("{$traq}\\Admin\\Users::save");
        $r->get('/admin/users/{id}/delete', 'delete_admin_user')->to("{$traq}\\Admin\\Users::destroy");

        // Plugins
        $r->get('/admin/plugins', 'admin_plugins')->to("{$traq}\\Admin\\Plugins::index");
        $r->get('/admin/plugins/install')->to("{$traq}\\Admin\\Plugins::install");
        $r->get('/admin/plugins/uninstall')->to("{$traq}\\Admin\\Plugins::uninstall");
        $r->get('/admin/plugins/enable')->to("{$traq}\\Admin\\Plugins::enable");
        $r->get('/admin/plugins/disable')->to("{$traq}\\Admin\\Plugins::disable");

        // Statuses
        $r->get('/admin/statuses', 'admin_statuses')->to("{$traq}\\Admin\\Statuses::index");
        $r->get('/admin/statuses/new', 'new_admin_status')->to("{$traq}\\Admin\\Statuses::new");
        $r->post('/admin/statuses/new')->to("{$traq}\\Admin\\Statuses::create");
        $r->get('/admin/statuses/{id}/edit', 'edit_admin_status')->to("{$traq}\\Admin\\Statuses::edit");
        $r->post('/admin/statuses/{id}/edit')->to("{$traq}\\Admin\\Statuses::save");
        $r->get('/admin/statuses/{id}/delete', 'delete_admin_status')->to("{$traq}\\Admin\\Statuses::destroy");

        // Priorities
        $r->get('/admin/priorities', 'admin_priorities')->to("{$traq}\\Admin\\Priorities::index");
        $r->get('/admin/priorities/new', 'new_admin_priority')->to("{$traq}\\Admin\\Priorities::new");
        $r->post('/admin/priorities/new')->to("{$traq}\\Admin\\Priorities::create");
        $r->get('/admin/priorities/{id}/edit', 'edit_admin_priority')->to("{$traq}\\Admin\\Priorities::edit");
        $r->post('/admin/priorities/{id}/edit')->to("{$traq}\\Admin\\Priorities::save");
        $r->get('/admin/priorities/{id}/delete', 'delete_admin_priority')->to("{$traq}\\Admin\\Priorities::destroy");

        // Severities
        $r->get('/admin/severities', 'admin_severities')->to("{$traq}\\Admin\\Severities::index");
        $r->get('/admin/severities/new', 'new_admin_severity')->to("{$traq}\\Admin\\Severities::new");
        $r->post('/admin/severities/new')->to("{$traq}\\Admin\\Severities::create");
        $r->get('/admin/severities/{id}/edit', 'edit_admin_severity')->to("{$traq}\\Admin\\Severities::edit");
        $r->post('/admin/severities/{id}/edit')->to("{$traq}\\Admin\\Severities::save");
        $r->get('/admin/severities/{id}/delete', 'delete_admin_severity')->to("{$traq}\\Admin\\Severities::destroy");

        // Types
        $r->get('/admin/types', 'admin_types')->to("{$traq}\\Admin\\Types::index");
        $r->get('/admin/types/new', 'new_admin_type')->to("{$traq}\\Admin\\Types::new");
        $r->post('/admin/types/new')->to("{$traq}\\Admin\\Types::create");
        $r->get('/admin/types/{id}/edit', 'edit_admin_type')->to("{$traq}\\Admin\\Types::edit");
        $r->post('/admin/types/{id}/edit')->to("{$traq}\\Admin\\Types::save");
        $r->get('/admin/types/{id}/delete', 'delete_admin_type')->to("{$traq}\\Admin\\Types::destroy");

        // Project Roles
        $r->get('/admin/roles', 'admin_project_roles')->to("{$traq}\\Admin\\ProjectRoles::index");
        $r->get('/admin/roles/new', 'new_admin_project_role')->to("{$traq}\\Admin\\ProjectRoles::new");
        $r->post('/admin/roles/new')->to("{$traq}\\Admin\\ProjectRoles::create");
        $r->get('/admin/roles/{id}/edit', 'edit_admin_project_role')->to("{$traq}\\Admin\\ProjectRoles::edit");
        $r->post('/admin/roles/{id}/edit')->to("{$traq}\\Admin\\ProjectRoles::save");
        $r->get('/admin/roles/{id}/delete', 'delete_admin_project_role')->to("{$traq}\\Admin\\ProjectRoles::destroy");

        // Groups
        $r->get('/admin/groups', 'admin_groups')->to("{$traq}\\Admin\\Groups::index");
        $r->get('/admin/groups/new', 'new_admin_group')->to("{$traq}\\Admin\\Groups::new");
        $r->post('/admin/groups/new')->to("{$traq}\\Admin\\Groups::create");
        $r->get('/admin/groups/{id}/edit', 'edit_admin_group')->to("{$traq}\\Admin\\Groups::edit");
        $r->post('/admin/groups/{id}/edit')->to("{$traq}\\Admin\\Groups::save");
        $r->get('/admin/groups/{id}/delete', 'delete_admin_group')->to("{$traq}\\Admin\\Groups::destroy");
    }
}
