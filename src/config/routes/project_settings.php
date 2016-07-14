<?php
use Avalon\Routing\Router;

$pn = 'project_settings_';
$purl = '/{pslug}/project-settings';
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
