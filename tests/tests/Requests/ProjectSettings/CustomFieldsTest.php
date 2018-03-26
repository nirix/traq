<?php

namespace Tests\Requests\ProjectSettings;

use Avalon\Http\RedirectResponse;
use Traq\Models\CustomField;
use Traq\Models\Project;
use Traq\Models\ProjectRole;
use Traq\Models\User;

class CustomFieldsTest extends \Avalon\Testing\PhpUnit\TestCase
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
     * @var CustomField
     */
    protected static $customField;

    /**
     * @beforeClass
     */
    public static function setUpProject()
    {
        static::$manager = createProjectManager();
        static::$project = static::$manager->project();
        static::$user = static::$manager->user();

        static::$customField = createCustomField(static::$project);
    }

    public function testListCustomFields()
    {
        $resp = $this->visit('project_settings_custom_fields', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">Custom Fields</h1>', $resp->body);
    }

    public function testNewCustomField()
    {
        $resp = $this->visit('project_settings_new_custom_field', [
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">New Custom Field</h1>', $resp->body);
    }

    public function testCreateTextField()
    {
        $resp = $this->visit('project_settings_create_custom_field', [
            'method' => 'POST',
            'routeTokens' => [
                'pslug' => static::$project['slug']
            ],
            'post' => [
                'name' => 'Text field',
                'slug' => 'text-field',
                'type' => 'text'
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('project_settings_custom_fields'), $resp->url);
    }

    public function testEditCustomField()
    {
        $resp = $this->visit('project_settings_edit_custom_field', [
            'routeTokens' => [
                'pslug' => static::$project['slug'],
                'id' => static::$customField['id']
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">Edit Custom Field</h1>', $resp->body);
    }

    public function testSaveCustomField()
    {
        $resp = $this->visit('project_settings_save_custom_field', [
            'routeTokens' => [
                'pslug' => static::$project['slug'],
                'id' => static::$customField['id']
            ],
            'method' => 'PUT',
            'post' => [
                'type' => 2
            ],
            'cookie' => [
                'traq' => static::$user['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('project_settings_custom_fields'), $resp->url);
    }
}
