<?php
use Avalon\Routing\Router;

$ans = "{$ns}Admin\\";

// -----------------------------------------------------------------------------
// Admin routes
Router::get('admincp', '/admin', "{$ans}Dashboard::index");
