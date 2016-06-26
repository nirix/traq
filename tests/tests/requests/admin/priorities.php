<?php

$testSuite->createGroup('Requests / Admin / Priorities', function ($g) {
    $admin = createAdmin();
    $priority = createPriority();

    $g->test('List priorities', function ($t) use ($admin, $priority) {
        $resp = $t->visit('admin_priorities', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains($priority['name'], $resp->body);
    });

    $g->test('New priority form', function ($t) use ($admin) {
        $resp = $t->visit('admin_new_priority', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">New Priority</h1>', $resp->body);
    });

    $g->test('Create priority', function ($t) use ($admin) {
        $resp = $t->visit('admin_create_priority', [
            'method' => 'POST',
            'post' => [
                'name' => 'My Priority'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_priorities'), $resp);
    });

    $g->test('Name in use', function ($t) use ($admin) {
        $resp = $t->visit('admin_create_priority', [
            'method' => 'POST',
            'post' => [
                'name' => 'High'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('Name is already in use', $resp->body);
    });

    $g->test('Edit priority form', function ($t) use ($admin, $priority) {
        $resp = $t->visit('admin_edit_priority', [
            'routeTokens' => [
                'id' => $priority['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">Edit Priority</h1>', $resp->body);
    });

    $g->test('Update priority', function ($t) use ($admin, $priority) {
        $resp = $t->visit('admin_save_priority', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $priority['id']
            ],
            'post' => [
                'name' => 'Just Another Priority'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_priorities'), $resp);
    });

    $g->test('Name is required', function ($t) use ($admin, $priority) {
        $resp = $t->visit('admin_save_priority', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $priority['id']
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

    $g->test('Delete priority', function ($t) use ($admin, $priority) {
        $resp = $t->visit('admin_delete_priority', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => $priority['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_priorities'), $resp);
    });
});
