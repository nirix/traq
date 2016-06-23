<?php
$config = [
    'environment' => 'test',
    'db' => [
        'test' => []
    ],
    'email' => [
        'type' => "sendmail",
        'path' => "/usr/bin/sendmail -bs"
    ]
];

$db = [
    'mysql' => [
        'driver'   => 'pdo_mysql',
        'host'     => '127.0.0.1',
        'user'     => 'travis',
        'password' => '',
        'dbname'   => 'traq_test',
        'prefix'   => 'test_',
        'charset'  => 'utf8'
    ],

    'postgresql' => [
        'driver'   => 'pdo_pgsql',
        'host'     => '127.0.0.1',
        'user'     => 'postgres',
        'password' => '',
        'dbname'   => 'traq_test',
        'prefix'   => 'test_',
        'charset'  => 'utf8'
    ],

    'sqlite' => [
        'driver' => 'pdo_sqlite',
        'memory' => true,
        'prefix' => 'test_'
    ]
];

$config['db']['test'] = $db[getenv('DB')];

return $config;
