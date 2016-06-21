<?php
use Avalon\Routing\Router;

$ans = "{$ns}Admin\\";

// -----------------------------------------------------------------------------
// Admin routes
Router::get('admincp', '/admin', "{$ans}Dashboard::index");

// -----------------------------------------------------------------------------
// Settings
Router::get('admin_settings', '/admin/settings', "{$ans}Settings::index");
Router::post('admin_settings_save', '/admin/settings', "{$ans}Settings::save");

// -----------------------------------------------------------------------------
// Projects
Router::get('admin_projects', '/admin/projects', "{$ans}Projects::index");
Router::get('admin_new_project', '/admin/projects/new', "{$ans}Projects::new");
Router::post('admin_create_project', '/admin/projects', "{$ans}Projects::create");
Router::get('admin_edit_project', '/admin/projects/{id}/edit', "{$ans}Projects::edit");
Router::put('admin_save_project', '/admin/projects/{id}', "{$ans}Projects::save")->method(['PUT', 'PATCH']);
Router::delete('admin_delete_project', '/admin/projects/{id}/delete', "{$ans}Projects::destroy");

// -----------------------------------------------------------------------------
// Project Roles
Router::get('admin_project_roles', '/admin/project-roles', "{$ans}ProjectRoles::index");
Router::get('admin_new_project_role', '/admin/project-roles/new', "{$ans}ProjectRoles::new");
Router::post('admin_create_project_role', '/admin/project-roles', "{$ans}ProjectRoles::create");
Router::get('admin_edit_project_role', '/admin/project-roles/{id}/edit', "{$ans}ProjectRoles::edit");
Router::put('admin_save_project_role', '/admin/project-roles/{id}', "{$ans}ProjectRoles::save")->method(['PUT', 'PATCH']);
Router::delete('admin_delete_project_role', '/admin/project-roles/{id}/delete', "{$ans}ProjectRoles::destroy");

// -----------------------------------------------------------------------------
// Users
Router::get('admin_users', '/admin/users', "{$ans}Users::index");

// -----------------------------------------------------------------------------
// Groups
Router::get('admin_groups', '/admin/usergroups', "{$ans}Groups::index");
Router::get('admin_new_group', '/admin/usergroups/new', "{$ans}Groups::new");
Router::post('admin_create_group', '/admin/usergroups', "{$ans}Groups::create");
Router::get('admin_edit_group', '/admin/usergroups/{id}/edit', "{$ans}Groups::edit");
Router::put('admin_save_group', '/admin/usergroups/{id}', "{$ans}Groups::save")->method(['PUT', 'PATCH']);
Router::delete('admin_delete_group', '/admin/usergroups/{id}/delete', "{$ans}Groups::destroy");

// -----------------------------------------------------------------------------
// Plugins
Router::get('admin_plugins', '/admin/plugins', "{$ans}Plugins::index");
Router::get('admin_plugins_install', '/admin/plugins/install', "{$ans}Plugins::install");
Router::get('admin_plugins_uninstall', '/admin/plugins/uninstall', "{$ans}Plugins::uninstall");
Router::get('admin_plugins_enable', '/admin/plugins/enable', "{$ans}Plugins::enable");
Router::get('admin_plugins_disable', '/admin/plugins/disable', "{$ans}Plugins::disable");

// -----------------------------------------------------------------------------
// Types
Router::get('admin_types', '/admin/types', "{$ans}Types::index");
Router::get('admin_new_type', '/admin/types/new', "{$ans}Types::new");
Router::post('admin_create_type', '/admin/types', "{$ans}Types::create");
Router::get('admin_edit_type', '/admin/types/{id}/edit', "{$ans}Types::edit");
Router::put('admin_save_type', '/admin/types/{id}', "{$ans}Types::save")->method(['PUT', 'PATCH']);
Router::delete('admin_delete_type', '/admin/types/{id}/delete', "{$ans}Types::destroy");

// -----------------------------------------------------------------------------
// Statuses
Router::get('admin_statuses', '/admin/statuses', "{$ans}Statuses::index");
Router::get('admin_new_status', '/admin/statuses/new', "{$ans}Statuses::new");
Router::post('admin_create_status', '/admin/statuses', "{$ans}Statuses::create");
Router::get('admin_edit_status', '/admin/statuses/{id}/edit', "{$ans}Statuses::edit");
Router::put('admin_save_status', '/admin/statuses/{id}', "{$ans}Statuses::save")->method(['PUT', 'PATCH']);
Router::delete('admin_delete_status', '/admin/statuses/{id}/delete', "{$ans}Statuses::destroy");

// -----------------------------------------------------------------------------
// Priorities
Router::get('admin_priorities', '/admin/priorities', "{$ans}Priorities::index");
Router::get('admin_new_priority', '/admin/priorities/new', "{$ans}Priorities::new");
Router::post('admin_create_priority', '/admin/priorities', "{$ans}Priorities::create");
Router::get('admin_edit_priority', '/admin/priorities/{id}/edit', "{$ans}Priorities::edit");
Router::put('admin_save_priority', '/admin/priorities/{id}', "{$ans}Priorities::save")->method(['PUT', 'PATCH']);
Router::delete('admin_delete_priority', '/admin/priorities/{id}/delete', "{$ans}Priorities::destroy");

// -----------------------------------------------------------------------------
// Severities
Router::get('admin_severities', '/admin/severities', "{$ans}Severities::index");
Router::get('admin_new_severity', '/admin/severities/new', "{$ans}Severities::new");
Router::post('admin_create_severity', '/admin/severities', "{$ans}Severities::create");
Router::get('admin_edit_severity', '/admin/severities/{id}/edit', "{$ans}Severities::edit");
Router::put('admin_save_severity', '/admin/severities/{id}', "{$ans}Severities::save")->method(['PUT', 'PATCH']);
Router::delete('admin_delete_severity', '/admin/severities/{id}/delete', "{$ans}Severities::destroy");

// -----------------------------------------------------------------------------
// Permissions
Router::get('admin_permissions', '/admin/permissions/usergroups', "{$ans}Permissions::usergroups");
