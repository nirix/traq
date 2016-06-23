<?php

use Avalon\Testing\TestSuite;

TestSuite::group('Admin / Projects', function ($g) {
    $admin = $GLOBALS['admin'];
    $project = createProject();

    $g->test('Create project', function ($t) use ($admin) {
        $resp = $t->visit('admin_create_project', [
            'method' => 'POST',
            'post' => [
                'name' => 'My Project',
                'slug' => 'my-project',
                'info' => 'This is a test project.'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->shouldRedirectTo($resp, $t->generateUrl('admin_projects'));
    });

    $g->test('Update project', function ($t) use ($admin, $project) {
        $resp = $t->visit('admin_save_project', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $project['id']
            ],
            'post' => [
                'name' => 'Just Another Project'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->shouldRedirectTo($resp, $t->generateUrl('admin_projects'));
    });

    $g->test('Delete project', function ($t) use ($admin, $project) {
        $resp = $t->visit('admin_delete_project', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => $project['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->shouldRedirectTo($resp, $t->generateUrl('admin_projects'));
    });
});
