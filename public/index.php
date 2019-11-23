<?php
$autoloader = require __DIR__ . '/vendor/autoload.php';

define('START_TIME', microtime(true));
define('START_MEM', memory_get_usage());

if (!file_exists(__DIR__ . '/config/config.php')) {
    header("Location: ./install");
    exit;
}

(new Traq\Kernel)->run();
