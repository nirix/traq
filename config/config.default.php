<?php
return [
    // Environment
    // Set to `development` for debugging.
    'environment' => "production",

    // Database config
    'db' => [
        'production' => [
            // MySQL connection
            'driver'   => 'pdo_mysql', // Database type.
            'host'     => 'localhost', // Database server.
            'user'     => 'root',      // Database username.
            'password' => 'root',      // Database password.
            'dbname'   => 'traq',      // Database name.
            //'port'   => 3306         // Database server port
        ]
    ],

    // Email config
    'email' => [
        // SMTP
        // 'type'     => "SMTP",
        // 'server'   => "smtp.mysite.com",
        // 'port'     => 25,
        // 'username' => "me@mysite.com",
        // 'password' => "mypassword"
        // 'security' => "ssl"

        // Sendmail
        'type' => "sendmail",
        'path' => "/usr/bin/sendmail -bs"
    ]
];
