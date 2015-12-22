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

// Members
Router::get('project_settings_members', '/{pslug}/settings/members', "{$ns}\\ProjectSettings\\Members::index");

// Custom fields
Router::get('project_settings_custom_fields', '/{pslug}/settings/custom-fields', "{$ns}\\ProjectSettings\\CustomFields::index");
