<?php

use Avalon\Testing\TestSuite;

TestSuite::group('Update ticket', function ($g) {
    $user = createUser();

    $user['group_id'] = 1;
    $user->save();

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

        $t->shouldContain($resp, $originalMilestone['name']);
        $t->shouldNotContain($resp, 'Changed <span class="ticket-history-property">Milestone</span>');

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

        $t->shouldRedirectTo($updateResp, $redirectUrl);

        // Check the milestone changed
        $checkResp = $t->visit('ticket', [
            'routeTokens' => [
                'pslug' => $project['slug'],
                'id' => $ticket['ticket_id']
            ]
        ]);

        $t->shouldContain($checkResp, $project['name']);
        $t->shouldContain($checkResp, 'Changed <span class="ticket-history-property">Milestone</span>');
        $t->shouldContain($checkResp, $milestone['name']);
        $t->shouldContain($checkResp, $originalMilestone['name']);
    });
});
