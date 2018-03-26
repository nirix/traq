<?php

namespace Tests\Requests\Admin;

use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Type;
use Traq\Models\User;

class TypesTest extends TestCase
{
    /**
     * @var User
     */
    protected static $admin;

    /**
     * @var Type
     */
    protected static $type;

    /**
     * @beforeClass
     */
    public static function setUpAdmin()
    {
        static::$admin = createAdmin();
        static::$type = createType();
    }

    public function testListTypes()
    {
        $resp = $this->visit('admin_types', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains(static::$type['name'], $resp->body);
    }

    public function testNewTypeForm()
    {
        $resp = $this->visit('admin_new_type', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">New Type</h1>', $resp->body);
    }

    public function testCreateType()
    {
        $resp = $this->visit('admin_create_type', [
            'method' => 'POST',
            'post' => [
                'name' => 'My Type',
                'bullet' => '#'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertRedirectTo($this->generateUrl('admin_types'), $resp);
    }

    public function testNameInUse()
    {
        $resp = $this->visit('admin_create_type', [
            'method' => 'POST',
            'post' => [
                'name' => 'Defect'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('Name is already in use', $resp->body);
    }

    public function testEditTypeForm()
    {
        $resp = $this->visit('admin_edit_type', [
            'routeTokens' => [
                'id' => static::$type['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">Edit Type</h1>', $resp->body);
    }

    public function testUpdateType()
    {
        $resp = $this->visit('admin_save_type', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$type['id']
            ],
            'post' => [
                'name' => 'Just Another Type'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertRedirectTo($this->generateUrl('admin_types'), $resp);
    }

    public function testNameRequiredValidation()
    {
        $resp = $this->visit('admin_save_type', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$type['id']
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

    public function testDeleteType()
    {
        $resp = $this->visit('admin_delete_type', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => static::$type['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertRedirectTo($this->generateUrl('admin_types'), $resp);
    }
}
