<?php

use Traq\Models\Setting;

$testSuite->createGroup('Requests / Admin / Settings', function ($g) {
    $admin = createAdmin();

    $g->test('Traq Settings', function ($t) use ($admin) {
        $resp = $t->visit('admin_settings', [
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertEquals(200, $resp->status);
        $t->assertContains('<h1 class="page-header">Settings</h1>', $resp->body);
    });

    $g->test('Save settings', function ($t) use ($admin) {
        $resp = $t->visit('admin_settings_save', [
            'method' => 'POST',
            'post' => [
                'settings' => [
                    'title' => 'New Title'
                ]
            ],
            'cookie' => [
                'traq' => $admin['session_hash']
            ]
        ]);

        $t->assertRedirectTo($t->generateUrl('admin_settings'), $resp);
        $t->assertEquals('New Title', Setting::find('setting', 'title')['value']);
    });
});
