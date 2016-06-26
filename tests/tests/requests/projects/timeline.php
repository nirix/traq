<?php

use Traq\Models\Timeline;

$testSuite->createGroup('Requests / Projects / Timeline', function ($g) {
    $project = createProject();
    $ticket = $project->tickets()->fetch() ?: createTicket($project);
    $wikiPage = createWikiPage($project);

    $g->test('Empty timeline', function ($t) use ($project, $ticket, $wikiPage) {
        $resp = $t->visit('timeline', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ]
        ]);

        $t->assertContains($project['name'], $resp->body);
        $t->assertContains('<h1 class="page-header">Timeline</h1>', $resp->body);
        $t->assertNotContains($ticket['summary'], $resp->body);
    });

    $g->test('With events', function ($t) use ($project, $ticket, $wikiPage) {
        $newTicketEvent = Timeline::newTicketEvent($ticket->user(), $ticket);
        $newTicketEvent->save();

        $updatedTicketEvent = Timeline::updateTicketEvent($ticket->user(), $ticket, 'ticket_updated', $ticket->status()['name']);
        $updatedTicketEvent->save();

        $closedTicketEvent = Timeline::updateTicketEvent($ticket->user(), $ticket, 'ticket_closed', $ticket->status()['name']);
        $closedTicketEvent->save();

        $completedMilestoneEvent = Timeline::milestoneCompletedEvent($ticket->user(), $ticket->milestone());
        $completedMilestoneEvent->save();

        $wikiPageCreatedEvent = Timeline::wikiPageCreatedEvent($ticket->user(), $wikiPage);
        $wikiPageCreatedEvent->save();

        $resp = $t->visit('timeline', [
            'routeTokens' => [
                'pslug' => $project['slug']
            ]
        ]);

        $t->assertContains($project['name'], $resp->body);
        $t->assertContains('<h1 class="page-header">Timeline</h1>', $resp->body);
        $t->assertContains($ticket['summary'], $resp->body);
        $t->assertContains($wikiPage['title'], $resp->body);
    });
});
