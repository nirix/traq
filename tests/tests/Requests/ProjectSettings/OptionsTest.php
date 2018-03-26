<?php

namespace Tests\Requests\ProjectSettings;

use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Project;
use Traq\Models\ProjectRole;
use Traq\Models\User;

class OptionsTest extends TestCase
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
     * @beforeClass
     */
    public static function setUpProject()
    {
        static::$manager = createProjectManager();
        static::$project = static::$manager->project();
        static::$user = static::$manager->user();
    }

    public function testDenyGuests()
    {
        $resp = $this->visit('project_settings', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ]
        ]);

        $this->assertEquals(403, $resp->status);
    }

    public function testDisallowOtherProjectManager()
    {
        $manager = createProjectManager();
        $user = $manager->user();

        $resp = $this->visit('project_settings', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ],
            'cookie' => [
                'traq' => $user['session_hash']
            ]
        ]);

        $this->assertEquals(403, $resp->status);
    }

    public function testAllowAccess()
    {
        $resp = $this->visit('project_settings', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">Settings</h1>', $resp->body);
    }
}
