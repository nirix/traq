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
 * Project setting routes.
 *
 * @author Jack Polgar <jack@polgar.id.au>
 * @since 4.0.0
 */
class ProjectSettings {
    public static function register(Router $r)
    {
        $traq = "Traq\\Controllers";

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
    }
}