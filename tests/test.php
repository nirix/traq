<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/src/version.php';

use Avalon\Testing\TestSuite;
use Traq\Kernel as TraqKernel;
use Traq\Database\Seeder as TraqSeeder;
use Traq\Models\Group;

TestSuite::configure(function ($suite) {
    $suite->setAppClass(TraqKernel::class);
    $suite->setAppPath(dirname(__DIR__) . '/src');
    $suite->setSeeder(new TraqSeeder);

    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PlainTextHandler);
    $whoops->register();
});

// Load some helpers
require __DIR__ . '/helpers.php';

$GLOBALS['admin'] = createUser(null, Group::find(1));
// Load some tests
require __DIR__ . '/projects/show.php';
require __DIR__ . '/projects/roadmap.php';
require __DIR__ . '/tickets/listing.php';
require __DIR__ . '/tickets/update.php';

// Go
printf('Running tests for Traq v%s / DB rev %d' . PHP_EOL . PHP_EOL, Traq\VERSION, Traq\DB_REVISION);
TestSuite::run();
