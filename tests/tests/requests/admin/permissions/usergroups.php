<?php

use Traq\Permissions;

$testSuite->createGroup('Requests / Admin / Permissions / Usergroups', function ($g) {
    $admin = createAdmin();

    $g->test('List permissions', function ($t) use ($admin) {
        $resp = $t->visit('admin_permissions', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
    });

    $g->test('Save permissions', function ($t) use ($admin) {
        $defaults = Permissions::getDefaults();

        $resp = $t->visit('admin_permissions_usergroups_save', [
            'method' => 'POST',
            'post' => [
                'permissions' => [
                    '2' => [
                        'ticket_properties_complete_tasks' => 1
                    ]
                ]
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_permissions'), $resp);
    });
});
