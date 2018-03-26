<?php

namespace Tests\Requests\Admin;

use Avalon\Http\RedirectResponse;
use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Milestone;
use Traq\Models\Project;
use Traq\Models\User;

class ProjectsTest extends TestCase
{
    /**
     * @var User
     */
    protected static $admin;

    /**
     * @var Project
     */
    public static $project;

    /**
     * @beforeClass
     */
    public static function setUpAdmin()
    {
        static::$admin = createAdmin();
        static::$project = createProject();
    }

    public function testListProjects()
    {
        $resp = $this->visit('admin_projects', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains(static::$project['name'], $resp->body);
    }

    public function testNewProjectForm()
    {
        $resp = $this->visit('admin_new_project', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">New Project</h1>', $resp->body);
    }

    public function testCreateProject()
    {
        $resp = $this->visit('admin_create_project', [
            'method' => 'POST',
            'post' => [
                'name' => 'My Project',
                'slug' => 'my-project',
                'info' => 'This is a test project.'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_projects'), $resp->url);
    }

    public function testSlugInUse()
    {
        $resp = $this->visit('admin_create_project', [
            'method' => 'POST',
            'post' => [
                'slug' => 'my-project'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertContains('Slug is already in use', $resp->body);
    }

    public function testEditProjectForm()
    {
        $resp = $this->visit('admin_edit_project', [
            'routeTokens' => [
                'id' => static::$project['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertContains('<h1 class="page-header">Edit Project</h1>', $resp->body);
    }

    public function testUpdateProject()
    {
        $resp = $this->visit('admin_save_project', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$project['id']
            ],
            'post' => [
                'name' => 'Just Another Project'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_projects'), $resp->url);
    }

    public function testSlugRequiredValidation()
    {
        $resp = $this->visit('admin_save_project', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$project['id']
            ],
            'post' => [
                'slug' => ''
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertContains('Slug is required', $resp->body);
    }

    public function testDeleteProject()
    {
        $milestone = createMilestone(static::$project);

        $resp = $this->visit('admin_delete_project', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => static::$project['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_projects'), $resp->url);
        $this->assertFalse(Milestone::find($milestone['id']));
    }
}
