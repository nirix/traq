<?php

namespace Tests\Requests\Admin;

use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\User;

class DashboardTest extends TestCase
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

    public function testDenyGuestAccess()
    {
        $resp = $this->visit('admincp');

        $this->assertEquals(403, $resp->status);
    }

    public function testAllowAdminAccess()
    {
        $resp = $this->visit('admincp', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('class="navbar-brand">AdminCP</a>', $resp->body);
    }
}
