<?php

$testSuite->createGroup('Requests / Admin / Types', function ($g) {
    $admin = createAdmin();
    $type = createType();

    $g->test('List types', function ($t) use ($admin, $type) {
        $resp = $t->visit('admin_types', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains($type['name'], $resp->body);
    });

    $g->test('New type form', function ($t) use ($admin) {
        $resp = $t->visit('admin_new_type', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">New Type</h1>', $resp->body);
    });

    $g->test('Create type', function ($t) use ($admin) {
        $resp = $t->visit('admin_create_type', [
            'method' => 'POST',
            'post' => [
                'name' => 'My Type',
                'bullet' => '#'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_types'), $resp);
    });

    $g->test('Name in use', function ($t) use ($admin) {
        $resp = $t->visit('admin_create_type', [
            'method' => 'POST',
            'post' => [
                'name' => 'Defect'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('Name is already in use', $resp->body);
    });

    $g->test('Edit type form', function ($t) use ($admin, $type) {
        $resp = $t->visit('admin_edit_type', [
            'routeTokens' => [
                'id' => $type['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">Edit Type</h1>', $resp->body);
    });

    $g->test('Update type', function ($t) use ($admin, $type) {
        $resp = $t->visit('admin_save_type', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $type['id']
            ],
            'post' => [
                'name' => 'Just Another Type'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_types'), $resp);
    });

    $g->test('Name is required', function ($t) use ($admin, $type) {
        $resp = $t->visit('admin_save_type', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $type['id']
            ],
            'post' => [
                'name' => ''
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('Name is required', $resp->body);
    });

    $g->test('Delete type', function ($t) use ($admin, $type) {
        $resp = $t->visit('admin_delete_type', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => $type['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_types'), $resp);
    });
});
