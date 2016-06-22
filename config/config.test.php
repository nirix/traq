<?php
return [
    'environment' => "test",
    'db' => [
        'test' => [
            'driver'   => 'pdo_mysql',
            'host'     => '127.0.0.1',
            'user'     => 'travis',
            'password' => '',
            'dbname'   => 'traq_test',
            'prefix'   => 'traq_',
            'charset'  => 'utf8'
        ]
    ],

    'email' => [
        'type' => "sendmail",
        'path' => "/usr/bin/sendmail -bs"
    ]
];
