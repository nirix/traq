<?php

namespace Tests\Requests\Admin;

use Avalon\Http\RedirectResponse;
use Avalon\Testing\PhpUnit\TestCase;
use Traq\Models\User;

class PluginsTest extends TestCase
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

    public function testListPlugins()
    {
        $resp = $this->visit('admin_plugins', [
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertEquals(200, $resp->status);
        $this->assertContains('<h1 class="page-header">Plugins</h1>', $resp->body);
    }

    public function testInstallPlugin()
    {
        $resp = $this->visit('admin_plugins_install', [
            'get' => [
                'plugin' => 'traq/markdown-plugin'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_plugins'), $resp->url);
    }

    public function testDisablePlugin()
    {
        $resp = $this->visit('admin_plugins_disable', [
            'get' => [
                'plugin' => 'traq/markdown-plugin'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_plugins'), $resp->url);
    }

    public function testEnablePlugin()
    {
        $resp = $this->visit('admin_plugins_enable', [
            'get' => [
                'plugin' => 'traq/markdown-plugin'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_plugins'), $resp->url);
    }

    public function testUninstallPlugin()
    {
        $resp = $this->visit('admin_plugins_uninstall', [
            'get' => [
                'plugin' => 'traq/markdown-plugin'
            ],
            'cookie' => [
                'traq' => static::$admin['session_hash']
            ]
        ]);

        $this->assertInstanceOf(RedirectResponse::class, $resp);
        $this->assertEquals($this->generateUrl('admin_plugins'), $resp->url);
    }
}
