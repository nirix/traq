<?php

$testSuite->createGroup('Requests / Admin / Project Roles', function ($g) {
    $admin = createAdmin();
    $role = createProjectRole();

    $g->test('List roles', function ($t) use ($admin) {
        $resp = $t->visit('admin_project_roles', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">Roles</h1>', $resp->body);
    });

    $g->test('New project form', function ($t) use ($admin) {
        $resp = $t->visit('admin_new_project_role', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">New Role</h1>', $resp->body);
    });

    $g->test('Create project role', function ($t) use ($admin) {
        $resp = $t->visit('admin_create_project_role', [
            'method' => 'POST',
            'post' => [
                'name' => 'Testing Role'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_project_roles'), $resp);
    });

    $g->test('Edit project role form', function ($t) use ($admin, $role) {
        $resp = $t->visit('admin_edit_project_role', [
            'routeTokens' => [
                'id' => $role['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertContains('<h1 class="page-header">Edit Role</h1>', $resp->body);
    });

    $g->test('Update project role', function ($t) use ($admin, $role) {
        $resp = $t->visit('admin_save_project_role', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $role['id']
            ],
            'post' => [
                'name' => 'Just Another Project Role'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_project_roles'), $resp);
    });

    $g->test('Name is required', function ($t) use ($admin, $role) {
        $resp = $t->visit('admin_save_project_role', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $role['id']
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

    $g->test('Delete project role', function ($t) use ($admin, $role) {

        $resp = $t->visit('admin_delete_project_role', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => $role['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_project_roles'), $resp);
    });
});
