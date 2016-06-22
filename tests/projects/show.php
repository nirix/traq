<?php

use Avalon\Testing\TestSuite;
use Traq\Models\Project;

TestSuite::group('View projects', function ($g) {
    $project = createProject();

    $g->test('Project listing', function ($t) use ($project) {
        $resp = $t->visit('projects');

        $t->shouldContain($resp, '<h1 class="page-header">Projects</h1>');
        $t->shouldContain($resp, $project['name']);
    });

    $g->test('Show project', function ($t) use ($project) {
        $resp = $t->visit('project', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ]
        ]);

        $t->shouldContain($resp, '<h1 class="page-header">' . $project['name'] . '</h1>');
    });
});
