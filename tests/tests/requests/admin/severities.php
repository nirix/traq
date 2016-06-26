<?php

$testSuite->createGroup('Requests / Admin / Severities', function ($g) {
    $admin = createAdmin();
    $severity = createSeverity();

    $g->test('List severities', function ($t) use ($admin, $severity) {
        $resp = $t->visit('admin_severities', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains($severity['name'], $resp->body);
    });

    $g->test('New severity form', function ($t) use ($admin) {
        $resp = $t->visit('admin_new_severity', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">New Severity</h1>', $resp->body);
    });

    $g->test('Create severity', function ($t) use ($admin) {
        $resp = $t->visit('admin_create_severity', [
            'method' => 'POST',
            'post' => [
                'name' => 'My Severity',
                'level' => 5
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_severities'), $resp);
    });

    $g->test('Name in use', function ($t) use ($admin) {
        $resp = $t->visit('admin_create_severity', [
            'method' => 'POST',
            'post' => [
                'name' => 'Normal'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertContains('Name is already in use', $resp->body);
    });

    $g->test('Edit severity form', function ($t) use ($admin, $severity) {
        $resp = $t->visit('admin_edit_severity', [
            'routeTokens' => [
                'id' => $severity['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertContains('<h1 class="page-header">Edit Severity</h1>', $resp->body);
    });

    $g->test('Update severity', function ($t) use ($admin, $severity) {
        $resp = $t->visit('admin_save_severity', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $severity['id']
            ],
            'post' => [
                'name' => 'Just Another Severity'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_severities'), $resp);
    });

    $g->test('Name is required', function ($t) use ($admin, $severity) {
        $resp = $t->visit('admin_save_severity', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $severity['id']
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

    $g->test('Delete severity', function ($t) use ($admin, $severity) {
        $resp = $t->visit('admin_delete_severity', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => $severity['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_severities'), $resp);
    });
});
