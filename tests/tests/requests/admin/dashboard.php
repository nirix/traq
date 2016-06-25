<?php

$testSuite->createGroup('Requests / Admin / Dashboard', function ($g) {
    $admin = createAdmin();

    $g->test('Should deny access', function ($t) {
        $resp = $t->visit('admincp');

        $t->assertEquals(403, $resp->status);
    });

    $g->test('Should allow access', function ($t) use ($admin) {
        $resp = $t->visit('admincp', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('class="navbar-brand">AdminCP</a>', $resp->body);
    });
});
