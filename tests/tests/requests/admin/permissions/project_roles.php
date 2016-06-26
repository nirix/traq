<?php

$testSuite->createGroup('Requests / Admin / Permissions / Project Roles', function ($g) {
    $admin = createAdmin();

    $g->test('List permissions', function ($t) use ($admin) {
        $resp = $t->visit('admin_permissions_roles', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
    });
});
