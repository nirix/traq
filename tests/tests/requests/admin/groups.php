<?php

$testSuite->createGroup('Requests / Admin / Groups', function ($g) {
    $admin = createAdmin();
    $group = createGroup();

    $g->test('List groups', function ($t) use ($admin, $group) {
        $resp = $t->visit('admin_groups', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains($group['name'], $resp->body);
    });

    $g->test('New group form', function ($t) use ($admin) {
        $resp = $t->visit('admin_new_group', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">New Group</h1>', $resp->body);
    });

    $g->test('Create group', function ($t) use ($admin) {
        $resp = $t->visit('admin_create_group', [
            'method' => 'POST',
            'post' => [
                'name' => 'My Group'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_groups'), $resp);
    });

    $g->test('Name in use', function ($t) use ($admin) {
        $resp = $t->visit('admin_create_group', [
            'method' => 'POST',
            'post' => [
                'name' => 'Admin'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertContains('Name is already in use', $resp->body);
    });

    $g->test('Edit group form', function ($t) use ($admin, $group) {
        $resp = $t->visit('admin_edit_group', [
            'routeTokens' => [
                'id' => $group['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertContains('<h1 class="page-header">Edit Group</h1>', $resp->body);
    });

    $g->test('Update group', function ($t) use ($admin, $group) {
        $resp = $t->visit('admin_save_group', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $group['id']
            ],
            'post' => [
                'name' => 'Just Another Group'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_groups'), $resp);
    });

    $g->test('Name is required', function ($t) use ($admin, $group) {
        $resp = $t->visit('admin_save_group', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $group['id']
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

    $g->test('Delete group', function ($t) use ($admin, $group) {
        $resp = $t->visit('admin_delete_group', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => $group['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_groups'), $resp);
    });
});
