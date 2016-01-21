<?php
use Avalon\Routing\Router;

Router::get('admin', '/admin', "{$ns}\\Admin\Dashboard::index");

// Settings
Router::get('admin_settings', '/admin/settings', "{$ns}\\Admin\\Settings::index");
Router::post('admin_settings_save', '/admin/settings', "{$ns}\\Admin\\Settings::save");

// Projects
Router::get('admin_projects', '/admin/projects', "{$ns}\\Admin\\Projects::index");
Router::get('admin_new_project', '/admin/projects/new', "{$ns}\\Admin\\Projects::new");
Router::post('admin_create_project', '/admin/projects/new', "{$ns}\\Admin\\Projects::create");
Router::get('admin_edit_project', '/admin/projects/{id}/edit', "{$ns}\\Admin\\Projects::edit");
Router::post('admin_save_project', '/admin/projects/{id}/edit', "{$ns}\\Admin\\Projects::save");
Router::get('admin_delete_project', '/admin/projects/{id}/delete', "{$ns}\\Admin\\Projects::destroy");

// Groups
Router::get('admin_groups', '/admin/groups', "{$ns}\\Admin\\Groups::index");
Router::get('admin_new_group', '/admin/groups/new', "{$ns}\\Admin\\Groups::new");
Router::post('admin_create_group', '/admin/groups/new', "{$ns}\\Admin\\Groups::create");
Router::get('admin_edit_group', '/admin/groups/{id}/edit', "{$ns}\\Admin\\Groups::edit");
Router::post('admin_save_group', '/admin/groups/{id}/edit', "{$ns}\\Admin\\Groups::save");
Router::get('admin_delete_group', '/admin/groups/{id}/delete', "{$ns}\\Admin\\Groups::destroy");

// Roles
Router::get('admin_project_roles', '/admin/project-roles', "{$ns}\\Admin\\ProjectRoles::index");
Router::get('admin_new_project_role', '/admin/project-roles/new', "{$ns}\\Admin\\ProjectRoles::new");
Router::post('admin_create_project_role', '/admin/project-roles/new', "{$ns}\\Admin\\ProjectRoles::create");
Router::get('admin_edit_project_role', '/admin/project-roles/{id}/edit', "{$ns}\\Admin\\ProjectRoles::edit");
Router::post('admin_save_project_role', '/admin/project-roles/{id}/edit', "{$ns}\\Admin\\ProjectRoles::save");
Router::get('admin_delete_project_role', '/admin/project-roles/{id}/delete', "{$ns}\\Admin\\ProjectRoles::destroy");

// Users
Router::get('admin_users', '/admin/users', "{$ns}\\Admin\\Users::index");
Router::get('admin_new_user', '/admin/users/new', "{$ns}\\Admin\\Users::new");
Router::post('admin_create_user', '/admin/users/new', "{$ns}\\Admin\\Users::create");
Router::get('admin_edit_user', '/admin/users/{id}/edit', "{$ns}\\Admin\\Users::edit");
Router::post('admin_save_user', '/admin/users/{id}/edit', "{$ns}\\Admin\\Users::save");
Router::get('admin_delete_user', '/admin/users/{id}/delete', "{$ns}\\Admin\\Users::destroy");

// Plugins
Router::get('admin_plugins', '/admin/plugins', "{$ns}\\Admin\\Plugins::index");
Router::get('admin_plugins_install', '/admin/plugins/install', "{$ns}\\Admin\\Plugins::install");
Router::get('admin_plugins_uninstall', '/admin/plugins/uninstall', "{$ns}\\Admin\\Plugins::uninstall");
Router::get('admin_plugins_enable', '/admin/plugins/enable', "{$ns}\\Admin\\Plugins::enable");
Router::get('admin_plugins_disable', '/admin/plugins/disable', "{$ns}\\Admin\\Plugins::disable");

// Types
Router::get('admin_types', '/admin/types', "{$ns}\\Admin\\Types::index");
Router::get('admin_new_type', '/admin/types/new', "{$ns}\\Admin\\Types::new");
Router::post('admin_create_type', '/admin/types/new', "{$ns}\\Admin\\Types::create");
Router::get('admin_edit_type', '/admin/types/{id}/edit', "{$ns}\\Admin\\Types::edit");
Router::post('admin_save_type', '/admin/types/{id}/edit', "{$ns}\\Admin\\Types::save");
Router::get('admin_delete_type', '/admin/types/{id}/delete', "{$ns}\\Admin\\Types::destroy");

// Statuses
Router::get('admin_statuses', '/admin/statuses', "{$ns}\\Admin\\Statuses::index");
Router::get('admin_new_status', '/admin/statuses/new', "{$ns}\\Admin\\Statuses::new");
Router::post('admin_create_status', '/admin/statuses/new', "{$ns}\\Admin\\Statuses::create");
Router::get('admin_edit_status', '/admin/statuses/{id}/edit', "{$ns}\\Admin\\Statuses::edit");
Router::post('admin_save_status', '/admin/statuses/{id}/edit', "{$ns}\\Admin\\Statuses::save");
Router::get('admin_delete_status', '/admin/statuses/{id}/delete', "{$ns}\\Admin\\Statuses::destroy");

// Priorities
Router::get('admin_priorities', '/admin/priorities', "{$ns}\\Admin\\Priorities::index");
Router::get('admin_new_priority', '/admin/priorities/new', "{$ns}\\Admin\\Priorities::new");
Router::post('admin_create_priority', '/admin/priorities/new', "{$ns}\\Admin\\Priorities::create");
Router::get('admin_edit_priority', '/admin/priorities/{id}/edit', "{$ns}\\Admin\\Priorities::edit");
Router::post('admin_save_priority', '/admin/priorities/{id}/edit', "{$ns}\\Admin\\Priorities::save");
Router::get('admin_delete_priority', '/admin/priorities/{id}/delete', "{$ns}\\Admin\\Priorities::destroy");

// Severities
Router::get('admin_severities', '/admin/severities', "{$ns}\\Admin\\Severities::index");
Router::get('admin_new_severity', '/admin/severities/new', "{$ns}\\Admin\\Severities::new");
Router::post('admin_create_severity', '/admin/severities/new', "{$ns}\\Admin\\Severities::create");
Router::get('admin_edit_severity', '/admin/severities/{id}/edit', "{$ns}\\Admin\\Severities::edit");
Router::post('admin_save_severity', '/admin/severities/{id}/edit', "{$ns}\\Admin\\Severities::save");
Router::get('admin_delete_severity', '/admin/severities/{id}/delete', "{$ns}\\Admin\\Severities::destroy");

// Permissions
Router::get('admin_permissions', '/admin/permissions/groups', "{$ns}\\Admin\\Permissions::groups");
Router::post('admin_permissions_groups_save', '/admin/permissions/groups', "{$ns}\\Admin\\Permissions::saveGroups");
Router::post('admin_permissions_roles_save', '/admin/permissions/roles', "{$ns}\\Admin\\Permissions::saveRoles");
Router::get('admin_permissions_roles', '/admin/permissions/roles', "{$ns}\\Admin\\Permissions::roles");
