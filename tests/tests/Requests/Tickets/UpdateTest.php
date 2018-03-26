<?php

namespace Tests\Requests\Tickets;

use Avalon\Http\RedirectResponse;
use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Milestone;
use Traq\Models\Project;
use Traq\Models\Ticket;
use Traq\Models\User;

class UpdateTest extends TestCase
{
    /**
     * @var User
     */
    protected static $user;

    /**
     * @var Project
     */
    protected static $project;

    /**
     * @var Ticket
     */
    protected static $ticket;

    /**
     * @var Milestone
     */
    protected static $milestone;

    /**
     * @beforeClass
     */
    public static function setUpTicket()
    {
        static::$user = createAdmin();
        static::$project = createProject();
        static::$ticket = createTicket(static::$project);
        static::$milestone = createMilestone(static::$project);
    }

    public function testChangeMilestone()
    {
        $originalMilestone = static::$ticket->milestone();

        // Fetch current ticket
        $resp = $this->visit('ticket', [
            'routeTokens' => [
                'pslug' => static::$project['slug'],
                'id' => static::$ticket['ticket_id']
            ]
        ]);

        $this->assertContains($originalMilestone['name'], $resp->body);
        $this->assertNotContains('Changed <span class="ticket-history-property">Milestone</span>', $resp->body);

        // Send PUT request to update milestone
        $updateResp = $this->visit('update_ticket', [
            'method' => 'PUT',
            'routeTokens' => [
                'pslug' => static::$project['slug'],
                'id' => static::$ticket['ticket_id']
            ],
            'post' => [
                'milestone_id' => static::$milestone['id']
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $redirectUrl = $this->generateUrl('ticket', [
            'pslug' => static::$project['slug'],
            'id' => static::$ticket['ticket_id']
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $updateResp);

        // Check the milestone changed
        $checkResp = $this->visit('ticket', [
            'routeTokens' => [
                'pslug' => static::$project['slug'],
                'id' => static::$ticket['ticket_id']
            ]
        ]);

        $this->assertContains(static::$project['name'], $checkResp->body);
        $this->assertContains('Changed <span class="ticket-history-property">Milestone</span>', $checkResp->body);
        $this->assertContains(static::$milestone['name'], $checkResp->body);
        $this->assertContains($originalMilestone['name'], $checkResp->body);
    }
}
