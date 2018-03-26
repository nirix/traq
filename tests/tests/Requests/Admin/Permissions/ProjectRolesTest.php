<?php

namespace Tests\Requests\Admin\Permissions;

use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\User;

class ProjectRolesTest extends TestCase
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

    public function testListPermissions()
    {
        $resp = $this->visit('admin_permissions_roles', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
    }
}
