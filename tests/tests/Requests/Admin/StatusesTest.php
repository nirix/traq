<?php

namespace Tests\Requests\Admin;

use Avalon\Http\RedirectResponse;
use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Status;
use Traq\Models\User;

class StatusesTest extends TestCase
{
    /**
     * @var User
     */
    protected static $admin;

    /**
     * @var Status
     */
    protected static $status;

    /**
     * @beforeClass
     */
    public static function setUpAdmin()
    {
        static::$admin = createAdmin();
        static::$status = createStatus();
    }

    public function testListStatuses()
    {
        $resp = $this->visit('admin_statuses', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains(static::$status['name'], $resp->body);
    }

    public function testNewStatusForm()
    {
        $resp = $this->visit('admin_new_status', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">New Status</h1>', $resp->body);
    }

    public function testCreateStatus()
    {
        $resp = $this->visit('admin_create_status', [
            'method' => 'POST',
            'post' => [
                'name' => 'My Status',
                'level' => 5
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_statuses'), $resp->url);
    }

    public function testNameInUse()
    {
        $resp = $this->visit('admin_create_status', [
            'method' => 'POST',
            'post' => [
                'name' => 'New'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertContains('Name is already in use', $resp->body);
    }

    public function testEditStatusForm()
    {
        $resp = $this->visit('admin_edit_status', [
            'routeTokens' => [
                'id' => static::$status['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertContains('<h1 class="page-header">Edit Status</h1>', $resp->body);
    }

    public function testUpdateStatus()
    {
        $resp = $this->visit('admin_save_status', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$status['id']
            ],
            'post' => [
                'name' => 'Just Another Status'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_statuses'), $resp->url);
    }

    public function testNameRequiredValidation()
    {
        $resp = $this->visit('admin_save_status', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$status['id']
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

    public function testDeleteStatus()
    {
        $resp = $this->visit('admin_delete_status', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => static::$status['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_statuses'), $resp->url);
    }
}
