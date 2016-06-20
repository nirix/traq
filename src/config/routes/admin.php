<?php
use Avalon\Routing\Router;

$ans = "{$ns}Admin\\";

// -----------------------------------------------------------------------------
// Admin routes
Router::get('admincp', '/admin', "{$ans}Dashboard::index");

// Settings
Router::get('admin_settings', '/admin/settings', "{$ans}Settings::index");
Router::post('admin_settings_save', '/admin/settings', "{$ans}Settings::save");
