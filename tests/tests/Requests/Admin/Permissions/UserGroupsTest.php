<?php

namespace Tests\Requests\Admin\Permissions;

use Avalon\Http\RedirectResponse;
use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\User;
use Traq\Permissions;

class UserGroupsTest extends TestCase
{
    /**
     * @var User
     */
    protected static $admin;

    /**
     * @beforeClass
     */
    public static function setUpAdmin()
    {
        static::$admin = createAdmin();
    }

    public function tetListPermissions()
    {
        $resp = $this->visit('admin_permissions', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
    }

    public function testSavePermissions()
    {
        $defaults = Permissions::getDefaults();

        $resp = $this->visit('admin_permissions_usergroups_save', [
            'method' => 'POST',
            'post' => [
                'permissions' => [
                    '2' => [
                        'ticket_properties_complete_tasks' => 1
                    ]
                ]
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_permissions'), $resp->url);
    }
}
