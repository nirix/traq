<?php
return [
    'dsn' => sprintf(
        'smtp://%s:%s@%s',
        urlencode('my-username'),
        urlencode('my-password'),
        'smtp.my-site.com:587',
    )
];
