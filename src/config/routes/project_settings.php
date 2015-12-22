<?php
use Avalon\Routing\Router;

// Settings
Router::get('project_settings', '/{pslug}/settings', "{$ns}\\ProjectSettings\Options::index");
Router::post('project_settings_save', '/{pslug}/settings', "{$ns}\\ProjectSettings\Options::save");

// Milestones
Router::get('project_settings_milestones', '/{pslug}/settings/milestones', "{$ns}\\ProjectSettings\\Milestones::index");

// Components
Router::get('project_settings_components', '/{pslug}/settings/components', "{$ns}\\ProjectSettings\\Components::index");

// Members
Router::get('project_settings_members', '/{pslug}/settings/members', "{$ns}\\ProjectSettings\\Members::index");

// Custom fields
Router::get('project_settings_custom_fields', '/{pslug}/settings/custom-fields', "{$ns}\\ProjectSettings\\CustomFields::index");
