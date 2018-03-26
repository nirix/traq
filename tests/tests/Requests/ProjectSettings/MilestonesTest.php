<?php

namespace Tests\Requests\ProjectSettings;

use Avalon\Http\RedirectResponse;
use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Milestone;
use Traq\Models\Project;
use Traq\Models\ProjectRole;
use Traq\Models\User;

class MilestonesTest extends TestCase
{
    /**
     * @var ProjectRole
     */
    protected static $manager;

    /**
     * @var Project
     */
    protected static $project;

    /**
     * @var User
     */
    protected static $user;

    /**
     * @var Milestone
     */
    protected static $milestone;

    /**
     * @beforeClass
     */
    public static function setUpProject()
    {
        static::$manager = createProjectManager();
        static::$project = static::$manager->project();
        static::$user = static::$manager->user();
        static::$milestone = createMilestone(static::$project);
    }

    public function testListMilestones()
    {
        $resp = $this->visit('project_settings_milestones', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">Milestones</h1>', $resp->body);
    }

    public function testNewMilestone()
    {
        $resp = $this->visit('project_settings_new_milestone', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">New Milestone</h1>', $resp->body);
    }

    public function testCreateMilestone()
    {
        $resp = $this->visit('project_settings_create_milestone', [
            'method' => 'POST',
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ],
            'post' => [
                'name' => '1.0-test-name',
                'slug' => '1.0-test-slug'
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('project_settings_milestones'), $resp->url);
    }

    public function testDisallowEditingOtherProjectsMilestone()
    {
        $milestone = createMilestone();

        $resp = $this->visit('project_settings_edit_milestone', [
            'routeTokens' => [
                'pslug' => static::$project['slug'],
                'id' => $milestone['id']
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertEquals(404, $resp->status);
    }

    public function testEditMilestone()
    {
        $resp = $this->visit('project_settings_edit_milestone', [
            'routeTokens' => [
                'pslug' => static::$project['slug'],
                'id' => static::$milestone['id']
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">Edit Milestone</h1>', $resp->body);
        $this->assertContains(static::$milestone['name'], $resp->body);
    }

    public function testSaveMilestone()
    {
        $resp = $this->visit('project_settings_save_milestone', [
            'method' => 'PATCH',
            'routeTokens' => [
                'pslug' => static::$project['slug'],
                'id' => static::$milestone['id'],
            ],
            'post' => [
                'name' => 'My Updated Milestone'
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('project_settings_milestones'), $resp->url);
    }
}
