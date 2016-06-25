<?php

$testSuite->createGroup('Requests / Tickets / Listing', function ($g) {
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

        $t->assertContains('<h1 class="page-header">Tickets</h1>', $resp);
        $t->assertContains($project['name'], $resp);
        $t->assertContains($ticketA['summary'], $resp);
        $t->assertContains($ticketB['summary'], $resp);
    });

    $g->test('Show ticket', function ($t) use ($project, $ticketA) {
        $resp = $t->visit('ticket', [
            'routeTokens' => [
                'pslug' => $project['slug'],
                'id' => $ticketA['ticket_id']
            ]
        ]);

        $t->assertContains($project['name'], $resp);
        $t->assertContains('<h1 class="page-header">#' . $ticketA['ticket_id']. ' - ' . $ticketA['summary'] . '</h1>', $resp);
        $t->assertContains($ticketA['body'], $resp);
    });
});
