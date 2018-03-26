<?php

namespace Tests\Requests\Admin;

use Avalon\Http\RedirectResponse;
use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\ProjectRole;
use Traq\Models\User;

class ProjectRolesTest extends TestCase
{
    /**
     * @var User
     */
    protected static $admin;

    /**
     * @var ProjectRole
     */
    protected static $role;

    /**
     * @beforeClass
     */
    public static function setUpAdmin()
    {
        static::$admin = createAdmin();
        static::$role = createProjectRole();
    }

    public function testListRoles()
    {
        $resp = $this->visit('admin_project_roles', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">Roles</h1>', $resp->body);
    }

    public function testNewProjectRoleForm()
    {
        $resp = $this->visit('admin_new_project_role', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">New Role</h1>', $resp->body);
    }

    public function testCreateProjectRole()
    {
        $resp = $this->visit('admin_create_project_role', [
            'method' => 'POST',
            'post' => [
                'name' => 'Testing Role'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_project_roles'), $resp->url);
    }

    public function testEditProjectRoleForm()
    {
        $resp = $this->visit('admin_edit_project_role', [
            'routeTokens' => [
                'id' => static::$role['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertContains('<h1 class="page-header">Edit Role</h1>', $resp->body);
    }

    public function testUpdateProjectRole()
    {
        $resp = $this->visit('admin_save_project_role', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$role['id']
            ],
            'post' => [
                'name' => 'Just Another Project Role'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_project_roles'), $resp->url);
    }

    public function testNameRequiredValidation()
    {
        $resp = $this->visit('admin_save_project_role', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$role['id']
            ],
            'post' => [
                'name' => ''
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertContains('Name is required', $resp->body);
    }

    public function testDeleteProjectRole()
    {
        $resp = $this->visit('admin_delete_project_role', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => static::$role['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_project_roles'), $resp->url);
    }
}
