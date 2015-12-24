<?php
use Avalon\Routing\Router;

$ns = "Traq\\Controllers";

//([a-z\-0-9\.]+)
Router::$extensions[] = 'js';

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

// Login and out
Router::get('session_new', '/login', "{$ns}\\Sessions::new");
Router::post('session_create', '/login', "{$ns}\\Sessions::create");
Router::get('session_destroy', '/logout', "{$ns}\\Sessions::destroy");
Router::get('user', '/profile/{id}', "{$ns}\\Users::show");

// Register
Router::get('user_new', '/register', "{$ns}\\Users::new");
Router::post('user_create', '/register', "{$ns}\\Users::create");
Router::get('account_activation', '/users/activate/{activation_code}', "{$ns}\\Users::activate");

// UserCP
Router::get('usercp', '/usercp', "{$ns}\\UserCP::index");
Router::get('usercp_generate_api_key', '/usercp/create_api_key', "{$ns}\\UserCP::generateApiKey");
Router::post('usercp_save', '/usercp', "{$ns}\\UserCP::save");

Router::get('usercp_password', '/usercp/password', "{$ns}\\UserCP::password");

Router::get('usercp_subscriptions', '/usercp/subscriptions', "{$ns}\\UserCP::subscriptions");

// -----------------------------------------------------------------------------
// Admin routes
require __DIR__ . '/routes/admin.php';

// -----------------------------------------------------------------------------
// Project settings routes
require __DIR__ . '/routes/project_settings.php';

// -----------------------------------------------------------------------------
// Project routes
require __DIR__ . '/routes/projects.php';
