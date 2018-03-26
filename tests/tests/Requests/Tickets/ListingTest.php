<?php

namespace Tests\Requests\Tickets;

use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Milestone;
use Traq\Models\Project;
use Traq\Models\Ticket;

class ListingTest extends TestCase
{
    /**
     * @var Project
     */
    protected static $project;

    /**
     * @var Milestone
     */
    protected static $milestone;

    /**
     * @var Ticket
     */
    protected static $ticketA;

    /**
     * @var Ticket
     */
    protected static $ticketB;

    /**
     * @beforeClass
     */
    public static function setUpTickets()
    {
        static::$project = createProject();
        static::$milestone = createMilestone(static::$project);
        static::$ticketA = createTicket(static::$project, static::$milestone);
        static::$ticketB = createTicket(static::$project, static::$milestone);
    }

    public function testListTickets()
    {
        $resp = $this->visit('tickets', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ]
        ]);

        $this->assertContains('<h1 class="page-header">Tickets</h1>', $resp->body);
        $this->assertContains(static::$project['name'], $resp->body);
        $this->assertContains(static::$ticketA['summary'], $resp->body);
        $this->assertContains(static::$ticketB['summary'], $resp->body);
    }

    public function testShowTicket()
    {
        $resp = $this->visit('ticket', [
            'routeTokens' => [
                'pslug' => static::$project['slug'],
                'id' => static::$ticketA['ticket_id']
            ]
        ]);

        $this->assertContains(static::$project['name'], $resp->body);
        $this->assertContains('<h1 class="page-header">#' . static::$ticketA['ticket_id']. ' - ' . static::$ticketA['summary'] . '</h1>', $resp->body);
        $this->assertContains(static::$ticketA['body'], $resp->body);
    }
}
