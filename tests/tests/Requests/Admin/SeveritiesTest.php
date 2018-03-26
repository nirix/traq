<?php

namespace Tests\Requests\Admin;

use Avalon\Http\RedirectResponse;
use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Severity;
use Traq\Models\User;

class SeveritiesTest extends TestCase
{
    /**
     * @var User
     */
    protected static $admin;

    /**
     * @var Severity
     */
    protected static $severity;

    /**
     * @beforeClass
     */
    public static function setUpAdmin()
    {
        static::$admin = createAdmin();
        static::$severity = createSeverity();
    }

    public function testListSeverities()
    {
        $resp = $this->visit('admin_severities', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains(static::$severity['name'], $resp->body);
    }

    public function testNewSeverityForm()
    {
        $resp = $this->visit('admin_new_severity', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">New Severity</h1>', $resp->body);
    }

    public function testCreateSeverity()
    {
        $resp = $this->visit('admin_create_severity', [
            'method' => 'POST',
            'post' => [
                'name' => 'My Severity',
                'level' => 5
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_severities'), $resp->url);
    }

    public function testNameInUse()
    {
        $resp = $this->visit('admin_create_severity', [
            'method' => 'POST',
            'post' => [
                'name' => 'Normal'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertContains('Name is already in use', $resp->body);
    }

    public function testEditSeverityForm()
    {
        $resp = $this->visit('admin_edit_severity', [
            'routeTokens' => [
                'id' => static::$severity['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertContains('<h1 class="page-header">Edit Severity</h1>', $resp->body);
    }

    public function testUpdateSeverity()
    {
        $resp = $this->visit('admin_save_severity', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$severity['id']
            ],
            'post' => [
                'name' => 'Just Another Severity'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_severities'), $resp->url);
    }

    public function testNameRequiredValidation()
    {
        $resp = $this->visit('admin_save_severity', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$severity['id']
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

    public function testDeleteSeverity()
    {
        $resp = $this->visit('admin_delete_severity', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => static::$severity['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_severities'), $resp->url);
    }
}

