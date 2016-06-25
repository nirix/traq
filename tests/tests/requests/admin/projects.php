<?php

use Traq\Models\Milestone;

$testSuite->createGroup('Requests / Admin / Projects', function ($g) {
    $admin = createAdmin();
    $project = createProject();

    $g->test('List projects', function ($t) use ($admin, $project) {
        $resp = $t->visit('admin_projects', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains($project['name'], $resp->body);
    });

    $g->test('New project form', function ($t) use ($admin) {
        $resp = $t->visit('admin_new_project', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">New Project</h1>', $resp->body);
    });

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

    $g->test('Slug in use', function ($t) use ($admin) {
        $resp = $t->visit('admin_create_project', [
            'method' => 'POST',
            'post' => [
                'slug' => 'my-project'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertContains('Slug is already in use', $resp->body);
    });

    $g->test('Edit project form', function ($t) use ($admin, $project) {
        $resp = $t->visit('admin_edit_project', [
            'routeTokens' => [
                'id' => $project['id']
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertContains('<h1 class="page-header">Edit Project</h1>', $resp->body);
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

    $g->test('Slug is required', function ($t) use ($admin, $project) {
        $resp = $t->visit('admin_save_project', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => $project['id']
            ],
            'post' => [
                'slug' => ''
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertContains('Slug is required', $resp->body);
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
