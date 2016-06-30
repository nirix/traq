<?php

$testSuite->createGroup('Requests / Project Settings / Components', function ($g) {
    $manager = createProjectManager();
    $project = $manager->project();
    $user = $manager->user();
    $component = createComponent($project);

    $g->test('List components', function ($t) use ($project, $user) {
        $resp = $t->visit('project_settings_components', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">Components</h1>', $resp->body);
    });

    $g->test('New component', function ($t) use ($project, $user) {
        $resp = $t->visit('project_settings_new_component', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">New Component</h1>', $resp->body);
    });

    $g->test('Create component', function ($t) use ($project, $user) {
        $resp = $t->visit('project_settings_create_component', [
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

        $t->assertRedirectTo($t->generateUrl('project_settings_components'), $resp);
    });

    $g->test('Disallow editing another projects component', function ($t) use ($project, $user) {
        $component = createComponent();

        $resp = $t->visit('project_settings_edit_component', [
            'routeTokens' => [
                'pslug' => $project['slug'],
                'id' => $component['id']
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertEquals(404, $resp->status);
    });

    $g->test('Edit component', function ($t) use ($project, $user, $component) {
        $resp = $t->visit('project_settings_edit_component', [
            'routeTokens' => [
                'pslug' => $project['slug'],
                'id' => $component['id']
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">Edit Component</h1>', $resp->body);
        $t->assertContains($component['name'], $resp->body);
    });
});
