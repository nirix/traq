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

        $t->assertContains('<h1 class="page-header">Tickets</h1>', $resp->body);
        $t->assertContains($project['name'], $resp->body);
        $t->assertContains($ticketA['summary'], $resp->body);
        $t->assertContains($ticketB['summary'], $resp->body);
    });

    $g->test('Show ticket', function ($t) use ($project, $ticketA) {
        $resp = $t->visit('ticket', [
            'routeTokens' => [
                'pslug' => $project['slug'],
                'id' => $ticketA['ticket_id']
            ]
        ]);

        $t->assertContains($project['name'], $resp->body);
        $t->assertContains('<h1 class="page-header">#' . $ticketA['ticket_id']. ' - ' . $ticketA['summary'] . '</h1>', $resp->body);
        $t->assertContains($ticketA['body'], $resp->body);
    });
});
