<?php
return [
    // Environment
    // Set to `development` for debugging.
    'environment' => "production",

    // Database config
    'database' => [
        // MySQL connection
        'driver'   => 'pdo_mysql', // Database type.
        'host'     => 'localhost', // Database server.
        'user'     => 'root',      // Database username.
        'password' => 'root',      // Database password.
        'dbname'   => 'traq',      // Database name.
        //'port'   => 3306         // Database server port
    ]
];
