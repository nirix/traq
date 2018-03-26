<?php

namespace Tests\Requests\Projects;

use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Milestone;
use Traq\Models\Project;

class RoadmapTest extends TestCase
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
     * @beforeClass
     */
    public static function setUpProject()
    {
        static::$project = createProject();
        static::$milestone = createMilestone(static::$project);
    }

    public function testListMilestones()
    {
        $resp = $this->visit('roadmap', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ]
        ]);

        $this->assertContains('<h1 class="page-header">Roadmap</h1>', $resp->body);
        $this->assertContains(static::$milestone['name'], $resp->body);
    }

    public function testShowMilestone()
    {
        $resp = $this->visit('milestone', [
            'routeTokens' => [
                'pslug' => static::$project['slug'],
                'slug'  => static::$milestone['slug']
            ]
        ]);

        $this->assertContains(static::$milestone['name'], $resp->body);
    }
}
