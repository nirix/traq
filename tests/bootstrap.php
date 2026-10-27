<?php

require __DIR__ . '/../vendor/autoload.php';

// Constants normally defined by src/bootstrap.php.
define('DOCROOT', dirname(__DIR__));
define('SYSPATH', DOCROOT . '/vendor/avalon/framework');
define('APPPATH', DOCROOT . '/vendor/traq');

require_once DOCROOT . '/src/Traq/version.php';

// Procedural helpers and classes that don't need the full application booted.
require_once DOCROOT . '/src/Traq/Helpers/uri.php';
require_once DOCROOT . '/src/Traq/common.php';
require_once DOCROOT . '/src/Traq/Locale.php';
