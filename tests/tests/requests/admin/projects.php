<?php

use Traq\Models\Milestone;

$testSuite->createGroup('Requests / Admin / Projects', function ($g) {
    $admin = createAdmin();
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

        $t->assertRedirectTo($t->generateUrl('admin_projects'), $resp);
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

        $t->assertRedirectTo($t->generateUrl('admin_projects'), $resp);
    });

    $g->test('Delete project', function ($t) use ($admin, $project) {
        $milestone = createMilestone($project);

        $resp = $t->visit('admin_delete_project', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => $project['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_projects'), $resp);
        $t->assertFalse(Milestone::find($milestone['id']));
    });
});
