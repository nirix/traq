<?php
use Avalon\Routing\Router;

$ns = "Traq\\Controllers\\";

Router::addToken('pslug', '(?P<pslug>[^/]*?)');
Router::addToken('wslug', '(?P<slug>.*)');
Router::addExtension('txt');

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
Router::post('usercp_save', '/usercp', "{$ns}UserCP::save");
Router::get('usercp_password', '/usercp/password', "{$ns}UserCP::password");
Router::patch('usercp_password_save', '/usercp/password', "{$ns}UserCP::savePassword");
Router::get('usercp_subscriptions', '/usercp/subscriptions', "{$ns}UserCP::subscriptions");

Router::get('usercp_generate_api_key', '/user/generate-api-key', "{$ns}UserCP::generateApiKey");

// -----------------------------------------------------------------------------
// Admin routes
require __DIR__ . '/routes/admin.php';

// -----------------------------------------------------------------------------
// Project routes
require __DIR__ . '/routes/projects.php';

// -----------------------------------------------------------------------------
// Project settings routes
require __DIR__ . '/routes/project_settings.php';
