<?php

namespace Tests\Requests\Tickets;

use Avalon\Testing\PhpUnit\TestCase;

class NewTest extends TestCase
{
    protected static $admin;
    protected static $project;

    /**
     * @beforeClass
     */
    public static function setUpProject()
    {
        static::$admin = createAdmin();
        static::$project = createProject();
    }

    public function testNewTicketForm()
    {
        $resp = $this->visit('new_ticket', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">New Ticket</h1>', $resp->body);
    }
}
