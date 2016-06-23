<?php

use Avalon\Testing\TestSuite;

TestSuite::group('Roadmap', function ($g) {
    $project = createProject();
    $milestone = createMilestone($project);

    $g->test('List milestones', function ($t) use ($project, $milestone) {
        $resp = $t->visit('roadmap', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ]
        ]);

        $t->shouldContain($resp, '<h1 class="page-header">Roadmap</h1>');
        $t->shouldContain($resp, $project['name']);
        $t->shouldContain($resp, $milestone['name']);
    });

    $g->test('Show milestone', function ($t) use ($project, $milestone) {
        $resp = $t->visit('milestone', [
            'routeTokens' => [
                'pslug' => $project['slug'],
                'slug' => $milestone['slug']
            ]
        ]);

        $t->shouldContain($resp, $project['name']);
        $t->shouldContain($resp, $milestone['name']);
    });
});
