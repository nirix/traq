<?php

$testSuite->createGroup('Requests / Projects / Roadmap', function ($g) {
    $project = createProject();
    $milestone = createMilestone($project);

    $g->test('List milestones', function ($t) use ($project, $milestone) {
        $resp = $t->visit('roadmap', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ]
        ]);

        $t->assertContains('<h1 class="page-header">Roadmap</h1>', $resp->body);
        $t->assertContains($milestone['name'], $resp->body);
    });

    $g->test('Show milestone', function ($t) use ($project, $milestone) {
        $resp = $t->visit('milestone', [
            'routeTokens' => [
                'pslug' => $project['slug'],
                'slug'  => $milestone['slug']
            ]
        ]);

        $t->assertContains($milestone['name'], $resp->body);
    });
});
