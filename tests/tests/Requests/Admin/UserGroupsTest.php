<?php

namespace Tests\Requests\Admin;

use Avalon\Http\RedirectResponse;
use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Group;
use Traq\Models\User;

class UserGroupsTest extends TestCase
{
    /**
     * @var User
     */
    protected static $admin;

    /**
     * @var Group
     */
    protected static $group;

    /**
     * @beforeClass
     */
    public static function setUpAdmin()
    {
        static::$admin = createAdmin();
        static::$group = createGroup();
    }

    public function testListGroups()
    {
        $resp = $this->visit('admin_groups', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains(static::$group['name'], $resp->body);
    }

    public function testNewGroupForm()
    {
        $resp = $this->visit('admin_new_group', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">New Group</h1>', $resp->body);
    }

    public function testCreateGroup()
    {
        $resp = $this->visit('admin_create_group', [
            'method' => 'POST',
            'post' => [
                'name' => 'My Group'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_groups'), $resp->url);
    }

    public function testNameInUse()
    {
        $resp = $this->visit('admin_create_group', [
            'method' => 'POST',
            'post' => [
                'name' => 'Admin'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertContains('Name is already in use', $resp->body);
    }

    public function testEditGroupForm()
    {
        $resp = $this->visit('admin_edit_group', [
            'routeTokens' => [
                'id' => static::$group['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertContains('<h1 class="page-header">Edit Group</h1>', $resp->body);
    }

    public function testUpdateGroup()
    {
        $resp = $this->visit('admin_save_group', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$group['id']
            ],
            'post' => [
                'name' => 'Just Another Group'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_groups'), $resp->url);
    }

    public function testNameValidation()
    {
        $resp = $this->visit('admin_save_group', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$group['id']
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

    public function testDeleteGroup()
    {
        $resp = $this->visit('admin_delete_group', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => static::$group['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_groups'), $resp->url);
    }
}
