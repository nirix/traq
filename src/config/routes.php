<?php
use Avalon\Routing\Router;

$ns = "Traq\\Controllers\\";

Router::addToken('pslug', '(?P<pslug>[^/]*?)');

Router::root("{$ns}Projects::index");

// -----------------------------------------------------------------------------
// User routes

Router::get('register', '/register', "{$ns}Users::new");
Router::post('user_create', '/register', "{$ns}Users::create");

Router::get('login', '/login', "{$ns}Sessions::new");
Router::post('session_create', '/login', "{$ns}Sessions::create");
Router::delete('logout', '/logout', "{$ns}Sessions::destroy");

Router::get('user', '/users/{id}', "{$ns}Users::show");

Router::get('usercp', '/usercp', "{$ns}UserCP::index");

// -----------------------------------------------------------------------------
// Project routes
require __DIR__ . '/routes/admin.php';

// -----------------------------------------------------------------------------
// Project routes
require __DIR__ . '/routes/projects.php';
