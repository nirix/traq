<?php

$testSuite->createGroup('Requests / Projects / Listing', function ($g) {
    $project = createProject();
    $ticket = createTicket($project);

    $ticket['status_id'] = 5;
    $ticket->save();

    $ticket->milestone()->status = 2;
    $ticket->milestone()->save();

    $g->test('List projects', function ($t) use ($project) {
        $resp = $t->visit('projects');

        $t->assertContains('<h1 class="page-header">Projects</h1>', $resp->body);
        $t->assertContains($project['name'], $resp->body);
    });

    $g->test('Show project', function ($t) use ($project) {
        $resp = $t->visit('project', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ]
        ]);

        $t->assertContains('<h1 class="page-header">' . $project['name'] . '</h1>', $resp->body);
    });

    $g->test('Show changelog', function ($t) use ($project) {
        $resp = $t->visit('changelog', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ]
        ]);

        $t->assertContains('<h1 class="page-header">Changelog</h1>', $resp->body);
    });
});
