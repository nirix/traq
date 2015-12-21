<?php
use Avalon\Routing\Router;

$ns = "Traq\\Controllers";

//([a-z\-0-9\.]+)

// Router::addToken('slug', '(?P<slug>[a-z\-0-9\.]+)');
Router::addToken('pslug', '(?P<pslug>[^/]+)');
Router::addToken('mslug', '(?P<mslug>[^/]+)');
Router::addToken('activation_code', '(?P<activation_code>[a-zA-Z0-9]+)');

// Root
Router::get('root', '/', "{$ns}\\Projects::index");
Router::set404("{$ns}\\Errors::notFound");

Router::get('projects', '/projects', "{$ns}\\Projects::index");
Router::get('js', '/_js', "{$ns}\\Misc::js");

// -----------------------------------------------------------------------------
// User routes

// Login
Router::get('session_new', '/login', "{$ns}\\Sessions::new");
Router::post('session_create', '/login', "{$ns}\\Sessions::create");

// Register
Router::get('user_new', '/register', "{$ns}\\Users::new");
Router::post('user_create', '/register', "{$ns}\\Users::create");

Router::get('session_destroy', '/logout', "{$ns}\\Sessions::destroy");
Router::get('user', '/profile/{id}', "{$ns}\\Users::show");

Router::get('account_activation', '/users/activate/{activation_code}', "{$ns}\\Users::activate");

// -----------------------------------------------------------------------------
// Admin routes
require __DIR__ . '/routes/admin.php';

// -----------------------------------------------------------------------------
// Project routes
require __DIR__ . '/routes/projects.php';
