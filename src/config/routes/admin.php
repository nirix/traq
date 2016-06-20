<?php
use Avalon\Routing\Router;

$ans = "{$ns}Admin\\";

// -----------------------------------------------------------------------------
// Admin routes
Router::get('admincp', '/admin', "{$ans}Dashboard::index");

// Settings
Router::get('admin_settings', '/admin/settings', "{$ans}Settings::index");
Router::post('admin_settings_save', '/admin/settings', "{$ans}Settings::save");

// Projects
Router::get('admin_projects', '/admin/projects', "{$ans}Projects::index");
Router::get('admin_new_project', '/admin/projects/new', "{$ans}Projects::new");
Router::post('admin_create_project', '/admin/projects', "{$ans}Projects::create");
Router::get('admin_edit_project', '/admin/projects/{id}/edit', "{$ans}Projects::edit");
Router::put('admin_save_project', '/admin/projects/{id}', "{$ans}Projects::save")->method(['PUT', 'PATCH']);
Router::delete('admin_delete_project', '/admin/projects/{id}/delete', "{$ans}Projects::destroy");

// Project Roles
Router::get('admin_project_roles', '/admin/project-roles', "{$ans}ProjectRoles::index");

// Users
Router::get('admin_users', '/admin/users', "{$ans}Users::index");

// Groups
Router::get('admin_groups', '/admin/usergroups', "{$ans}Groups::index");

// Plugins
Router::get('admin_plugins', '/admin/plugins', "{$ans}Plugins::index");

// Types
Router::get('admin_types', '/admin/types', "{$ans}Types::index");

// Statuses
Router::get('admin_statuses', '/admin/statuses', "{$ans}Statuses::index");

// Priorities
Router::get('admin_priorities', '/admin/priorities', "{$ans}Priorities::index");

// Severities
Router::get('admin_severities', '/admin/severities', "{$ans}Severities::index");

// Permissions
Router::get('admin_permissions', '/admin/permissions/usergroups', "{$ans}Permissions::usergroups");
