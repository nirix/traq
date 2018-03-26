<?php
$autoloader = require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../src/version.php';
require __DIR__ . '/helpers/common.php';
require __DIR__ . '/helpers/models.php';

$testSuite = new \Avalon\Testing\TestSuite(
    Traq\Kernel::class,
    Traq\Database\Seeder::class,
    require __DIR__ . '/../config/config.php'
);

$testSuite->setup();

\Avalon\Testing\PhpUnit\TestCase::$app = $testSuite->getApp();
