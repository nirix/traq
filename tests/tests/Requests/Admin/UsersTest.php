<?php

namespace Tests\Requests\Admin;

use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\User;

class UsersTest extends TestCase
{
    /**
     * @var User
     */
    protected static $admin;

    /**
     * @var User
     */
    protected static $user;

    /**
     * @beforeClass
     */
    public static function setUpAdmin()
    {
        static::$admin = createAdmin();
        static::$user = createUser();
    }

    public function testListUsers()
    {
        $resp = $this->visit('admin_users', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains(static::$user['name'], $resp->body);
    }

    public function testNewUserForm()
    {
        $resp = $this->visit('admin_new_user', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">New User</h1>', $resp->body);
    }

    public function testCreateUser()
    {
        $resp = $this->visit('admin_create_user', [
            'method' => 'POST',
            'post' => [
                'name'     => 'My User',
                'username' => 'my_user',
                'password'  => 'testing1234',
                'email'    => 'testing1234@example.com',
                'group_id' => 2
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertRedirectTo($this->generateUrl('admin_users'), $resp);
    }

    public function testNameInUse()
    {
        $resp = $this->visit('admin_create_user', [
            'method' => 'POST',
            'post' => [
                'username' => 'Anonymous'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('Username is already in use', $resp->body);
    }

    public function testEditUserForm()
    {
        $resp = $this->visit('admin_edit_user', [
            'routeTokens' => [
                'id' => static::$user['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">Edit User</h1>', $resp->body);
    }

    public function testUpdateUser()
    {
        $resp = $this->visit('admin_save_user', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$user['id']
            ],
            'post' => [
                'name' => 'Just Another User',
                'password' => '1234testing'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertRedirectTo($this->generateUrl('admin_users'), $resp);
    }

    public function testNameRequiredValidation()
    {
        $resp = $this->visit('admin_save_user', [
            'method' => 'PATCH',
            'routeTokens' => [
                'id' => static::$user['id']
            ],
            'post' => [
                'username' => ''
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('Username is required', $resp->body);
    }

    public function testDeleteUser()
    {
        $resp = $this->visit('admin_delete_user', [
            'method' => 'DELETE',
            'routeTokens' => [
                'id' => static::$user['id']
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertRedirectTo($this->generateUrl('admin_users'), $resp);
    }
}
