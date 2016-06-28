<?php

$testSuite->createGroup('Requests / Project Settings / Milestones', function ($g) {
    $manager = createProjectManager();
    $project = $manager->project();
    $user = $manager->user();
    $milestone = createMilestone($project);

    $g->test('List milestones', function ($t) use ($project, $user) {
        $resp = $t->visit('project_settings_milestones', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">Milestones</h1>', $resp->body);
    });

    $g->test('New milestone', function ($t) use ($project, $user) {
        $resp = $t->visit('project_settings_new_milestone', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">New Milestone</h1>', $resp->body);
    });

    $g->test('Create milestone', function ($t) use ($project, $user) {
        $resp = $t->visit('project_settings_create_milestone', [
            'method' => 'POST',
            'routeTokens' => [
                'pslug' => $project['slug']
            ],
            'post' => [
                'name' => '1.0-test-name',
                'slug' => '1.0-test-slug'
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('project_settings_milestones'), $resp);
    });

    $g->test('Disallow editing another projects milestone', function ($t) use ($project, $user) {
        $milestone = createMilestone();

        $resp = $t->visit('project_settings_edit_milestone', [
            'routeTokens' => [
                'pslug' => $project['slug'],
                'id' => $milestone['id']
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertEquals(404, $resp->status);
    });

    $g->test('Edit milestone', function ($t) use ($project, $user, $milestone) {
        $resp = $t->visit('project_settings_edit_milestone', [
            'routeTokens' => [
                'pslug' => $project['slug'],
                'id' => $milestone['id']
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">Edit Milestone</h1>', $resp->body);
        $t->assertContains($milestone['name'], $resp->body);
    });
});
