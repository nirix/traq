<?php

namespace Tests\Requests\Projects;

use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Project;
use Traq\Models\Ticket;
use Traq\Models\Timeline;
use Traq\Models\WikiPage;

class TimelineTest extends TestCase
{
    /**
     * @var Project
     */
    public static $project;

    /**
     * @var Ticket
     */
    public static $ticket;

    /**
     * @var WikiPage
     */
    public static $wikiPage;

    /**
     * @beforeClass
     */
    public static function setUpTimeline()
    {
        static::$project = createProject();
        static::$ticket = createTicket(static::$project);
        static::$wikiPage = createWikiPage(static::$project);
    }

    public function testEmptyTimeline()
    {
        $resp = $this->visit('timeline', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ]
        ]);

        $this->assertContains(static::$project['name'], $resp->body);
        $this->assertContains('<h1 class="page-header">Timeline</h1>', $resp->body);
        $this->assertNotContains(static::$ticket['summary'], $resp->body);
    }

    public function testTimelineWithEvents()
    {
        $newTicketEvent = Timeline::newTicketEvent(static::$ticket->user(), static::$ticket);
        $newTicketEvent->save();

        $updatedTicketEvent = Timeline::updateTicketEvent(
            static::$ticket->user(),
            static::$ticket,
            'ticket_updated',
            static::$ticket->status()['name']
        );
        $updatedTicketEvent->save();

        $closedTicketEvent = Timeline::updateTicketEvent(
            static::$ticket->user(),
            static::$ticket,
            'ticket_closed',
            static::$ticket->status()['name']
        );
        $closedTicketEvent->save();

        $completedMilestoneEvent = Timeline::milestoneCompletedEvent(
            static::$ticket->user(),
            static::$ticket->milestone()
        );
        $completedMilestoneEvent->save();

        $wikiPageCreatedEvent = Timeline::wikiPageCreatedEvent(
            static::$ticket->user(),
            static::$wikiPage
        );
        $wikiPageCreatedEvent->save();

        $resp = $this->visit('timeline', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ]
        ]);

        $this->assertContains(static::$project['name'], $resp->body);
        $this->assertContains('<h1 class="page-header">Timeline</h1>', $resp->body);
        $this->assertContains(static::$ticket['summary'], $resp->body);
        $this->assertContains(static::$wikiPage['title'], $resp->body);
    }
}
