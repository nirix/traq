<?php

$testSuite->createGroup('Requests / Admin / Statuses', function ($g) {
    $admin = createAdmin();
    $status = createStatus();

    $g->test('List statuses', function ($t) use ($admin, $status) {
        $resp = $t->visit('admin_statuses', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains($status['name'], $resp->body);
    });

    $g->test('New status form', function ($t) use ($admin) {
        $resp = $t->visit('admin_new_status', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">New Status</h1>', $resp->body);
    });

    $g->test('Create status', function ($t) use ($admin) {
        $resp = $t->visit('admin_create_status', [
            'method' => 'POST',
            'post' => [
                'name' => 'My Status',
                'level' => 5
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_statuses'), $resp);
    });

    $g->test('Name in use', function ($t) use ($admin) {
        $resp = $t->visit('admin_create_status', [
            'method' => 'POST',
            'post' => [
                'name' => 'New'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertContains('Name is already in use', $resp->body);
    });

    $g->test('Edit status form', function ($t) use ($admin, $status) {
        $resp = $t->visit('admin_edit_status', [
            'routeTokens' => [
                'id' => $status['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertContains('<h1 class="page-header">Edit Status</h1>', $resp->body);
    });

    $g->test('Update status', function ($t) use ($admin, $status) {
        $resp = $t->visit('admin_save_status', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $status['id']
            ],
            'post' => [
                'name' => 'Just Another Status'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_statuses'), $resp);
    });

    $g->test('Name is required', function ($t) use ($admin, $status) {
        $resp = $t->visit('admin_save_status', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $status['id']
            ],
            'post' => [
                'name' => ''
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertContains('Name is required', $resp->body);
    });

    $g->test('Delete status', function ($t) use ($admin, $status) {
        $resp = $t->visit('admin_delete_status', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => $status['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_statuses'), $resp);
    });
});
