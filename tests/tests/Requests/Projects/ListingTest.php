<?php

namespace Tests\Requests\Projects;

use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Project;
use Traq\Models\Ticket;

class ListingTest extends TestCase
{
    /**
     * @var Project
     */
    protected static $project;

    /**
     * @var Ticket
     */
    protected static $ticket;

    /**
     * @beforeClass
     */
    public static function setUpProject()
    {
        static::$project = createProject();
        static::$ticket = createTicket(static::$project);

        static::$ticket['status_id'] = 5;
        static::$ticket->save();

        static::$ticket->milestone()->status = 2;
        static::$ticket->milestone()->save();
    }

    public function testListProjects()
    {
        $resp = $this->visit('projects');

        $this->assertContains('<h1 class="page-header">Projects</h1>', $resp->body);
        $this->assertContains(static::$project['name'], $resp->body);
    }

    public function testShowProject()
    {
        $resp = $this->visit('project', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ]
        ]);

        $this->assertContains('<h1 class="page-header">' . static::$project['name'] . '</h1>', $resp->body);
    }

    public function testShowChangelog()
    {
        $resp = $this->visit('changelog', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ]
        ]);

        $this->assertContains('<h1 class="page-header">Changelog</h1>', $resp->body);
    }
}
