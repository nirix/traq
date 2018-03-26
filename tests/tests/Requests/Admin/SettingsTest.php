<?php

namespace Tests\Requests\Admin;

use Avalon\Http\RedirectResponse;
use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\Setting;
use Traq\Models\User;

class SettingsTest extends TestCase
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

    public function testSettingsPage()
    {
        $resp = $this->visit('admin_settings', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">Settings</h1>', $resp->body);
    }

    public function testSaveSettings()
    {
        $resp = $this->visit('admin_settings_save', [
            'method' => 'POST',
            'post' => [
                'settings' => [
                    'title' => 'New Title'
                ]
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_settings'), $resp->url);
        $this->assertEquals('New Title', Setting::find('setting', 'title')['value']);
    }
}
