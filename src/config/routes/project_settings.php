<?php
use Avalon\Routing\Router;

// Settings
Router::get('project_settings', '/{pslug}/settings', "{$ns}\\ProjectSettings\Options::index");
Router::post('project_settings_save', '/{pslug}/settings', "{$ns}\\ProjectSettings\Options::save");

// Milestones
Router::get('project_settings_milestones', '/{pslug}/settings/milestones', "{$ns}\\ProjectSettings\\Milestones::index");
Router::get('project_settings_new_milestone', '/{pslug}/settings/milestones/new', "{$ns}\\ProjectSettings\\Milestones::new");
Router::post('project_settings_create_milestone', '/{pslug}/settings/milestones/new', "{$ns}\\ProjectSettings\\Milestones::create");
Router::get('project_settings_edit_milestone', '/{pslug}/settings/milestones/{id}/edit', "{$ns}\\ProjectSettings\\Milestones::edit");
Router::post('project_settings_save_milestone', '/{pslug}/settings/milestones/{id}/edit', "{$ns}\\ProjectSettings\\Milestones::save");
Router::get('project_settings_delete_milestone', '/{pslug}/settings/milestones/{id}/delete', "{$ns}\\ProjectSettings\\Milestones::destroy");

// Components
Router::get('project_settings_components', '/{pslug}/settings/components', "{$ns}\\ProjectSettings\\Components::index");
Router::get('project_settings_new_component', '/{pslug}/settings/components/new', "{$ns}\\ProjectSettings\\Components::new");
Router::post('project_settings_create_component', '/{pslug}/settings/components/new', "{$ns}\\ProjectSettings\\Components::create");
Router::get('project_settings_edit_component', '/{pslug}/settings/components/{id}/edit', "{$ns}\\ProjectSettings\\Components::edit");
Router::post('project_settings_save_component', '/{pslug}/settings/components/{id}/edit', "{$ns}\\ProjectSettings\\Components::save");
Router::get('project_settings_delete_component', '/{pslug}/settings/components/{id}/delete', "{$ns}\\ProjectSettings\\Components::destroy");

// Members
Router::get('project_settings_members', '/{pslug}/settings/members', "{$ns}\\ProjectSettings\\Members::index");
Router::post('project_settings_create_member', '/{pslug}/settings/members/new', "{$ns}\\ProjectSettings\\Members::create");
Router::post('project_settings_save_members', '/{pslug}/settings/members/save', "{$ns}\\ProjectSettings\\Members::saveAll");
Router::get('project_settings_delete_member', '/{pslug}/settings/members/{id}/delete', "{$ns}\\ProjectSettings\\Members::destroy");

// Custom fields
Router::get('project_settings_custom_fields', '/{pslug}/settings/custom-fields', "{$ns}\\ProjectSettings\\CustomFields::index");
Router::get('project_settings_new_custom_field', '/{pslug}/settings/custom-fields/new', "{$ns}\\ProjectSettings\\CustomFields::new");
Router::post('project_settings_create_custom_field', '/{pslug}/settings/custom-fields/new', "{$ns}\\ProjectSettings\\CustomFields::create");
