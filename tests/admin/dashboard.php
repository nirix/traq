<?php

use Avalon\Testing\TestSuite;

TestSuite::group('Admin Dashboard', function ($g) {
    $admin = $GLOBALS['admin'];

    $g->test('Should deny access', function ($t) {
        $resp = $t->visit('admincp');

        $t->assertEqual(403, $resp->getResponse()->status);
    });

    $g->test('Should allow access', function ($t) use ($admin) {
        $resp = $t->visit('admincp', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEqual(200, $resp->getResponse()->status);
        $t->shouldContain($resp, 'class="navbar-brand">AdminCP</a>');
    });
});
