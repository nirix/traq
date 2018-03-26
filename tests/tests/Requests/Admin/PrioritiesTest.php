<?php

namespace Tests\Requests\Admin;

use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Priority;
use Traq\Models\User;

class PrioritiesTest extends TestCase
{
    /**
     * @var User
     */
    protected static $admin;

    /**
     * @var Priority
     */
    protected static $priority;

    /**
     * @beforeClass
     */
    public static function setUpAdmin()
    {
        static::$admin = createAdmin();
        static::$priority = createPriority();
    }

    public function testListPriorities()
    {
        $resp = $this->visit('admin_priorities', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains(static::$priority['name'], $resp->body);
    }

    public function testNewPriorityForm()
    {
        $resp = $this->visit('admin_new_priority', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">New Priority</h1>', $resp->body);
    }

    public function testCreatePriority()
    {
        $resp = $this->visit('admin_create_priority', [
            'method' => 'POST',
            'post' => [
                'name' => 'My Priority'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals($this->generateUrl('admin_priorities'), $resp->url);
    }

    public function testNameInUse()
    {
        $resp = $this->visit('admin_create_priority', [
            'method' => 'POST',
            'post' => [
                'name' => 'High'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('Name is already in use', $resp->body);
    }

    public function testEditPriorityForm()
    {
        $resp = $this->visit('admin_edit_priority', [
            'routeTokens' => [
                'id' => static::$priority['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">Edit Priority</h1>', $resp->body);
    }

    public function testUpdatePriority()
    {
        $resp = $this->visit('admin_save_priority', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$priority['id']
            ],
            'post' => [
                'name' => 'Just Another Priority'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals($this->generateUrl('admin_priorities'), $resp->url);
    }

    public function testNameRequiredValidation()
    {
        $resp = $this->visit('admin_save_priority', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$priority['id']
            ],
            'post' => [
                'name' => ''
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('Name is required', $resp->body);
    }

    public function testDeletePriority()
    {
        $resp = $this->visit('admin_delete_priority', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => static::$priority['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals($this->generateUrl('admin_priorities'), $resp->url);
    }
}
