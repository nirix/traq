<?php

$testSuite->createGroup('Requests / Project Settings / Custom Fields', function ($g) {
    $manager = createProjectManager();
    $project = $manager->project();
    $user = $manager->user();

    $customField = createCustomField($project);

    $g->test('List custom fields', function ($t) use ($project, $user) {
        $resp = $t->visit('project_settings_custom_fields', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">Custom Fields</h1>', $resp->body);
    });

    $g->test('New custom field', function ($t) use ($project, $user) {
        $resp = $t->visit('project_settings_new_custom_field', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">New Custom Field</h1>', $resp->body);
    });

    $g->test('Create text field', function ($t) use ($project, $user) {
        $resp = $t->visit('project_settings_create_custom_field', [
            'method' => 'POST',
            'routeTokens' => [
                'pslug' => $project['slug']
            ],
            'post' => [
                'name' => 'Text field',
                'slug' => 'text-field',
                'type' => 'text'
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('project_settings_custom_fields'), $resp);
    });

    $g->test('Edit custom field', function ($t) use ($project, $user, $customField) {
        $resp = $t->visit('project_settings_edit_custom_field', [
            'routeTokens' => [
                'pslug' => $project['slug'],
                'id' => $customField['id']
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">Edit Custom Field</h1>', $resp->body);
    });

    $g->test('Save custom field', function ($t) use ($project, $user, $customField) {
        $resp = $t->visit('project_settings_save_custom_field', [
            'routeTokens' => [
                'pslug' => $project['slug'],
                'id' => $customField['id']
            ],
            'method' => 'PUT',
            'post' => [
                'type' => 2
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('project_settings_custom_fields'), $resp);
    });
});
