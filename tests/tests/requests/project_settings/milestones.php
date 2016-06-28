<?php

$testSuite->createGroup('Requests / Project Settings / Milestones', function ($g) {
    $manager = createProjectManager();
    $project = $manager->project();
    $user = $manager->user();

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
});
