<?php

$testSuite->createGroup('Requests / Admin / Users', function ($g) {
    $admin = createAdmin();
    $user = createUser();

    $g->test('List users', function ($t) use ($admin, $user) {
        $resp = $t->visit('admin_users', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains($user['name'], $resp->body);
    });

    $g->test('New user form', function ($t) use ($admin) {
        $resp = $t->visit('admin_new_user', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">New User</h1>', $resp->body);
    });

    $g->test('Create user', function ($t) use ($admin) {
        $resp = $t->visit('admin_create_user', [
            'method' => 'POST',
            'post' => [
                'name'     => 'My User',
                'username' => 'my_user',
                'password'  => 'testing1234',
                'email'    => 'testing1234@example.com',
                'group_id' => 2
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_users'), $resp);
    });

    $g->test('Username in use', function ($t) use ($admin) {
        $resp = $t->visit('admin_create_user', [
            'method' => 'POST',
            'post' => [
                'username' => 'Anonymous'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('Username is already in use', $resp->body);
    });

    $g->test('Edit user form', function ($t) use ($admin, $user) {
        $resp = $t->visit('admin_edit_user', [
            'routeTokens' => [
                'id' => $user['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">Edit User</h1>', $resp->body);
    });

    $g->test('Update user', function ($t) use ($admin, $user) {
        $resp = $t->visit('admin_save_user', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $user['id']
            ],
            'post' => [
                'name' => 'Just Another User',
                'password' => '1234testing'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_users'), $resp);
    });

    $g->test('Name is required', function ($t) use ($admin, $user) {
        $resp = $t->visit('admin_save_user', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $user['id']
            ],
            'post' => [
                'username' => ''
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('Username is required', $resp->body);
    });

    $g->test('Delete user', function ($t) use ($admin, $user) {
        $resp = $t->visit('admin_delete_user', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => $user['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_users'), $resp);
    });
});
