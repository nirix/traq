<?php

$testSuite->createGroup('Requests / Project Settings', function ($g) {
    $manager = createProjectManager();
    $project = $manager->project();
    $user = $manager->user();

    $g->test('Deny access to guests', function ($t) use ($project) {
        $resp = $t->visit('project_settings', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ]
        ]);

        $t->assertEquals(403, $resp->status);
    });

    $g->test('Disallow other project manager', function ($t) use ($project) {
        $manager = createProjectManager();
        $user = $manager->user();

        $resp = $t->visit('project_settings', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertEquals(403, $resp->status);
    });

    $g->test('Allow access', function ($t) use ($project, $user) {
        $resp = $t->visit('project_settings', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">Settings</h1>', $resp->body);
    });
});
