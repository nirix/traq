<?php
use Avalon\Routing\Router;

$pn = 'project_settings_';
$purl = '/{pslug}/settings';
$pns = "{$ns}ProjectSettings\\";

// -----------------------------------------------------------------------------
// Options
Router::get('project_settings', $purl, "{$pns}Options::index");
Router::put('project_settings_save', $purl, "{$pns}Options::save");

// -----------------------------------------------------------------------------
// Milestones
Router::get("{$pn}milestones", "{$purl}/milestones", "{$pns}Milestones::index");
Router::get("{$pn}new_milestone", "{$purl}/milestones/new", "{$pns}Milestones::new");
Router::post("{$pn}create_milestone", "{$purl}/milestones", "{$pns}Milestones::create");
Router::get("{$pn}edit_milestone", "{$purl}/milestones/{id}/edit", "{$pns}Milestones::edit");
Router::put("{$pn}save_milestone", "{$purl}/milestones/{id}", "{$pns}Milestones::save")->method(['PUT', 'PATCH']);
Router::get("{$pn}delete_milestone", "{$purl}/milestones/{id}/delete", "{$pns}Milestones::delete");

// -----------------------------------------------------------------------------
// Components
Router::get("{$pn}components", "{$purl}/components", "{$pns}Components::index");
Router::get("{$pn}new_component", "{$purl}/components/new", "{$pns}Components::new");
Router::post("{$pn}create_component", "{$purl}/components", "{$pns}Components::create");
Router::get("{$pn}edit_component", "{$purl}/components/{id}/edit", "{$pns}Components::edit");
Router::put("{$pn}save_component", "{$purl}/components/{id}", "{$pns}Components::save")->method(['PUT', 'PATCH']);
Router::delete("{$pn}delete_component", "{$purl}/components/{id}/delete", "{$pns}Components::destroy");

// -----------------------------------------------------------------------------
// Members
Router::get("{$pn}members", "{$purl}/members", "{$pns}Members::index");
Router::post("{$pn}create_member", "{$purl}/members", "{$pns}Members::create");
Router::put("{$pn}save_members", "{$purl}/members", "{$pns}Members::saveAll");
Router::delete("{$pn}delete_member", "{$purl}/members/{id}", "{$pns}Members::destroy");

// -----------------------------------------------------------------------------
// Custom fields
Router::get("{$pn}custom_fields", "{$purl}/custom-fields", "{$pns}CustomFields::index");
Router::get("{$pn}new_custom_field", "{$purl}/custom-fields/new", "{$pns}CustomFields::new");
Router::post("{$pn}create_custom_field", "{$purl}/custom-fields", "{$pns}CustomFields::create");
Router::get("{$pn}edit_custom_field", "{$purl}/custom-fields/{id}/edit", "{$pns}CustomFields::edit");
Router::put("{$pn}save_custom_field", "{$purl}/custom-fields/{id}", "{$pns}CustomFields::save");

Router::delete("{$pn}delete_custom_field", "{$purl}/custom-fields/{id}", "{$pns}CustomFields::destroy");
