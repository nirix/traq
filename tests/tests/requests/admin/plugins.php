<?php

$testSuite->createGroup('Requests / Admin / Plugins', function ($g) {
    $admin = createAdmin();

    $g->test('List plugins', function ($t) use ($admin) {
        $resp = $t->visit('admin_plugins', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">Plugins</h1>', $resp->body);
    });

    $g->test('Install plugin', function ($t) use ($admin) {
        $resp = $t->visit('admin_plugins_install', [
            'get' => [
                'plugin' => 'traq/markdown-plugin'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_plugins'), $resp);
    });

    $g->test('Disable plugin', function ($t) use ($admin) {
        $resp = $t->visit('admin_plugins_disable', [
            'get' => [
                'plugin' => 'traq/markdown-plugin'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_plugins'), $resp);
    });

    $g->test('Enable plugin', function ($t) use ($admin) {
        $resp = $t->visit('admin_plugins_enable', [
            'get' => [
                'plugin' => 'traq/markdown-plugin'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_plugins'), $resp);
    });

    $g->test('Uninstall plugin', function ($t) use ($admin) {
        $resp = $t->visit('admin_plugins_uninstall', [
            'get' => [
                'plugin' => 'traq/markdown-plugin'
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_plugins'), $resp);
    });
});
