<?php

use Avalon\Testing\TestSuite;

TestSuite::group('Ticket listing', function ($g) {
    $project = createProject();
    $milestone = createMilestone($project);
    $ticketA = createTicket($project, $milestone);
    $ticketB = createTicket($project, $milestone);

    $g->test('List tickets', function ($t) use ($project, $milestone, $ticketA, $ticketB) {
        $resp = $t->visit('tickets', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ]
        ]);

        $t->shouldContain($resp, '<h1 class="page-header">Tickets</h1>');
        $t->shouldContain($resp, $project['name']);
        $t->shouldContain($resp, $ticketA['summary']);
        $t->shouldContain($resp, $ticketB['summary']);
    });
});
