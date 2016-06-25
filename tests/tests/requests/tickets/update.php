<?php

$testSuite->createGroup('Requests / Tickets / Update', function ($g) {
    $user = createAdmin();

    $project = createProject();
    $ticket = createTicket($project);
    $milestone = createMilestone($project);

    $g->test('Change milestone', function ($t) use ($user, $project, $ticket, $milestone) {
        $originalMilestone = $ticket->milestone();

        // Fetch current ticket
        $resp = $t->visit('ticket', [
            'routeTokens' => [
                'pslug' => $project['slug'],
                'id' => $ticket['ticket_id']
            ]
        ]);

        $t->assertContains($originalMilestone['name'], $resp);
        $t->assertNotContains('Changed <span class="ticket-history-property">Milestone</span>', $resp);

        // Send PUT request to update milestone
        $updateResp = $t->visit('update_ticket', [
            'method' => 'PUT',
            'routeTokens' => [
                'pslug' => $project['slug'],
                'id' => $ticket['ticket_id']
            ],
            'post' => [
                'milestone_id' => $milestone['id']
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $redirectUrl = $t->generateUrl('ticket', [
            'pslug' => $project['slug'],
            'id' => $ticket['ticket_id']
        ]);

        $t->assertRedirectTo($redirectUrl, $updateResp);

        // Check the milestone changed
        $checkResp = $t->visit('ticket', [
            'routeTokens' => [
                'pslug' => $project['slug'],
                'id' => $ticket['ticket_id']
            ]
        ]);

        $t->assertContains($project['name'], $checkResp);
        $t->assertContains('Changed <span class="ticket-history-property">Milestone</span>', $checkResp);
        $t->assertContains($milestone['name'], $checkResp);
        $t->assertContains($originalMilestone['name'], $checkResp);
    });
});
