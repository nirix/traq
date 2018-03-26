<?php

namespace Tests\Requests\ProjectSettings;

use Avalon\Http\RedirectResponse;
use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Component;
use Traq\Models\Project;
use Traq\Models\ProjectRole;
use Traq\Models\User;

class ComponentsTest extends TestCase
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
     * @var Component
     */
    protected static $component;

    /**
     * @beforeClass
     */
    public static function setUpProject()
    {
        static::$manager = createProjectManager();
        static::$project = static::$manager->project();
        static::$user = static::$manager->user();
        static::$component = createComponent(static::$project);
    }

    public function testListComponents()
    {
        $resp = $this->visit('project_settings_components', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">Components</h1>', $resp->body);
    }

    public function testNewComponent()
    {
        $resp = $this->visit('project_settings_new_component', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">New Component</h1>', $resp->body);
    }

    public function testCreateComponent()
    {
        $resp = $this->visit('project_settings_create_component', [
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
        $this->assertEquals($this->generateUrl('project_settings_components'), $resp->url);
    }

    public function testDisallowEditingOtherProjectsComponents()
    {
        $component = createComponent();

        $resp = $this->visit('project_settings_edit_component', [
            'routeTokens' => [
                'pslug' => static::$project['slug'],
                'id' => $component['id']
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertEquals(404, $resp->status);
    }

    public function testEditComponent()
    {
        $resp = $this->visit('project_settings_edit_component', [
            'routeTokens' => [
                'pslug' => static::$project['slug'],
                'id' => static::$component['id']
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">Edit Component</h1>', $resp->body);
        $this->assertContains(static::$component['name'], $resp->body);
    }

    public function testSaveComponent()
    {
        $resp = $this->visit('project_settings_save_component', [
            'method' => 'PATCH',
            'routeTokens' => [
                'pslug' => static::$project['slug'],
                'id' => static::$component['id']
            ],
            'post' => [
                'name' => 'My Updated Component'
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('project_settings_components'), $resp->url);
    }
}
