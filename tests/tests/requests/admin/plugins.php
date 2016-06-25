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
});
